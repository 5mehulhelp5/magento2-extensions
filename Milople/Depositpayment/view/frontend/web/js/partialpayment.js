/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Partialpaymentauto
* @copyright   Copyright (c) 2019 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/

// this function called when click on Installment
function installmentChecked(flag,productId,url,currencySymbol,currencyCode,numOfInstallment)
{
	/*
	here 
		flag = 1 means installment radio cliked
		flag = 2 means refresh button from calculation table clicked
		flag = 3 means question mark button clicked
	*/
	var partialPaymentDetailTable = jQuery("#partial-payment-detail-table");
	var isAjax = true;
	var ajaxLoader = jQuery("#ajax-load");
	jQuery("#full-payment .allow-partialpayment-radio").attr("aria-invalid",false);
	if(flag==1)
	{
		if(productId == '')
		{
			if((numOfInstallment == 0 && typeof jQuery("input[name=allow-partial-payment]").val() !== 'undefined') || (numOfInstallment == 1 && jQuery("#allow-partial-payment").val()!=''))
			{
				ajaxLoader.show(); //display ajax loader in installment radio button
				document.getElementById('wholecart_partialpayment_form').submit();
			}
			else
			{
				alert('Please select installment');
			}
			return;
		}
		else
		{
			ajaxLoader.show(); //display ajax loader in installment radio button
		}
	}
	else
	{
		var ajaxLoader = jQuery("#refresh-only-table img");
	}	
	var fullPaymentRadio = jQuery("#full-payment-radio");
	var installmentRadio = jQuery("#installment-radio");
	var fullPayment = jQuery("#full-payment");
	var installment = jQuery("#installment");
	var selectDisabled = 0;
	
	jQuery(".partialpayment .wait-loader").show();
	if(jQuery('select#allow_partial_payment').length){
		jQuery("#allow_partial_payment").prop("disabled", true);
		selectDisabled = 1;
	}
	partialPaymentDetailTable.addClass("_display"); //display partial payment calculation table
	if(flag==1 || (productId == '' && flag==2))
	{
		installment.addClass("_active"); // set active for installment radio
		fullPayment.removeClass("_active"); // remove active from full payment radio
	}
	//fetch price
	 var price='';
	 var priceElementId = 'price-including-tax-product-price-'+productId;
	 var priceExcludingTax='price-excluding-tax-product-price-'+productId;
	 
	 if(price == ''){
		price =jQuery('#product-price-'+productId ).text();	 
	 }
	 if(price=='')
	 {
	  price =jQuery('#' + priceExcludingTax).text();
	 }
	 if(price=='')
	 {
		 
		 price = jQuery('#'+priceElementId+ ' span.price').text();

	 }
	 
	 price = price.replace(currencySymbol, '');
	 price = price.replace(currencyCode, '');
	 price = price.replace(',', '');
	
	postParams = {"price":price, "productId":productId,"url":url,"currencySymbol":currencySymbol,"currencyCode":currencyCode};	
	if(numOfInstallment !== undefined){		
		if(jQuery('#allow_partial_payment').length){
			var numOfInstallment = jQuery('#allow_partial_payment').find(":selected").val();
		}else{
			var numOfInstallment = jQuery('#allow-partial-payment').find(":selected").val();
		}
		if(numOfInstallment < 1){
			isAjax = false;
			jQuery(".partialpayment .wait-loader").hide();			
			jQuery("#partial-payment-detail-table").html("");
			jQuery("#allow_partial_payment").prop("disabled", false);
		}	
		postParams = {"price":price, "productId":productId,"url":url,"currencySymbol":currencySymbol,"currencyCode":currencyCode,"numOfInstallment":numOfInstallment};
	}
	
	// call display calculation table
	if(isAjax){
		jQuery.ajax({
			   url:url, 
			   type: 'Post',
				dataType : 'json',
			   data: postParams
		})
		.done(function(transport) {
			if(flag=1)
			{
				ajaxLoader.hide(); // hide ajax loader from installment radio button
			}
			jQuery(".partialpayment .wait-loader").hide();
			if(selectDisabled){
				selectDisabled=0;
				jQuery("#allow_partial_payment").prop("disabled", false);
			}
			partialPaymentDetailTable.html(transport.html); // set calculation table
		})
	}
}

// this function called when click on Full Payment or clicked on close calculation table
function fullpaymentChecked(flag)
{
	/*
	here
		flag = 0 means close button of table is clicked
		flag = 1 means full payment radio is clicked
	*/
	jQuery("#partial-payment-detail-table").removeClass("_display");
	if(flag)
	{
		jQuery("#ajax-load").hide(); // hide ajax refresh
		jQuery("#full-payment").addClass("_active"); // set full payment as active
		jQuery("#installment").removeClass("_active"); // set installment payment as active
		jQuery("#full-payment .allow-partialpayment-radio").removeAttr("aria-invalid");
	}
}

//to display partial payment block on cart page
function showPartialPaymentOptions()
{
	jQuery(".partialpayment-wholecart-block .overlay").addClass("show");
}

//to hide partial payment block on cart page
function hidePartialPaymentOptions()
{
	jQuery(".partialpayment-wholecart-block .overlay").removeClass("show");
}