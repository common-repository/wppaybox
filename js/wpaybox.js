/*	Define the plugin jquery var in order to avoid conflict with other plugin and scripts	*/
var wpaybox = jQuery.noConflict();

wpaybox(document).ready(function(){

	/*	Hide the message container if not empty	*/
	if(wpaybox("#wpayboxMessage").html != ''){
		setTimeout(function(){
			wpaybox("#wpayboxMessage").removeClass("wpayboxPageMessage_Updated");
			wpaybox("#wpayboxMessage").html("");
		}, 5000);
	}

	/*	Start the script that allows to make the header part of a page following the scroll	*/
	if(wpaybox("#pageTitleContainer").offset()){
		var pageTitleContainerOffset = wpaybox("#pageTitleContainer").offset().top;
		wpaybox(window).scroll(function(){
			if((wpaybox(window).scrollTop() > pageTitleContainerOffset) && !(wpaybox("#pageTitleContainer").hasClass("wpayboxPageTitle_Fixed"))){
				wpaybox("#pageTitleContainer").removeClass("pageTitle");
				wpaybox("#pageTitleContainer").addClass("wpayboxPageTitle_Fixed");
				wpaybox("#wpayboxPageHeaderButtonContainer").removeClass("wpayboxPageHeaderButton");
				wpaybox("#wpayboxPageHeaderButtonContainer").addClass("wpayboxPageHeaderButton_Fixed");
				wpaybox("#wpayboxMainContent").addClass("wpayboxContent_Fixed");
			}
			else if((wpaybox(window).scrollTop() <= pageTitleContainerOffset)  && (wpaybox("#pageTitleContainer").hasClass("wpayboxPageTitle_Fixed"))){
				wpaybox("#pageTitleContainer").addClass("pageTitle");
				wpaybox("#pageTitleContainer").removeClass("wpayboxPageTitle_Fixed");
				wpaybox("#wpayboxPageHeaderButtonContainer").addClass("wpayboxPageHeaderButton");
				wpaybox("#wpayboxPageHeaderButtonContainer").removeClass("wpayboxPageHeaderButton_Fixed");
				wpaybox("#wpayboxMainContent").removeClass("wpayboxContent_Fixed");
			}
		});
	}

	/*	Start the script that allows to make the message container following the scroll	*/
	if(wpaybox("#wpayboxMessage").offset()){
		var pageTitleContainerOffset = wpaybox("#wpayboxMessage").offset().top;
		wpaybox(window).scroll(function(){
			if((wpaybox(window).scrollTop() > pageTitleContainerOffset) && !(wpaybox("#wpayboxMessage").hasClass("wpayboxPageMessage_Fixed"))){
				wpaybox("#wpayboxMessage").addClass("wpayboxPageMessage_Fixed");
			}
			else if((wpaybox(window).scrollTop() <= pageTitleContainerOffset)  && (wpaybox("#wpayboxMessage").hasClass("wpayboxPageMessage_Fixed"))){
				wpaybox("#wpayboxMessage").removeClass("wpayboxPageMessage_Fixed");
			}
		});
	}

});

/*	Function called by default into form interface	*/
function wpayboxFormsInterface(deletionConfirmMessage){
	wpayboxFormsPaymentTypeSelection();
	wpaybox("#payment_type").change(function(){
		wpayboxFormsPaymentTypeSelection();
	});
	wpaybox("#wpayboxAssociateOffer").click(function(){
		var addNewOffer = true;
		var alreadyAffected = wpaybox("#associatedOfferList").val().split(", ");
		for(var i=0; i<alreadyAffected.length; i++){
			if(alreadyAffected[i] == wpaybox("#existingOffers").val()){
				addNewOffer = false;
			}
		}
		if(addNewOffer){
			wpaybox("#associatedOfferList").val(wpaybox("#associatedOfferList").val() + wpaybox("#existingOffers").val() + ", ");
			wpaybox("#associatedOfferListOutput").html(wpaybox("#associatedOfferListOutput").html() + '<div id="selectedOffer' + wpaybox("#existingOffers").val() + '" ><div class="ui-icon deleteOfferAssociation alignleft" >&nsp;</div>&nbsp;' + wpayboxConvertAccentTojs(offerList[wpaybox("#existingOffers").val()]) + '</div>');
		}
	});
	wpaybox(".deleteOfferAssociation").click(function(){
		if(confirm(wpayboxConvertAccentTojs(deletionConfirmMessage))){
			var currentOfferToDelete = wpaybox(this).attr("id").replace("offer", "");
			wpaybox("#associatedOfferList").val(wpaybox("#associatedOfferList").val().replace(currentOfferToDelete + ", ", ""));
			wpaybox("#selectedOffer" + currentOfferToDelete).remove();
		}
	});
}

/*	When changing the payment form type, display or hide complementary element into the form	*/
function wpayboxFormsPaymentTypeSelection(){
	if(wpaybox("#payment_type").val() == "single_payment"){
		wpaybox("#wpayboxMultiplePaymentFieldContainer").hide();
	}
	else if(wpaybox("#payment_type").val() == "multiple_payment"){
		wpaybox("#wpayboxMultiplePaymentFieldContainer").show();
	}
}

/*	Define the different behavior for the main interface	*/
function wpayboxMainInterface(currentType, confirmCancelMessage, listingSlugUrl){
	wpaybox("#" + currentType + "_form input, #" + currentType + "_form textarea").keypress(function(){
		wpaybox("#" + currentType + "_form_has_modification").val("yes");
	});
	wpaybox("#" + currentType + "_form select").change(function(){
		wpaybox("#" + currentType + "_form_has_modification").val("yes");
	});
	wpaybox("#save").click(function(){
		wpaybox("#" + currentType + "_form").attr("action", listingSlugUrl);
		wpaybox("#" + currentType + "_form").submit();
	});
	wpaybox("#add").click(function(){
		wpaybox("#" + currentType + "_form").submit();
	});
	wpaybox("#saveandcontinue").click(function(){
		wpaybox("#" + currentType + "_action").val(wpaybox("#" + currentType + "_action").val() + "andcontinue");
		wpaybox("#" + currentType + "_form").submit();
	});
	wpaybox(".wpayboxCancelButton").click(function(){
		if((wpaybox("#" + currentType + "_form_has_modification").val() == "yes")){
			if(!confirm(wpayboxConvertAccentTojs(confirmCancelMessage))){
				return false;
			}
		}
	});
}

/*	Allows to output special characters into javascript	*/
function wpayboxConvertAccentTojs(text){
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/&Eacute;/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/&eacute;/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}