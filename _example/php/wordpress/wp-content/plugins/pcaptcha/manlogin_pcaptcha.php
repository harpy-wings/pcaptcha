<?php
/*
Plugin Name: ManLogin.com PCaptcha
Plugin URI: https://wordpress.org/plugins/PCaptcha/
Description: Add a PCaptcha to the login and registration 
Author: Manlogin.com
Version: 1.0.0
Author URI: https://ManLogin.com
Text Domain: PCaptcha
Domain Path: /languages/
*/

if ( !function_exists( 'add_action' ) ) {
    die();
}

class LoginPCaptcha {

    public static function init() {

        add_action( 'admin_menu', array('LoginPCaptcha', 'register_menu_page' ));
        add_action( 'admin_init', array('LoginPCaptcha', 'register_settings' ));
        add_action( 'admin_notices', array('LoginPCaptcha', 'admin_notices' ));

        delete_option('login_pcaptcha_v3_key');
        delete_option('login_pcaptcha_v3_secret');

        if (LoginPCaptcha::valid_key_secret(get_option('login_pcaptcha_key')) &&
            LoginPCaptcha::valid_key_secret(get_option('login_pcaptcha_secret')) ) {
            /* Handle form display logic downstream of this */
            add_action('login_form',array('LoginPCaptcha', 'pcaptcha_form'));
            add_action('register_form',array('LoginPCaptcha', 'pcaptcha_form'), 99);
            add_action('signup_extra_fields',array('LoginPCaptcha', 'pcaptcha_form'), 99);
            add_action('lostpassword_form',array('LoginPCaptcha', 'pcaptcha_form'));

            /* if array is in whitelist, do not hook in captcha for login, registration, or lost password */
            if ( !LoginPCaptcha::ip_in_whitelist() ) {
                add_filter('registration_errors',array('LoginPCaptcha', 'authenticate'), 10, 3);
                add_action('lostpassword_post',array('LoginPCaptcha', 'authenticate'), 10, 1);
                add_filter('authenticate', array('LoginPCaptcha', 'authenticate'), 30, 3);
                add_filter('shake_error_codes', array('LoginPCaptcha', 'add_shake_error_codes') );
                add_action('plugins_loaded', array('LoginPCaptcha', 'action_plugins_loaded'));
                delete_option('login_pcaptcha_notice');
            }
        } else {
            delete_option('login_pcaptcha_working');
            update_option('login_pcaptcha_message_type', 'notice-error');
            update_option('login_pcaptcha_error', "تنظیمات کپچا را از منوی تنظیمات بررسی کنید!");
            add_action('woocommerce_register_post',array('LoginPCaptcha', 'authenticate'));
            add_action('woocommerce_register_form',array('LoginPCaptcha', 'pcaptcha_form'));
        }

    }

    public static function action_plugins_loaded() {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('woocommerce_login_form',array('LoginPCaptcha', 'pcaptcha_form'));
            add_action('woocommerce_lostpassword_form',array('LoginPCaptcha', 'pcaptcha_form'));
            add_action('woocommerce_register_post',array('LoginPCaptcha', 'woo_authenticate'), 10, 3);
            add_action('woocommerce_register_form',array('LoginPCaptcha', 'pcaptcha_form'));
        }
    }

    public static function register_menu_page(){
        add_options_page( 'تنظیمات کپچای من‌لاگین','کپچای من‌لاگین', 'manage_options', plugin_dir_path(  __FILE__ ).'admin.php');
    }

    public static function register_settings() {

        /* user-configurable values */
        add_option('login_pcaptcha_uid', '');
        add_option('login_pcaptcha_key', '');
        add_option('login_pcaptcha_secret', '');
        add_option('login_pcaptcha_whitelist', '');

        /* user-configurable value checking public static functions */
        register_setting( 'login_pcaptcha', 'login_pcaptcha_uid', 'LoginPCaptcha::filter_string' );
        register_setting( 'login_pcaptcha', 'login_pcaptcha_key', 'LoginPCaptcha::filter_string' );
        register_setting( 'login_pcaptcha', 'login_pcaptcha_secret', 'LoginPCaptcha::filter_string' );
        register_setting( 'login_pcaptcha', 'login_pcaptcha_whitelist', 'LoginPCaptcha::filter_whitelist' );

        /* system values to determine if captcha is working and display useful error messages */
        delete_option('login_pcaptcha_working');
        update_option('login_pcaptcha_error', "تنظیمات کپچا را از منوی تنظیمات بررسی کنید!");
        add_option('login_pcaptcha_message_type', 'notice-error');
        if (LoginPCaptcha::valid_key_secret(get_option('login_pcaptcha_key')) &&
           LoginPCaptcha::valid_key_secret(get_option('login_pcaptcha_secret')) ) {
            update_option('login_pcaptcha_working', true);
        } else {
            delete_option('login_pcaptcha_working');
            update_option('login_pcaptcha_message_type', 'notice-error');
            update_option('login_pcaptcha_error', "تنظیمات کپچا را از منوی تنظیمات بررسی کنید!");
        }
    }

    public static function filter_string( $string ) {
        return trim(filter_var($string, FILTER_SANITIZE_STRING)); //must consist of valid string characters
    }

    public static function valid_key_secret( $string ) {
        if (strlen($string) > 40) {
            return true;
        } else {
            return false;
        }
    }

    public static function filter_whitelist( $string ) {
        return preg_replace( '/[ \t]/', '', trim(filter_var($string, FILTER_SANITIZE_STRING)) ); //must consist of valid string characters, remove spaces
    }

    public static function get_ip_address() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    $ip = filter_var($ip, FILTER_VALIDATE_IP);
                    if (!empty($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return false;
    }

    public static function ip_in_whitelist() {

        /* get whitelist and convert to array */
        $whitelist_str = get_option('login_pcaptcha_whitelist');
        if (!empty($whitelist_str)) {
            $whitelist = explode("\r\n", trim($whitelist_str));
        } else {
            $whitelist = array();
        }

        /* get ip address */
        $ip = LoginPCaptcha::get_ip_address();

        if ( !empty($ip) && !empty($whitelist) && in_array($ip, $whitelist) ) {
            return true;
        } else {
            return false;
        }

    }


    public static function pcaptcha_form() {

        if (!LoginPCaptcha::ip_in_whitelist()) {
            echo '<link rel="stylesheet" href="https://manlogin.com/public/css/pcap.style.min.css">';
            echo '<div style="width:250px;height:160px;overflow:hidden;" id="PCaptcha" class="PCaptcha"></div>';
            echo '<script src="https://manlogin.com/captcha/'.get_option('login_pcaptcha_uid').'/'.get_option('login_pcaptcha_key').'?lang=fa"></script>';
        } else {
            update_option('login_pcaptcha_notice', time());
            update_option('login_pcaptcha_message_type', 'notice-info');
            update_option('login_pcaptcha_error', 'Captcha bypassed by whitelist for ip address');
            echo '<p style="color: red; font-weight: bold;">Captcha bypassed by whitelist for ip address</p>';
        }
    }

    public static function woo_authenticate($username, $email, $errors) {
        return LoginPCaptcha::authenticate( $errors, $username );
    }

    public static function authenticate($user_or_email, $username = null, $password = null) {
        if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) !== 'wp-login.php' && //calling context must be wp-login.php
            !isset($_POST['woocommerce-login-nonce']) && !isset($_POST['woocommerce-lost-password-nonce']) && !isset($_POST['woocommerce-register-nonce']) ) { //or a WooCommerce form
            //bypass reCaptcha checking
            update_option('login_pcaptcha_notice', time());
            update_option('login_pcaptcha_message_type', 'notice-error');
            update_option('login_pcaptcha_error', 'کپچای من‌لاگین was bypassed on login page');
            return $user_or_email;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          return $user_or_email;
        }
        if (isset($_POST['pcaptcha'])) {
            $remoteip = LoginPCaptcha::get_ip_address();
            $uid = get_option('login_pcaptcha_uid');
            $secret = get_option('login_pcaptcha_secret');
            $pcaptcha = LoginPCaptcha::filter_string($_POST['pcaptcha']);
            
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $result = file_get_contents("https://manlogin.com/captcha/cheack/v1/$uid/$secret/$pcaptcha", false, stream_context_create($arrContextOptions));
            $g_response = json_decode($result);
            if (is_object($g_response)) {
                if ( $g_response->Code == 200 && $g_response->success ) {
                    update_option('login_pcaptcha_working', true);
                    return $user_or_email; // success, let them in
                } else {
                    return new WP_Error('authentication_failed',$g_response->Message);
                }
            } else {
                delete_option('login_pcaptcha_working');
                update_option('login_pcaptcha_notice',time());
                update_option('login_pcaptcha_google_error', 'error');
                update_option('login_pcaptcha_error', 'کپچای من‌لاگین درست کار نمی‌کند. تنظیمات خود را چک کنید!');
                return $user_or_email; //not a sane response, prevent lockouts
            }
        } else {
            update_option('login_pcaptcha_working', true);
            if (isset($_POST['action']) && $_POST['action'] === 'lostpassword') {
                return new WP_Error('authentication_failed', '<strong>خطا</strong>&nbsp;: تایید من ربات نیستم، انجام نشده است!');
            }
            if (is_wp_error($user_or_email)) {
                $user_or_email->add('pcaptcha', '<strong>خطا</strong>&nbsp;: تایید من ربات نیستم، انجام نشده است!');
                return $user_or_email;
            } else {
                return new WP_Error('authentication_failed', '<strong>خطا</strong>&nbsp;: تایید من ربات نیستم، انجام نشده است!');
            }
        }
    }

    public static function admin_notices() {
        // not working, or notice fired in last 30 seconds
        $login_pcaptcha_error = get_option('login_pcaptcha_error');
        $login_pcaptcha_working = get_option('login_pcaptcha_working'); 
        $login_pcaptcha_notice = get_option('login_pcaptcha_notice');
        $time = time();
        if(!empty($login_pcaptcha_error) && (empty($login_pcaptcha_working) || ($time - $login_pcaptcha_notice < 30))) {
            $message_type = get_option('login_pcaptcha_message_type');
            if (empty($message_type)) {
                $message_type = 'notice-info';
            }
            echo '<div class="notice '.$message_type.' is-dismissible">'."\n";
            echo '    <p>'."\n";
            echo get_option('login_pcaptcha_error');
            echo '    </p>'."\n";
            echo '</div>'."\n";
        }
    }

    public static function add_shake_error_codes( $shake_error_codes ) {
        $shake_error_codes[] = 'pcaptcha';
        $shake_error_codes[] = 'invalid_captcha';
        return $shake_error_codes;
    }
}
LoginPCaptcha::init();
