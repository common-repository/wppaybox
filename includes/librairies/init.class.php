<?php
/**
* Plugin Loader
* 
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-paybox
* @subpackage librairies
*/

/**
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @package wp-paybox
* @subpackage librairies
*/
class wpaybox_init
{

	/**
	*	Load the different element need to create the plugin environnement
	*/
	function wpaybox_plugin_load()
	{
		global $wpaybox_db_option;

		/*	Call function to create the main left menu	*/
		add_action('admin_menu', array('wpaybox_init', 'wpaybox_menu') );

		/*	Get the current language to translate the different text in plugin	*/
		$locale = get_locale();
		$moFile = WPAYBOX_INC_PLUGIN_DIR . 'languages/wppaybox-' . $locale . '.mo';
		if( !empty($locale) && (is_file($moFile)) )
		{
			load_textdomain('wpaybox', $moFile);
		}

		/*	Do the update on the database	*/
		wpaybox_database::wpaybox_db_update();

		/*	Check the last optimisation date if it was not perform today weoptimise the database	*/
		if($wpaybox_db_option->get_db_optimisation_date() != date('Y-m-d'))
		{
			wpaybox_database::wpaybox_db_optimisation();

			$wpaybox_db_option->set_db_optimisation_date(date('Y-m-d'));
			$wpaybox_db_option->set_db_option();
		}

		/*	Include the different css	*/
		add_action('init', array('wpaybox_init', 'wpaybox_front_css') );
		/*	Include the different css	*/
		add_action('init', array('wpaybox_init', 'wpaybox_front_js') );
		/*	Include the different css	*/
		add_action('admin_init', array('wpaybox_init', 'wpaybox_admin_css') );
		/*	Include the different js	*/
		add_action('admin_init', array('wpaybox_init', 'wpaybox_admin_js') );
	}

	/**
	*	Create the main left menu with different parts
	*/
	function wpaybox_menu() 
	{
		/*	Add the options menu in the options section	*/
		add_options_page(__('Options principale du module de paiement paybox', 'wpaybox'), __('Paybox', 'wpaybox'), 'wpaybox_manage_options', WPAYBOX_OPTION_MENU, array('wpaybox_option', 'doOptionsPage'));

		/*	Main menu */
		add_menu_page(__('Liste des commandes', 'wpaybox' ), __('Paybox', 'wpaybox' ), 'wpaybox_view_orders', WPAYBOX_URL_SLUG_ORDERS_LISTING, array('wpaybox_display', 'displayPage'));

		/*	Redefine the dashboard page	*/
		add_submenu_page( WPAYBOX_URL_SLUG_ORDERS_LISTING, wpaybox_orders::pageTitle(), __('Commandes', 'wpaybox' ), 'wpaybox_view_orders', WPAYBOX_URL_SLUG_ORDERS_LISTING, array('wpaybox_display','displayPage'));
		add_submenu_page( WPAYBOX_URL_SLUG_ORDERS_LISTING, wpaybox_payment_form::pageTitle(), __('Formulaires', 'wpaybox' ), 'wpaybox_view_forms', WPAYBOX_URL_SLUG_FORMS_LISTING, array('wpaybox_display','displayPage'));
		add_submenu_page( WPAYBOX_URL_SLUG_ORDERS_LISTING, wpaybox_offers::pageTitle(), __('Offres', 'wpaybox' ), 'wpaybox_view_offers', WPAYBOX_URL_SLUG_OFFERS_LISTING, array('wpaybox_display','displayPage'));
	}

	/**
	*	Define the javascript to include in each page
	*/
	function wpaybox_admin_js()
	{
		if(!wp_script_is('jquery', 'queue'))
		{
			wp_enqueue_script('jquery');
		}
		if(!wp_script_is('jquery-ui-core', 'queue'))
		{
			wp_enqueue_script('jquery-ui-core');
		}
		wp_enqueue_script('wpaybox_main_js', WPAYBOX_JS_URL . 'wpaybox.js');
	}

	/**
	*	Define the javascript to include in each page
	*/
	function wpaybox_front_js()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('wpaybox_main_js', WPAYBOX_JS_URL . 'wpaybox.js');
	}

	/**
	*	Define the css to include in each page
	*/
	function wpaybox_admin_css()
	{
		wp_register_style('wpaybox_jquery-ui', WPAYBOX_CSS_URL . 'jquery-ui.css');
		wp_enqueue_style('wpaybox_jquery-ui');
		wp_register_style('wpaybox_main_css', WPAYBOX_CSS_URL . 'wpaybox.css');
		wp_enqueue_style('wpaybox_main_css');
	}

	/**
	*	Define the css to include in frontend
	*/
	function wpaybox_front_css()
	{
		wp_register_style('wpaybox_front_main_css', WPAYBOX_CSS_URL . 'wpaybox_front.css');
		wp_enqueue_style('wpaybox_front_main_css');
	}
}