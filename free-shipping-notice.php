<?php

/*
Plugin Name: Free Shipping Notice for WooCommerce
Description: A plugin that shows how much more a customer needs to spend before they receive a free shipping reward.
Version: 1.0
Author: Caitlin Chou
Author URI: http://caitlinchou.me
Text Domain: free-shipping-notice-for-woocommerce
Copyright: © 2018 Caitlin Chou.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('init', 'fsn_init');

function fsn_init () {
    if (class_exists('WooCommerce')) {
        add_action( 'woocommerce_proceed_to_checkout', 'shipping_notice');
        add_action( 'wp_head', 'fsn_css' );
        add_action('admin_menu', 'fsn_options');
        add_action( 'admin_enqueue_scripts', 'fsn_color_picker' );
    } else {
        add_action('admin_notices', 'fsn_missing_wc');
    }
}

function fsn_color_picker() {
    wp_enqueue_script( 'iris',get_template_directory_uri().'/assets/iris.min.js' );
    wp_enqueue_script( 'iris-init',get_template_directory_uri().'/assets/iris-init.js' );

}

function shipping_notice() {
    $totalamount = WC()->cart->cart_contents_total;
    if($totalamount < 50)
        echo 'You\'re <span class="freeship">$' . (get_option('fsn-options[shipping-min]')-$totalamount) . '</span> away from free shipping!<br><br>';
}

function fsn_css() {
  echo ("<style type='text/css'>
	.freeship {
             font-weight: 500;
             color: #" .
      (get_option('fsn-options[highlight-color]') == '' ? 'ff0000' : get_option('fsn-options[highlight-color]')) . "
	</style>");
}

function fsn_options() {
    add_options_page('Free Shipping Notice Options',
        'Free Shipping Notice',
        'manage_options',
        'fsn_options',
        'fsn_options_page');
    add_action('admin_init', 'fsn_update');
    function fsn_update() {
        register_setting('fsn_settings', 'fsn_color');
    }
}

function fsn_options_page() {
    ?>
    <div class="wrap">
        <h1>Free Shipping Notice</h1>
        <form method="post" action="options.php">
            <?php settings_fields('fsn_settings'); ?>
            <?php do_settings_sections('fsn_settings'); ?>
            <label for="fsn-color-picker">Color</label>
            <input type="text" class="color-picker" name="fsn-options[highlight-color]" id='fsn-color-picker' value="#<?php echo get_option('fsn-options[highlight-color]');?>" placeholder="#ff0000" />
            <label for="fsn-shipping-min">Free Shipping Minimum ($)</label>
            <input type="number" name="fsn-options[shipping-min]" id='fsn-shipping-min' value="#<?php echo get_option('fsn-options[shipping-min]');?>" placeholder="50" />
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function fsn_missing_wc() {
    ?>
    <div class="error notice">
        <p><?php _e( 'You need to install and activate WooCommerce in order to use Free Shipping Notice!', 'free-shipping-notice-for-woocommerce')?>

        </p>
    </div>
<?php
}
