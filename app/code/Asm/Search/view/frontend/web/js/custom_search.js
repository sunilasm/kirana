require([
"jquery",
"Magento_Ui/js/modal/modal"
], function($, modal){
    $(document).ready(function() {
         //alert("Hi, I am from custom_search.js");

        var latitudeAndLongitude=document.getElementById("lat"),
        location={
            latitude:'',
            longitude:''
        };

        if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showPosition);
        }
        else{
        latitudeAndLongitude.innerHTML="Geolocation is not supported by this browser.";
        }

        function showPosition(position){ 
            location.latitude=position.coords.latitude;
            location.longitude=position.coords.longitude;
            //document.getElementById("lat") = position.coords.latitude;
            //document.getElementById("lng") = position.coords.latitude;
            $("#lat").val(position.coords.latitude);
            $("#lng").val(position.coords.longitude);
            /*latitudeAndLongitude.="Latitude: " + position.coords.latitude + 
            "<br>Longitude: " + position.coords.longitude; */
            var geocoder = new google.maps.Geocoder();
            var latLng = new google.maps.LatLng(location.latitude, location.longitude);

        if (geocoder) {
            geocoder.geocode({ 'latLng': latLng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                console.log(results[0].formatted_address); 
                //$('#address').html('Address:'+results[0].formatted_address);
            }
            else {
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
                class: '',
                click: function () {
                    this.closeModal();
                    $('#popup-modal').html(" ");
                }
            }]
        };
        // Disallow location 
        if($("#lat").val() == '' && $("#lng").val() == ''){
                var popup = modal(options, $('#popup-modal'));
                $("#popup-modal").modal('openModal');
        }
        
        
    });
});