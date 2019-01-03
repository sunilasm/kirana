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
        navigator.geolocation.getCurrentPosition(showPosition);
        }
        else{
            //console.log("hererre");
        latitudeAndLongitude.innerHTML="Geolocation is not supported by this browser.";
        }

       

        function showPosition(position){ 
            location.latitude=position.coords.latitude;
            location.longitude=position.coords.longitude;
            //document.getElementById("lat") = position.coords.latitude;
            //document.getElementById("lng") = position.coords.latitude;
                $("#lat").val(position.coords.latitude);
                $("#lng").val(position.coords.longitude);

                var lat = $('#lat').val();
                var lng = $('#lng').val();
                //console.log(lat+'--'+lng);
                 $.cookie('latnew', lat );
                 $.cookie('lngnew', lng );

                 //console.log('hhhh--'+$.cookie('latnew'));

            /*latitudeAndLongitude.="Latitude: " + position.coords.latitude + 
            "<br>Longitude: " + position.coords.longitude; */
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
        
         var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Modal Title',
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
            // Disallow location 
            //console.log("flag-->"+$.cookie('latnew'));
            if($("#lat").val() == '' && $("#lng").val() == '' && $.cookie('latnew') == '' && $.cookie('lngnew') == ''){
                    //$('#popup-modal').removeClass('hidden')
                    var popup = modal(options, $('#popup-modal'));
                    setTimeout(function() {
                    $('#popup-modal').modal('openModal');
                    }, 5000);

                   // $('#popup-modal').modal('openModal');
                    //$("#popup-modal").modal('openModal');
            }   
        
    });
});