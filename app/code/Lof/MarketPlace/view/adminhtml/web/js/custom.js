require([
  'jquery',
  'mage/url',
  'jquery/ui'  
], function($,url){
   var linkUrl = url.build('geolocation/index');
  console.log(linkUrl);
   	$( document ).ready(function() {
   		var shoptime = $("#seller_24by7_shop").val();
    	if(shoptime == "Yes"){
	   		$(".field-opening_time").show();
	  		$(".field-closeing_time").show();
	   	}else{
	   		$(".field-opening_time").hide();
	  		$(".field-closeing_time").hide();
	   	} 
	   	var shore = $("#seller_parent_store").val();
    	if(shore == "Yes"){
	   		$(".field-parent_store_id").show();
	   	}else{
	   		$(".field-parent_store_id").hide();
	   	} 
	   	
	}); 
   $(document).on('change', '#seller_24by7_shop', function() {
	   	if(this.value == "Yes"){
	   		$(".field-opening_time").show();
	  		$(".field-closeing_time").show();
	   	}else{
	   		$(".field-opening_time").hide();
	  		$(".field-closeing_time").hide();
	   	}  		
  	});  	

	$(document).on('focusout', '#seller_postcode', function() {
	var address = $('#seller_address').val();
	var city = $('#seller_city').val();
	var state = $('#seller_region').val();
	var country = $('#seller_country').val();
	var postcode = $('#seller_postcode').val();
	var base_url = window.location.origin;
	var host = window.location.host;
	var pathArray = window.location.pathname.split( '/admin_lirbuz' );
	var param = 'address='+address+'&city='+city+'&state='+state+'&country='+country+'&pincode='+postcode;
	$.ajax({
            showLoader: true,
            url: base_url+''+pathArray[0]+'/geolocation/index',
            data: param,
            type: "POST",
            dataType: 'json',
		}).done(function (data) {
            if(data.status == 'success')
			{
				$('#seller_geo_lat').val(data.geo.lat);
				$('#seller_geo_lng').val(data.geo.lng);
			}
			else
			{
				console.log("Geolocatiopn failed. :"+data.status+":"+ data.message);
			}
		}).fail(function (jqXhr) {
			console.log(jqXhr.responseText);
		});
	});
}); 