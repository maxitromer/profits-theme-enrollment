<?php

/*
Plugin Name: Profits Theme Enrollment
Plugin URI: https://github.com/maxitromer/profits-theme-enrollment
Description: Add Woocommerce clients to your Profits Theme Courses
Version: 0.1.1
Author: Maxi Tromer
Author URI: https://github.com/maxitromer
Developer: Maxi Tromer
Developer URI: https://github.com/maxitromer
GitHub Plugin URI: https://github.com/maxitromer/profits-theme-enrollment
WC requires at least: 3.0
WC tested up to: 3.7.1
Text Domain: profits-theme-enrollment
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// require __DIR__ . '/vendor/autoload.php';

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	if ( is_admin() ){
		if (! ( get_option( 'profits_woocommerce_settings_server' ) ) ) {
			add_action( 'admin_notices', 'profits_woocommerce_admin_notice_no_configuration' );
		}
		add_filter( 'woocommerce_settings_tabs_array', 'profits_woocommerce_add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_profits', 'profits_woocommerce_settings_tab' );
		add_action( 'woocommerce_update_options_profits', 'profits_woocommerce_settings_tab_update' );
	}
	else {
	}
}

/* 
Plug-in management
*/

function profits_woocommerce_add_settings_tab($settings_tabs) {

	$settings_tabs['profits'] = __( 'Profits Theme Integration', 'profits-theme-enrollment' );
	return $settings_tabs;
}

function profits_woocommerce_settings_tab() {
    woocommerce_admin_fields( profits_woocommerce_tab_settings() );
}

function profits_woocommerce_settings_tab_update() {
    woocommerce_update_options( profits_woocommerce_tab_settings() );
}

function profits_woocommerce_tab_settings() {
	$settings = array(

		'profits_section_title' => array(
			'name' => __('Set the Connection to Integrate Profits Theme', 'profits-theme-enrollment'),
			'type' => 'title',
			'desc' => __('Just add your Profits Theme Installation URL preferibly use a TLS (https) connection to be safe.', 'profits-theme-enrollment'),
			'id'   => 'profits_woocommerce_settings_profits_section_title'
		),
		
		'server' => array(
			'name'        => __('Profits Theme URL', 'profits-theme-enrollment'),
			'type'        => 'text',
			'css'         => 'min-width:200px;',
			'desc_tip'    => __('Include http or https and final /', 'profits-theme-enrollment'),
			'placeholder' =>  __('Your Profits Theme URL (including http/https)', 'profits-theme-enrollment'),
			'id'          => 'profits_woocommerce_settings_server'
		),

		'profits_section_end' => array(
			'type' => 'sectionend',
			'id'   => 'profits_woocommerce_settings_profits_section_end'
		),

	);
	return apply_filters( 'profits_woocommerce_settings', $settings );
}

// PLUGIN ADMIN FUNCTIONS

function profits_woocommerce_admin_notice_no_configuration() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'The Profits Theme integration for Woocommerce needs to be configured', 'profits-theme-enrollment' ); ?></p>
    </div>
    <?php
}

add_action('plugins_loaded', 'profits_woocommerce_load_textdomain');
function profits_woocommerce_load_textdomain() {
	load_plugin_textdomain( 'profits-theme-enrollment', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

include_once 'profits-theme-functions.php';


function profits_order_status_changed_action ( $order_id, $old_status, $new_status ) {

	// Get an instance of the WC_Order object
	$order = wc_get_order( $order_id );

	// Iterating through each WC_Order_Item_Product objects
	foreach ( $order->get_items() as $item_key => $item ):

		// IF PRODUCT IS NOT A SUBSCRIPTION ...
	    if ( $item->get_product()->get_type() != 'subscription' && $item->get_product()->get_type() != 'variable-subscription' ) {

			$order_data  = $order->get_data(); // The Order data
		    $product     = $item->get_product(); // Get the WC_Product object
			$profits_url = get_option( 'profits_woocommerce_settings_server' );
	
			// PROCESS AND SEND THE DATA TO UPDATE PROFITS THEME
			woocommerce_to_profits_theme ( $profits_url, $product->get_sku(), $new_status, $order_data['billing']['email'], $order_data['billing']['first_name'] );

	    }

	endforeach;

}

add_action( 'woocommerce_order_status_changed', 'profits_order_status_changed_action', 99, 3 );


function profits_update_subscription ( $subscription, $new_status, $old_status ) {

	include_once ('settings.php');

	$related_orders_ids_array = $subscription->get_related_orders( 'all', 'parent' );

	// Iterating through each Order object (in this case only the parent order)
	foreach ( $related_orders_ids_array as $order_id ) {

	    $order = new WC_Order( $order_id );
	    $items = $order->get_items();

	    foreach ( $items as $item ) {

	    	// IF PRODUCT IS A SUBSCRIPTION ...
	    	if ( $item->get_product()->get_type() == 'subscription' OR $item->get_product()->get_type() == 'variable-subscription' ) {

		    	$order_data   = $order->get_data();   // Get the Order Data
		    	$item_data    = $item->get_data();    // Get the Item Data
			    $product      = $item->get_product(); // Get the Product Data
			    $profits_url = get_option( 'profits_woocommerce_settings_server' );

			    // PROCESS AND SEND THE DATA TO UPDATE PROFITS THEME
				woocommerce_to_profits_theme ( $profits_url, $product->get_sku(), $subscription->get_status(), $order_data['billing']['email'], $order_data['billing']['first_name'] );

	    	}

	    }

	}   

}

add_action('woocommerce_subscription_status_updated', 'profits_update_subscription', 100, 3);

