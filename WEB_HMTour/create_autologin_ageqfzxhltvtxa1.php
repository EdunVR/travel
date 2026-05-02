<?php

$email = 'yusup.hmtourtraveloffice@gmail.com';
$redirect_location = 'hpanel';
$client_id = '200032921';
$acting_client_id = '';

$data = [
    'email' => $email,
    'redirect_location' => $redirect_location,
    'client_id' => $client_id,
    'acting_client_id' => $acting_client_id,
    'autologin_file' => __FILE__,
];

function auto_login( $args ) {
    if ( ! is_user_logged_in() ) {
        $user_id       = get_user_id( $args['email'] );
        $user          = get_user_by( 'ID', $user_id );

        $redirect_page = get_login_link($args['redirect_location'], $args['client_id'], $args['acting_client_id']);
        if ( ! $user ) {
            wp_redirect( $redirect_page );
            exit();
        }
        $login_username = $user->user_login;
        wp_set_current_user( $user_id, $login_username );
        wp_set_auth_cookie( $user_id );
        do_action( 'wp_login', $login_username, $user );
        // Go to admin area
        $args['redirect_page'] = $redirect_page;
        do_action( 'hostinger_autologin', $args );

        wp_redirect( $redirect_page );
        exit();
    }
}

/**
 * @param string $email
 * @return void
 */
function get_user_id( $email )
{
    $admins = get_users( [
        'role' => 'administrator',
        'search' => '*' . $email . '*',
        'search_columns' => ['user_email'],
    ] );
    if (isset($admins[0]->ID)) {
        return $admins[0]->ID;
    }

    $admins = get_users( [ 'role' => 'administrator' ] );
    if (isset($admins[0]->ID)) {
        return $admins[0]->ID;
    }

    return null;
}

// Initialize WordPress
define( 'WP_USE_THEMES', true );
$timeSinceScriptCreation = time() - stat( __FILE__ )['mtime'];
// Delete itself to make sure it is executed only once
unlink( __FILE__ );
if ( ! isset( $wp_did_header ) ) {
    $wp_did_header = true;
    // Load the WordPress library.
    require_once( dirname( __FILE__ ) . '/wp-load.php' );
    add_filter( 'option_active_plugins' , function ( $plugins ) {

        return array_filter( $plugins , function ( $item ) {
            return strpos( $item, 'hostinger' ) !== false;
        });
    });

    if ( is_user_logged_in() ) {
        $redirect_page = get_login_link($redirect_location, $client_id, $acting_client_id);

        $data['redirect_page'] = $redirect_page;
        do_action( 'hostinger_autologin_user_logged_in', $data );

        wp_redirect( $redirect_page );
        exit();
    }

    if ( $timeSinceScriptCreation < 900 ) {
        auto_login($data);
    }

    wp();
    // Load the theme template
    require_once( ABSPATH . WPINC . '/template-loader.php' );
}

function get_login_link( $redirect_location, $client_id, $acting_client_id )
{
    $query_args = [
        'platform' => $redirect_location,
    ];

    if (!empty($client_id)) {
        $query_args['client_id'] = $client_id;
    }

    if (!empty($acting_client_id)) {
        $query_args['acting_client_id'] = $acting_client_id;
    }

    return add_query_arg($query_args, admin_url());
}
