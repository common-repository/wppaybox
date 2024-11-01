<?php
/**
* Define the different method to access or create orders
* 
*	Define the different method to access or create orders
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-paybox
* @subpackage librairies
*/

/**
* Define the different method to access or create orders
* @package wp-paybox
* @subpackage librairies
*/
class wpaybox_orders
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wpaybox_orders';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getPageIcon()
	{
		return '';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getListingSlug()
	{
		return WPAYBOX_URL_SLUG_ORDERS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPAYBOX_URL_SLUG_ORDERS_EDITION;
	}
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPAYBOX_DBT_ORDERS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		$action = isset($_REQUEST['action']) ? wpaybox_tools::varSanitizer($_REQUEST['action']) : '';
		$objectInEdition = isset($_REQUEST['id']) ? wpaybox_tools::varSanitizer($_REQUEST['id']) : '';

		$title = __('Liste des commandes', 'wpaybox' );
		if($action != '')
		{
			if(($action == 'view') || ($action == 'delete'))
			{
				$editedItem = wpaybox_orders::getElement($objectInEdition);
				$title = sprintf(__('Voir la commande "%s"', 'wpaybox'), $editedItem->order_reference);
			}
		}
		return $title;
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*/
	function elementAction()
	{
		$pageAction = isset($_REQUEST[wpaybox_orders::getDbTable() . '_action']) ? wpaybox_tools::varSanitizer($_REQUEST[wpaybox_orders::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wpaybox_orders::getDbTable()]['id']) ? wpaybox_tools::varSanitizer($_REQUEST[wpaybox_orders::getDbTable()]['id']) : '';

		/*	Start definition of output message when action is doing on another page	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/****************************************************************************/
		$action = isset($_REQUEST['action']) ? wpaybox_tools::varSanitizer($_REQUEST['action']) : '';
		$saveditem = isset($_REQUEST['saveditem']) ? wpaybox_tools::varSanitizer($_REQUEST['saveditem']) : '';
		if(($action != '') && ($action == 'deleteok') && ($saveditem > 0))
		{
			$editedElement = wpaybox_orders::getElement($saveditem, "'deleted'");
			$pageMessage = '<img src="' . WPAYBOX_SUCCES_ICON . '" alt="action success" class="wpayboxPageMessage_Icon" />' . sprintf(__('La commande "%s" a &eacute;t&eacute; supprim&eacute;e avec succ&eacute;s', 'wpaybox'), '<span class="bold" >' . $editedElement->order_reference . '</span>');
		}

		if($pageAction == 'delete')
		{
			if(current_user_can('wpaybox_delete_orders'))
			{
				$_REQUEST[wpaybox_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wpaybox_orders::getDbTable()]['status'] = 'deleted';
				$actionResult = wpaybox_database::update($_REQUEST[wpaybox_orders::getDbTable()], $id, wpaybox_orders::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}

		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wpaybox_orders::getDbTable()]['name'] . '</span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$pageMessage .= '<img src="' . wpaybox_ERROR_ICON . '" alt="action error" class="wpayboxPageMessage_Icon" />' . sprintf(__('Une erreur est survenue lors de la suppression de %s', 'wpaybox'), $elementIdentifierForMessage);
				if(WPAYBOX_DEBUG)
				{
					$pageMessage .= '<br/>' . $wpdb->last_error;
				}
			}
			elseif(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
			{
				/*************************			GENERIC				****************************/
				/*************************************************************************/
				$pageMessage .= '<img src="' . wpaybox_SUCCES_ICON . '" alt="action success" class="wpayboxPageMessage_Icon" />' . sprintf(__('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', 'wpaybox'), $elementIdentifierForMessage);
				if($pageAction == 'delete')
				{
					wp_redirect(admin_url('admin.php?page=' . wpaybox_orders::getListingSlug() . "&action=deleteok&saveditem=" . $id));
				}
			}
			elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
			{
				$pageMessage .= '<img src="' . wpaybox_ERROR_ICON . '" alt="action error" class="wpayboxPageMessage_Icon" />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.', 'wpaybox');
			}
		}
	}
	/**
	*	Return the list page content, containing the table that present the item list
	*
	*	@return string $listItemOutput The html code that output the item list
	*/
	function elementList()
	{
		global $currencyIconList;
		$listItemOutput = '';

		/*	Start the table definition	*/
		$tableId = wpaybox_orders::getDbTable() . '_list';
		$tableSummary = __('orders listing', 'wpaybox');
		$tableTitles = array();
		$tableTitles[] = __('R&eacute;f&eacute;rence', 'wpaybox');
		$tableTitles[] = __('Date', 'wpaybox');
		$tableTitles[] = __('Montant', 'wpaybox');
		$tableTitles[] = __('Statut', 'wpaybox');
		$tableClasses = array();
		$tableClasses[] = 'wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_reference_column';
		$tableClasses[] = 'wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_date_column';
		$tableClasses[] = 'wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_ammount_column';
		$tableClasses[] = 'wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_order_status_column';

		$line = 0;
		$elementList = wpaybox_orders::getElement('', "'valid', 'moderated'", '', " ORDER BY O.creation_date DESC");
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wpaybox_orders::getDbTable() . '_' . $element->id;

				$elementLabel = $element->order_reference;
				$subRowActions = '';
				if(current_user_can('wpaybox_view_orders_details'))
				{
					$editAction = admin_url('admin.php?page=' . wpaybox_orders::getEditionSlug() . '&amp;action=view&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wpaybox') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->order_reference  . '</a>';
				}
				if(current_user_can('wpaybox_delete_orders'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wpaybox_orders::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wpaybox') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wpayboxRowAction" >' . $subRowActions . '
	</div>';

				$orderAmount = '';
				$orderAmount = ($element->order_amount / 100);

				unset($tableRowValue);
				$tableRowValue[] = array('class' => wpaybox_orders::getCurrentPageCode() . '_reference_cell', 'value' => $elementLabel . $rowActions);
				$tableRowValue[] = array('class' => wpaybox_orders::getCurrentPageCode() . '_date_cell', 'value' => mysql2date('d M Y H:i:s', $element->creation_date, true));
				$tableRowValue[] = array('class' => wpaybox_orders::getCurrentPageCode() . '_amount_cell', 'value' => $orderAmount . '&nbsp;' . $currencyIconList[$element->order_currency]);
				$tableRowValue[] = array('class' => wpaybox_orders::getCurrentPageCode() . '_order_status_cell', 'value' => __($element->order_status, 'wpaybox'));
				$tableRows[] = $tableRowValue;

				$line++;
			}
		}
		else
		{
			$tableRowsId[] = wpaybox_orders::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wpaybox_orders::getCurrentPageCode() . '_label_cell', 'value' => __('Aucune commande n\'a encore &eacute;t&eacute; pass&eacute;e', 'wpaybox'));
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wpaybox_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}
	/**
	*	Return the page content to add a new item
	*
	*	@return string The html code that output the interface for adding a nem item
	*/
	function elementEdition($itemToEdit = '')
	{
		global $currencyIconList;
		$dbFieldList = wpaybox_database::fields_to_input(wpaybox_orders::getDbTable());

		$editedItem = '';
		if($itemToEdit != '')
		{
			$editedItem = wpaybox_orders::getElement($itemToEdit);
		}

		$the_form_content_hidden = $the_form_general_content = '';
		$the_form_payment_content = $the_form_user_content = $the_form_order_content = '';
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];

			$pageAction = isset($_REQUEST[wpaybox_orders::getDbTable() . '_action']) ? wpaybox_tools::varSanitizer($_REQUEST[wpaybox_orders::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wpaybox_orders::getDbTable()][$input_name]) ? wpaybox_tools::varSanitizer($_REQUEST[wpaybox_orders::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}

			/*	Translate the field value	*/
			$input_def['value'] = __($currentFieldValue, 'wpaybox');

			/*	Store the payment definition fields	*/
			if(substr($input_name, 0, 8) == 'payment_')
			{
				if($input_name == 'payment_currency')
				{
					$input_def['value'] = $currencyIconList[$currentFieldValue];
				}
				elseif($input_name == 'payment_amount')
				{
					$input_def['value'] = ($currentFieldValue / 100);
				}
				elseif($input_name == 'payment_recurrent_amount')
				{
					if($currentFieldValue > 0)
					{
						$input_def['value'] = ($currentFieldValue / 100);
					}
					else
					{
						$input_def['value'] = ($editedItem->payment_amount / 100);
					}
				}
				elseif($input_name == 'payment_recurrent_start_delay')
				{
					if($currentFieldValue == 0)
					{
						$input_def['value'] =  __('D&eacute;but imm&eacute;diat', 'wpaybox');
					}
					else
					{
						$input_def['value'] =  sprintf(__('%d jours apr&eacute;s l\'inscription', 'wpaybox'), $currentFieldValue);
					}
				}
				elseif($input_name == 'payment_recurrent_day_of_month')
				{
					if($currentFieldValue == 0)
					{
						$currentFieldValue = mysql2date('d', $editedItem->creation_date);
					}
					$input_def['value'] =  sprintf(__('Le %d de chaque mois', 'wpaybox'), $currentFieldValue);
				}

				if((substr($input_name, 0, 18) != 'payment_recurrent_') || ($editedItem->payment_type == 'multiple_payment'))
				{
					$the_form_payment_content .= '
		<div class="clear" >
			<div class="wpaybox_form_label wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpaybox') . '
			</div>
			<div class="wpaybox_form_input wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
				}
			}

			/*	Store the user fields	*/
			elseif(substr($input_name, 0, 5) == 'user_')
			{
				$input_name = $input_name . '_admin_side';
				$the_form_user_content .= '
		<div class="clear" >
			<div class="wpaybox_form_label wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpaybox') . '
			</div>
			<div class="wpaybox_form_input wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
			}

			/*	Store the payment return fields	*/
			elseif((substr($input_name, 0, 6) == 'order_') || ($input_name == 'offer_id'))
			{
				if($input_name == 'order_currency')
				{
					$input_def['value'] = $currencyIconList[$currentFieldValue];
				}
				elseif($input_name == 'order_amount')
				{
					$input_def['value'] = ($currentFieldValue / 100);
				}

				$the_form_order_content .= '
		<div class="clear" >
			<div class="wpaybox_form_label wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpaybox') . '
			</div>
			<div class="wpaybox_form_input wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
			}		

			/*	For all the other field	*/
			else
			{
				if($input_name == 'creation_date')
				{
					$input_name = 'order_creation_date';
					$input_def['value'] = mysql2date('d M Y H:i', $currentFieldValue, true);
				}

				if(($input_name == 'status') || ($input_name == 'last_update_date') || ($input_name == 'form_id'))
				{
					$input_def['type'] = 'hidden';
				}

				if($input_def['type'] != 'hidden')
				{
					$the_form_general_content .= '
			<div class="clear" >
				<div class="wpaybox_form_label wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					' . __($input_name, 'wpaybox') . '
				</div>
				<div class="wpaybox_form_input wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $input_def['value'] . '
				</div>
			</div>';
				}
				else
				{
					$the_form_content_hidden .= '
			' . wpaybox_form::check_input_type($input_def, wpaybox_orders::getDbTable());
				}
			}
		}

		/*	Build the general output with the different order's element	*/
		$the_form_general_content .= '
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations commandes', 'wpaybox') . '</legend>
			<div>' . $the_form_order_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations client', 'wpaybox') . '</legend>
			<div>' . $the_form_user_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations paiement', 'wpaybox') . '</legend>
			<div>' . $the_form_payment_content	. '</div>
		</fieldset>';

		$the_form = '
<form name="' . wpaybox_orders::getDbTable() . '_form" id="' . wpaybox_orders::getDbTable() . '_form" method="post" action="" >
' . wpaybox_form::form_input(wpaybox_orders::getDbTable() . '_action', wpaybox_orders::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wpaybox_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
<div id="wpayboxFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wpaybox_' . wpaybox_orders::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wpaybox(document).ready(function(){
		wpaybox("#delete").click(function(){
			wpaybox("#' . wpaybox_orders::getDbTable() . '_action").val("delete");
			deleteOrder();
		});
		if(wpaybox("#' . wpaybox_orders::getDbTable() . '_action").val() == "delete"){
			deleteOrder();
		}
		function deleteOrder(){
			if(confirm(wpayboxConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cette commande?', 'wpaybox') . '"))){
				wpaybox("#' . wpaybox_orders::getDbTable() . '_form").submit();
			}
			else{
				wpaybox("#' . wpaybox_orders::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';

		return $the_form;
	}
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		$action = isset($_REQUEST['action']) ? wpaybox_tools::varSanitizer($_REQUEST['action']) : 'add';
		$currentPageButton = '';

		if(current_user_can('wpaybox_delete_orders') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wpaybox') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wpayboxCancelButton" ><a href="' . admin_url('admin.php?page=' . wpaybox_orders::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wpaybox') . '</a></h2>';

		return $currentPageButton;
	}
	/**
	*	Get the existing element list into database
	*
	*	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	*	@param string $elementStatus optionnal The status of element to get into database. Default is set to valid element
	*
	*	@return object $elements A wordpress database object containing the element list
	*/
	function getElement($elementId = '', $elementStatus = "'valid', 'moderated'", $whatToGet = 'id', $orderByStatement = '')
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = "
			AND O." . $whatToGet . " = '" . $elementId . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT O.*
		FROM " . wpaybox_orders::getDbTable() . " AS O
		WHERE O.status IN (".$elementStatus.") " . $moreQuery . "
		" . $orderByStatement
		);

		/*	Get the query result regarding on the function parameters. If there must be only one result or a collection	*/
		if($elementId == '')
		{
			$elements = $wpdb->get_results($query);
		}
		else
		{
			$elements = $wpdb->get_row($query);
		}

		return $elements;
	}

	/**
	*	Save a new order into database from the given informations
	*
	*	@param array $orderInformations The informations sent by the user through the form and from the payment form definition
	*
	*	@return integer $orderReference The last order Identifier to create an unique
	*/
	function saveNewOrder($orderInformations)
	{
		global $wpdb;
		$orderReference = 0;
		
		/*	Get the last order identifier	*/
		$offer = wpaybox_offers::getElement($_POST['selectedOffer']);

		/*	Create the new order	*/
		$orderMoreInformations['id'] = '';
		$orderMoreInformations['form_id'] = $orderInformations['formIdentifier'];
		$orderMoreInformations['offer_id'] = $orderInformations['selectedOffer'];
		$orderMoreInformations['status'] = 'valid';
		$orderMoreInformations['order_status'] = 'initialised';
		$orderMoreInformations['creation_date'] = date('Y-m-d H:i:s');
		foreach($orderInformations['order_user'] as $orderUserField => $orderUserFieldValue)
		{
			$orderMoreInformations[$orderUserField] = $orderUserFieldValue;
		}

		/*	Save offer informations in case of modification in future	*/
		$orderMoreInformations['payment_type'] = $offer->payment_type;
		$orderMoreInformations['payment_recurrent_amount'] = $offer->payment_recurrent_amount;
		$orderMoreInformations['payment_recurrent_number'] = $offer->payment_recurrent_number;
		$orderMoreInformations['payment_recurrent_frequency'] = $offer->payment_recurrent_frequency;
		$orderMoreInformations['payment_recurrent_day_of_month'] = $offer->payment_recurrent_day_of_month;
		$orderMoreInformations['payment_recurrent_start_delay'] = $offer->payment_recurrent_start_delay;
		$orderMoreInformations['payment_reference_prefix'] = $offer->payment_reference_prefix;
		$orderMoreInformations['payment_name'] = $offer->payment_name;
		$orderMoreInformations['payment_currency'] = $offer->payment_currency;
		$orderMoreInformations['payment_amount'] = $offer->payment_amount;
		$orderMoreInformations['order_currency'] = $offer->payment_currency;
		$orderMoreInformations['order_amount'] = $offer->payment_amount;
		$actionResult = wpaybox_database::save($orderMoreInformations, wpaybox_orders::getDbTable());
		if($actionResult == 'done')
		{
			$orderReference = $wpdb->insert_id;
			/*	Update the new order reference	*/
			$orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
			$orderMoreInformations['order_reference'] = $offer->payment_reference_prefix . $orderReference;
			$actionResult = wpaybox_database::update($orderMoreInformations, $orderReference, wpaybox_orders::getDbTable());
		}

		/*	Check the payment type in case that this is a multiple payment	*/
		if($offer->payment_type == 'multiple_payment')
		{
			$orderReference .= 'IBS_2MONT' . zeroise($offer->payment_recurrent_amount, 10) . 'IBS_NBPAIE' . zeroise($offer->payment_recurrent_number, 2) . 'IBS_FREQ' . zeroise($offer->payment_recurrent_frequency, 2) . 'IBS_QUAND' . zeroise($offer->payment_recurrent_day_of_month, 2) . 'IBS_DELAIS' . zeroise($offer->payment_recurrent_start_delay, 3);
		}

		return $orderReference;
	}

	/**
	*	Output the result of a transaction we return form paybox. Called by a shortcode on the return page (success/canceled/declined)
	*
	*	@return string $outputMessage A message to output to the end-user when transaction is finished
	*/
	function paymentReturn()
	{
		global $currencyIconList;

		$reference = isset($_REQUEST['reference']) ? wpaybox_tools::varSanitizer($_REQUEST['reference']) : '';
		$autorisation = isset($_REQUEST['autorisation']) ? wpaybox_tools::varSanitizer($_REQUEST['autorisation']) : '';
		$transaction = isset($_REQUEST['transaction']) ? wpaybox_tools::varSanitizer($_REQUEST['transaction']) : '';
		$error = isset($_REQUEST['error']) ? wpaybox_tools::varSanitizer($_REQUEST['error']) : '';

		if($reference != '')
		{
			$referenceComponent = explode('IBS_2MONT', $reference);
			if(is_array($referenceComponent) && (count($referenceComponent) >=2 ))
			{
				$reference = $referenceComponent[0];
			}
			/*	Get the orders informations to update with the paybox return infos	*/
			$currentOrder = wpaybox_orders::getElement($reference, "'valid'", 'order_reference');

			/*	Update the current order	*/
			$orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
			$orderMoreInformations['order_autorisation'] = $autorisation;
			$orderMoreInformations['order_transaction'] = $transaction;
			$orderMoreInformations['order_error'] = $error;

			$url = '';
			switch($error)
			{
				case '00000':
					$order_status = 'closed';
					/*	Get the orders informations to update with the paybox return infos	*/
					$currentOrder = wpaybox_orders::getElement($reference, "'valid'", 'order_reference');
					$amout = $currentOrder->order_amount / 100;
					$outputMessage = sprintf(__('Votre paiement de %s a bien &eacute;t&eacute; effectu&eacute;', 'wpaybox'), $amout . '&nbsp;' . $currencyIconList[$currentOrder->order_currency]);
				break;
				case '00001':
				case '00003':
				case '00004':
				case '00006':
				case '00008':
				case '00009':
				case '00010':
				case '00011':
				case '00015':
				case '00016':
				case '00021':
				case '00029':
				case '00030':
				case '00031':
				case '00032':
				case '00033':	
					$order_status = 'error';
					$outputMessage = __('Une erreur est survenue lors de votre paiement, pour plus d\'informations contactez nous en pr&eacute;cisant le cod d\'erreur suivant: PaymentReturn#' . $reference . 'E' . $error . '', 'wpaybox');
				break;
			}

			$orderMoreInformations['order_status'] = $order_status;
			$actionResult = wpaybox_database::update($orderMoreInformations, $currentOrder->id, wpaybox_orders::getDbTable());
		}
		else
		{
			$outputMessage = '';
		}

		return '<div class="paymentReturnResponse" >' . $outputMessage . '</div>';
	}

}