<?php

/*
Plugin Name: Free Shipping Notice for WooCommerce
Description: Displays the remaining price to receive free shipping on the cart and checkout pages.
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
        add_action( 'woocommerce_proceed_to_checkout', 'fsn_shipping_notice_cart');
        add_action( 'woocommerce_checkout_before_order_review', 'fsn_shipping_notice_checkout');
        add_action( 'wp_head', 'fsn_css' );
        add_action('admin_menu', 'fsn_options');
        add_action( 'admin_enqueue_scripts', 'fsn_load_scripts' );
    } else {
        add_action('admin_notices', 'fsn_missing_wc');
    }
}

function fsn_load_scripts() {
    wp_enqueue_script( 'jscolor',plugin_dir_url(__FILE__).('/assets/jscolor.js') );
    wp_enqueue_script( 'chosen',plugin_dir_url(__FILE__).('/assets/chosen.jquery.min.js') );
    wp_enqueue_script( 'chosen-init',plugin_dir_url(__FILE__).('/assets/chosen-init.js') );

}

function fsn_shipping_notice_cart() {
    $totalamount = WC()->cart->cart_contents_total;
    //if (WC_Geolocation::is_geolocation_enabled($current_settings))
        $location = WC_Geolocation::geolocate_ip();
    $country_code = $location['country'];
    $country = WC()->countries->countries[$country_code];
    $free_shipping_countries = get_option("fsn-countries");
    if ($totalamount < get_option('fsn-shipping-min') && ($free_shipping_countries=="" || in_array($country, $free_shipping_countries) || get_option("fsn-all-countries")=="All"))
        echo fsn_message($totalamount).'<br><br>';
}

function fsn_shipping_notice_checkout() {
    $totalamount = WC()->cart->cart_contents_total;
    $location = WC_Geolocation::geolocate_ip();
    $country_code = $location['country'];
    $country = WC()->countries->countries[$country_code];
    $free_shipping_countries = get_option("fsn-countries");
    if ($totalamount < get_option('fsn-shipping-min') && ($free_shipping_countries=="" || in_array($country, $free_shipping_countries) || get_option("fsn-all-countries")=="All"))
        echo fsn_message($totalamount);
}

function fsn_message($totalamount) {
    return "You're <span class='freeship'>".get_option('fsn-default-currency').(get_option('fsn-shipping-min')-$totalamount)."</span> away from free shipping!";
}

function fsn_css() {
  echo ("<style type='text/css'>
	.freeship {
             font-weight: 500;
             color: #" .
      (get_option('fsn-highlight-color') == '' ? 'ff0000' : get_option('fsn-highlight-color')) . "
	</style>");
}

function fsn_options() {
    add_options_page('Free Shipping Notice Options',
        'Free Shipping Notice',
        'manage_options',
        'fsn_options',
        'fsn_options_page');
    add_action('admin_init', 'fsn_update');
}
function fsn_update() {
    register_setting('fsn_settings', 'fsn-highlight-color');
    register_setting('fsn_settings', 'fsn-shipping-min');
    register_setting('fsn_settings', 'fsn-countries');
    register_setting('fsn_settings', 'fsn-all-countries');
    register_setting('fsn_settings', 'fsn-default-currency');
}

function fsn_options_page() {
    ?>
    <div class="wrap">
        <h1>Free Shipping Notice</h1>
        <form method="post" action="options.php">
            <?php settings_fields('fsn_settings'); ?>
            <?php do_settings_sections('fsn_settings'); ?>
            <table>
                <tr>
                    <td><label for="color-picker">Color</label></td>
                    <td><input type="text" class="jscolor" name="fsn-highlight-color" id="color-picker" value="<?php echo get_option('fsn-highlight-color', '#ff0000')?>" /></td>
                </tr>
                <tr>
                    <td><label for="fsn-shipping-min">Free Shipping Minimum (<?php echo get_woocommerce_currency()?>)</label></td>
                    <td><input type="number" name="fsn-shipping-min" id='fsn-shipping-min' value="<?php echo get_option('fsn-shipping-min', 50);?>" /></td>
                </tr>
                <tr>
                    <td><label for="fsn-all-countries">Enable for all countries?</label></td>
                    <td><input type="checkbox" name="fsn-all-countries" id="fsn-all-countries" value="All" <?php echo (get_option('fsn-all-countries')=="All" ? 'checked' : '');?> /></td>
                </tr>
                <?php if (WC_Geolocation::is_geolocation_enabled($current_settings)) {?>
                <tr>
                    <td><label for="fsn-countries">Free Shipping Countries</label></td>
                    <td>
                        <select multiple data-placeholder="Choose a country..." class="chosen-select" id="fsn-countries" name="fsn-countries[]">
                            <?php $option = get_option('fsn-countries'); ?>
                            <?php
                            $countries_obj = new WC_Countries();
                            $countries = $countries_obj->__get('countries');
                            foreach($countries as $country) {
                                echo "<option ".(in_array($country,$option) ? "selected" : "").">$country</option>";
                            }?>
                        </select>
                    </td>
                </tr>
        <?php } ?>
                <tr>
                    <td><label for="fsn-currency-setting">Default Currency Symbol</label></td>
                    <td><input type="text" id="fsn-currency-setting" name="fsn-default-currency" value="<?php echo get_option('fsn-default-currency', '$');?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function fsn_missing_wc() {
    ?>
    <div class="error notice">
        <p><?php _e('You need to install and activate WooCommerce in order to use Free Shipping Notice!', 'free-shipping-notice-for-woocommerce')?>

        </p>
    </div>
<?php
}
