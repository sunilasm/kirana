require([
"jquery",
"Magento_Ui/js/modal/modal",
"jquery/jquery.cookie"
], function($, modal){
    $(document).ready(function() {
         //alert("Hi, I am from custom_search.js");
         
        var latitudeAndLongitude=document.getElementById("lat"),
        location={
            latitude:'',
            longitude:''
        };
        //var flag;
        if (navigator.geolocation){
                if($.cookie('latitude') == null && $.cookie('longitude') == null){
                    navigator.geolocation.getCurrentPosition(showPosition);
                }
        }
        else{
            //console.log("hererre");
        latitudeAndLongitude.innerHTML="Geolocation is not supported by this browser.";
        }

       

        function showPosition(position){ 
            location.latitude=position.coords.latitude;
            location.longitude=position.coords.longitude;
                $("#lat").val(position.coords.latitude);
                $("#lng").val(position.coords.longitude);
                var latitude = $('#lat').val();
                var longitude = $('#lng').val();
                
                 $.cookie('latitude', latitude );
                 $.cookie('longitude', longitude );
                 $.cookie('custmerloginstatus', 'false' );
                 console.log($.cookie('latitude')+'--'+$.cookie('longitude'));

            var geocoder = new google.maps.Geocoder();
            var latLng = new google.maps.LatLng(location.latitude, location.longitude);

            if (geocoder) {
                geocoder.geocode({ 'latLng': latLng}, function (results, status) {
                   // console.log(status);
                if (status == google.maps.GeocoderStatus.OK) {
                    //console.log(results[0].formatted_address); 
                    //flag = true;
                    //$('#address').html('Address:'+results[0].formatted_address);
                }
                else {
                    //flag = false;
                    //$('#address').html('Geocoding failed: '+status);
                    console.log("Geocoding failed: " + status);
                }
                }); //geocoder.geocode()
            }   


        } //showPosition
       // Customer is login 
        if(jQuery('#customerLogin').val()){
            //if($.cookie('latitude') == '' && $.cookie('longitude') == ''){
                var geocoder = new google.maps.Geocoder();
                var addressLog = jQuery('#addressFull').val();
                 var latLng = new google.maps.LatLng($.cookie('latitude'), $.cookie('longitude'));
                 geocoder.geocode( { 'latLng': latLng}, function(results, status) {
                       if (status == google.maps.GeocoderStatus.OK) {
                        $('#currentAdd').html(results[0].formatted_address);
                        $('#currentAddval').val(results[0].formatted_address);
                      }
                    });
                 // Get Default Logedin User Address
                geocoder.geocode( { 'address': addressLog}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $('#defaultAdd').html(addressLog);
                    $('#defaultAddval').val(addressLog);
                    addresslatitude =  results[0].geometry.location.lat();
                    addresslongitude = results[0].geometry.location.lng();
                    } 
                    console.log($.cookie('custmerloginstatus'));
                    if($.cookie('latitude') != addresslatitude && $.cookie('longitude') != addresslongitude && $.cookie('custmerloginstatus') == 'false'){
                        var popup = modal(optionsnew, $('#popup-modal-islogin'));
                            $('#popup-modal-islogin').modal('openModal');
                    }else{
                        $("#lat").val(addresslatitude);
                        $("#lng").val(addresslongitude); 
                    }   
                });
             //}
        }
        // Rgister user or non registered user popup
         var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Address',
                modalClass: 'custom-modal',
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: 'getaddressdetails',
                    click: function () {
                        var address = jQuery('#autocomplete').val();
                           var geocoder = new google.maps.Geocoder();
                            //var address = "new york";

                            geocoder.geocode( { 'address': address}, function(results, status) {
                              //console.log('status-->'+status);
                               if (status == google.maps.GeocoderStatus.OK) {
                                var latitude = results[0].geometry.location.lat();
                                var longitude = results[0].geometry.location.lng();
                                jQuery('#lat').val(latitude);
                                jQuery('#lng').val(longitude);
                              }
                            });
                        this.closeModal();
                        $('#popup-modal').html(" ");
                    }
                }]
            };

            // Registered user popup
            var optionsnew = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Confirm Address',
                modalClass: 'custom-modal-login',
                buttons: [{
                    text: $.mage.__('Confirm'),
                    class: 'getaddressdetailslogin',
                    click: function () {
                        //alert($("input[name='checkaddress']:checked").val());
                        var geocoder = new google.maps.Geocoder();
                            var address = $("input[name='checkaddress']:checked").val();

                            geocoder.geocode( { 'address': address}, function(results, status) {
                               if (status == google.maps.GeocoderStatus.OK) {
                                var latitude = results[0].geometry.location.lat();
                                var longitude = results[0].geometry.location.lng();
                                 $.cookie('latitude', latitude );
                                 $.cookie('longitude', longitude );
                                 $.cookie('custmerloginstatus', 'true' );
                                jQuery('#lat').val(latitude);
                                jQuery('#lng').val(longitude);
                              }
                            });
                        this.closeModal();
                        $('#popup-modal-islogin').html(" ");
                    }
                }]
            };
 // alert($("#lat").val()+'--'+$("#lng").val()+'--'+$.cookie('latitude')+'--'+$.cookie('longitude'));
            if($("#lat").val() == '' && $("#lng").val() == '' && $.cookie('latitude') == null && $.cookie('longitude') == null){
                    //$('#popup-modal').removeClass('hidden')
                    //alert('kk');
                    var popup = modal(options, $('#popup-modal'));
                    setTimeout(function() {
                    $('#popup-modal').modal('openModal');
                    }, 5000);

                   // $('#popup-modal').modal('openModal');
                    //$("#popup-modal").modal('openModal');
            }   
        
    });
});