<?php
$centerpointLang = 73.77836839999999;
$centerpointLat = 18.564684;

$checkPointLang = 73.77577;
$checkPointLat = 18.558386;

$km=1;

$ky = 40000 / 360;
$kx = cos(pi() * $centerpointLat / 180.0) * $ky;
$dx = abs($centerpointLang - $checkPointLang) * $kx;
$dy = abs($centerpointLat - $checkPointLat) * $ky;
$res = sqrt($dx * $dx + $dy * $dy) <= $km;

echo "here--".$res;
 ?>

<!-- Get Current location -->
        <button onclick="getLocation()">Try It</button>
        <p id="demo"></p>
        <script>
        var x = document.getElementById("demo");
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else { 
                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {
            x.innerHTML = "Latitude: " + position.coords.latitude + 
            "<br>Longitude: " + position.coords.longitude;
        }
        </script>
        <!-- End Get Current location -->

<?php
exit;
            
/*

function areaPointsNear(checkPoint, centerPoint, km) {
var ky = 40000 / 360;
var kx = Math.cos(Math.PI * centerPoint.lat / 180.0) * ky;
var dx = Math.abs(centerPoint.lng - checkPoint.lng) * kx;
var dy = Math.abs(centerPoint.lat - checkPoint.lat) * ky;
return Math.sqrt(dx * dx + dy * dy) <= km;
}
var nearpoint = { lat: 18.4461598, lng: 73.82549139999992 };

var currentpoint = { lat: 18.5596801, lng: 73.7904919 };
var n = areaPointsNear(nearpoint, currentpoint, 10);
alert(n);
console.log(n);
*/
?>