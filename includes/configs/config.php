<?php
/**
* Main config file for the pluging
* 
* The non-specific config will be found in this file, other config files includes too
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-paybox
* @subpackage config
*/


/**
*	Start main plugin variable definition
*/
{
	DEFINE('WPAYBOX_VERSION', '1.0');
	DEFINE('WPAYBOX_DEBUG', false);

	DEFINE('WPAYBOX_OPTION_MENU', 'wpaybox_options');

	DEFINE('WPAYBOX_URL_SLUG_ORDERS_LISTING', 'wpaybox_orders');
	DEFINE('WPAYBOX_URL_SLUG_ORDERS_EDITION', 'wpaybox_orders');

	DEFINE('WPAYBOX_URL_SLUG_FORMS_LISTING', 'wpaybox_forms');
	DEFINE('WPAYBOX_URL_SLUG_FORMS_EDITION', 'wpaybox_forms');

	DEFINE('WPAYBOX_URL_SLUG_OFFERS_LISTING', 'wpaybox_offers');
	DEFINE('WPAYBOX_URL_SLUG_OFFERS_EDITION', 'wpaybox_offers');
}


/**
*	Start plugin path definition
*/
{
	DEFINE('WPAYBOX_HOME_URL', WP_PLUGIN_URL . '/' . WPAYBOX_PLUGIN_DIR . '/');
	DEFINE('WPAYBOX_HOME_DIR', WP_PLUGIN_DIR . '/' . WPAYBOX_PLUGIN_DIR . '/');
	
	DEFINE('WPAYBOX_INC_PLUGIN_DIR', WPAYBOX_HOME_DIR . 'includes/');
	DEFINE('WPAYBOX_LIB_PLUGIN_DIR', WPAYBOX_INC_PLUGIN_DIR . 'librairies/');

	DEFINE('WPAYBOX_CSS_URL', WPAYBOX_HOME_URL . 'css/');
	DEFINE('WPAYBOX_JS_URL', WPAYBOX_HOME_URL . 'js/');
}


/**
*	Start database definition
*/
{
	/**
	* Get the global wordpress prefix for database table
	*/
	global $wpdb;
	/**
	* Define the main plugin prefix
	*/
	DEFINE('WPAYBOX_DB_PREFIX', $wpdb->prefix . "wppaybox__");
	/**
	*	Define the table wich will contain the different informations about the user and its payment
	*/
	DEFINE('WPAYBOX_DBT_ORDERS', WPAYBOX_DB_PREFIX . 'orders');
	/**
	*	Define the table wich will contain the different informations about the form to create
	*/
	DEFINE('WPAYBOX_DBT_FORMS', WPAYBOX_DB_PREFIX . 'forms');
	/**
	*	Define the table wich will contain the different existing offers
	*/
	DEFINE('WPAYBOX_DBT_LINK_FORMS_OFFERS', WPAYBOX_DB_PREFIX . 'forms_offers_link');
	/**
	*	Define the table wich will contain the different existing offers
	*/
	DEFINE('WPAYBOX_DBT_OFFERS', WPAYBOX_DB_PREFIX . 'offers');
}


/**
*	Start picture definition
*/
{
	DEFINE('WPAYBOX_SUCCES_ICON', admin_url('images/yes.png'));
	DEFINE('WPAYBOX_ERROR_ICON', admin_url('images/no.png'));
}

/**
*	Define the currency list
*/
{
	$currencyList = array();
	$currencyList[978] = __('Euro', 'wpaybox');
	$currencyList[840] = __('US Dollar', 'wpaybox');

	$currencyIconList = array();
	$currencyIconList[978] = '&euro;';
	$currencyIconList[840] = '&dollar;';
}

/**
*	Define the test environnement vars
*/
{
	$testEnvironnement['tpe'] = '1999888';
	$testEnvironnement['rang'] = '99';
	$testEnvironnement['identifier'] = '2';
	$testEnvironnement['url'] = 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi';
}

/**
*	Define the field to hide into a combobox
*/
{
	$comboxOptionToHide = array('deleted');
}