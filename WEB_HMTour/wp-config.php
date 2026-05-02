<?php


/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u127727849_hikamimandiri' );

/** Database username */
define( 'DB_USER', 'u127727849_hikamimandiri' );

/** Database password */
define( 'DB_PASSWORD', '@Hikami2025' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'by3:PstUhCbyOQ$P?_hi;E=#Q^S>L~e)AcOJh64hd5 crutM<x/VO0xCy`ik<o+O' );
define( 'SECURE_AUTH_KEY',   '|J8.R6Nc_0jnch|CJUV/? <(c* |{Hd%L%*$1zBLhX?M*;Sw?Q]C}hw@_i>:6aSR' );
define( 'LOGGED_IN_KEY',     'CRn{W_v{@J6sp~Eil[g,6C}KxHrI<&0Bng>Vpzih>_^+7m1o4AA/V{^bys3Y)e4k' );
define( 'NONCE_KEY',         '@W4)#Z<P[cb[usZX;U/1 GE~B=sk%hLX5GA1f<HQR*Es!ly;d(#Xv`2e(Xa*2DXI' );
define( 'AUTH_SALT',         'GmK;*GRfX*cDh~G9Jew}bJCcwiSQ!(~yB.(O#y>9(a#uP%`X!uF3d!P[S2M%>]6M' );
define( 'SECURE_AUTH_SALT',  'Bhe$>qWE<jdz^&$*& F?gqJ^^CQ@)Fg=j@Z?T&P6Ua+_j2e:K5BJjHFN]+PVXS%:' );
define( 'LOGGED_IN_SALT',    'fp!^v{@}3)XS:o^fYRf8-o>Ua8$@Okp-;&{9($Tf~eA`Oi1Bcht`MT&$70ypZI_)' );
define( 'NONCE_SALT',        '<at-z5~k$bW%;J9wleT|od:I=uXj^Hr|Xwp9uKdDvPHa^!H$8c{![_n!x@ TZbc0' );
define( 'WP_CACHE_KEY_SALT', 'S9+}G+ppf1]guu9+Wc=:?vK*lpOmH(J7iF]RL9t jx5&{jOZBT,)w-s ?4bGT#%]' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', 'bdb5ddc9e4412a2da0aa952308061707' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
