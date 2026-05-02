<?php if ( $type !== false && $type == 'v3' ):?>
    <div class="js-uap-recaptcha-v3-item"></div>
    <span class="uap-js-register-captcha-key" data-value="<?php echo esc_attr($key);?>"></span>
<?php else :?>
    <div class="g-recaptcha-wrapper" class="<?php echo esc_attr($class);?>">
        <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($key);?>"></div>
    </div>
<?php endif;?>
