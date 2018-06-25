<?php

/*
Plugin Name: Free Shipping Notice
Description: A plugin that shows
Version: 1.0
Author: Caitlin Chou
Author URI: http://caitlinchou.me
Copyright: © 2018 Caitlin Chou.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function shipping_notice() {
    $totalamount = WC()->cart->cart_contents_total;
    if($totalamount < 50)
        echo 'You\'re <span class="freeship">$' . (50-$totalamount) . '</span> away from free shipping!<br><br>';
}

function fsn_css() {
  echo "<style type='text/css'>
	.freeship {
             font-weight: 500;
             color: #960740;
	</style>";
}

function fsn_options() {
    add_options_page('Free Shipping Notice Options',
        'Free Shipping Notice',
        'manage_options',
        'fsn_options',
        'fsn_options_page');
}

function fsn_options_page() {
    ?>
    <div>
        <form method="post" action="options.php">
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
    add_action( 'woocommerce_proceed_to_checkout', 'shipping_notice');
    add_action( 'wp_head', 'my_css' );
}