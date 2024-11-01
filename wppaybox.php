<?php
/*
Plugin Name: wppaybox
Description: This plugin allows to add a paybox payment form on a wordpress website
Version: 1.3
Author: Eoxia
Author URI: http://www.eoxia.com
*/

/**
* Plugin main file.
* 
*	This file is the main file called by wordpress for our plugin use. It define the basic vars and include the different file needed to use the plugin
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-paybox
*/

/**
*	First thing we define the main directory for our plugin in a super global var	
*/
DEFINE('WPAYBOX_PLUGIN_DIR', basename(dirname(__FILE__)));
/**
*	Include the different config for the plugin	
*/
require_once(WP_PLUGIN_DIR . '/' . WPAYBOX_PLUGIN_DIR . '/includes/configs/config.php' );
/**
*	Define the path where to get the config file for the plugin
*/
DEFINE('WPAYBOX_CONFIG_FILE', WPAYBOX_INC_PLUGIN_DIR . 'configs/config.php');
/**
*	Include the file which includes the different files used by all the plugin
*/
require_once(	WPAYBOX_INC_PLUGIN_DIR . 'includes.php' );

/*	Create an instance for the database option	*/
$wpaybox_db_option = new wpaybox_db_option();

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPAYBOX_LIB_PLUGIN_DIR . 'install.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin	
*/
register_activation_hook( __FILE__ , array('wpaybox_install', 'wpaybox_activate') );
register_deactivation_hook( __FILE__ , array('wpaybox_install', 'wpaybox_deactivate') );

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPAYBOX_LIB_PLUGIN_DIR . 'init.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin	
*/
add_action('plugins_loaded', array('wpaybox_init', 'wpaybox_plugin_load'));

add_shortcode('wppaybox_payment_return', array('wpaybox_orders', 'paymentReturn'));
add_shortcode('wpaybox_payment_form', array('wpaybox_payment_form', 'displayForm'));