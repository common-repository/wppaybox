<?php
/**
 * Plugin Installer
 * 
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-paybox
 * @subpackage librairies
 */

/**
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @package wp-paybox
 * @subpackage librairies
 */
class wpaybox_install
{

	/**
	*	Define actions lauched after plugin activation. Create the database, create the different option, call the permisssion setters
	*/
	function wpaybox_activate()
	{
		global $wpaybox_db_option;
	
		/*	Create an instance for the database option	*/
		$wpaybox_db_option = new wpaybox_db_option();
		$currentDBVersion = $wpaybox_db_option->get_db_version();
		if(!($currentDBVersion > 0))
		{
			$wpaybox_db_option->create_db_option();
		}

		/*	Create 	*/
		wpaybox_database::wpaybox_db_creation();

		/*	Create the option for the store option	*/
		$wpaybox_store_mainoption = get_option('wpaybox_store_mainoption');
		if($wpaybox_store_mainoption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['storeTpe'] = $testEnvironnement['tpe'];
			$optionList['storeRang'] = $testEnvironnement['rang'];
			$optionList['storeIdentifier'] = $testEnvironnement['identifier'];
			$optionList['environnement'] = 'test';
			wpaybox_option::saveStoreConfiguration('wpaybox_store_mainoption', $optionList, false);
		}

		/*	Create the option for the return url	*/
		$wpaybox_store_urloption = get_option('wpaybox_store_urloption');
		if($wpaybox_store_urloption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['urlSuccess'] = get_bloginfo('siteurl') . '/';
			$optionList['urlDeclined'] = get_bloginfo('siteurl') . '/';
			$optionList['urlCanceled'] = get_bloginfo('siteurl') . '/';
			wpaybox_option::saveStoreConfiguration('wpaybox_store_urloption', $optionList, false);
		}

		/*	Set the different permissions	*/
		wpaybox_install::wpaybox_set_permissions();
	}

	/**
	*	Define actions launched when plugin is deactivate.
	*/
	function wpaybox_deactivate()
	{
		global $wpdb;

		// $wpdb->query("DROP TABLE " . WPAYBOX_DBT_ORDERS . ", " . WPAYBOX_DBT_FORMS . ", " . WPAYBOX_DBT_OFFERS . ", " . WPAYBOX_DBT_LINK_FORMS_OFFERS . ";");
		// delete_option('wpaybox_store_urloption');
		// delete_option('wpaybox_store_urloption');
		// delete_option('wpaybox_db_option');
	}

	/**
	*	Define the different permissions affected to users.
	*/
	function wpaybox_set_permissions()
	{
		$wpaybox_permission_list = array();
		$wpaybox_permission_list[] = 'wpaybox_manage_options';

		$wpaybox_permission_list[] = 'wpaybox_view_orders';
		$wpaybox_permission_list[] = 'wpaybox_view_orders_details';
		$wpaybox_permission_list[] = 'wpaybox_delete_orders';

		$wpaybox_permission_list[] = 'wpaybox_view_forms';
		$wpaybox_permission_list[] = 'wpaybox_view_forms_details';
		$wpaybox_permission_list[] = 'wpaybox_add_forms';
		$wpaybox_permission_list[] = 'wpaybox_edit_forms';
		$wpaybox_permission_list[] = 'wpaybox_delete_forms';

		$wpaybox_permission_list[] = 'wpaybox_view_forms_offers_link';
		$wpaybox_permission_list[] = 'wpaybox_delete_forms_offers_link';

		$wpaybox_permission_list[] = 'wpaybox_view_offers';
		$wpaybox_permission_list[] = 'wpaybox_view_offers_details';
		$wpaybox_permission_list[] = 'wpaybox_add_offers';
		$wpaybox_permission_list[] = 'wpaybox_edit_offers';
		$wpaybox_permission_list[] = 'wpaybox_delete_offers';

		/**
		*	Add capabilities to the administrator role
		*/
		$role = get_role('administrator');
		foreach($wpaybox_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);

		$wpaybox_permission_list = array();

		$wpaybox_permission_list[] = 'wpaybox_view_orders';
		$wpaybox_permission_list[] = 'wpaybox_view_orders_details';

		$wpaybox_permission_list[] = 'wpaybox_view_forms';
		$wpaybox_permission_list[] = 'wpaybox_view_forms_details';

		$wpaybox_permission_list[] = 'wpaybox_view_forms_offers_link';

		$wpaybox_permission_list[] = 'wpaybox_view_offers';
		$wpaybox_permission_list[] = 'wpaybox_view_offers_details';
		/**
		*	Add capabilities to the editor role
		*/
		$role = get_role('editor');
		foreach($wpaybox_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);
	}

}