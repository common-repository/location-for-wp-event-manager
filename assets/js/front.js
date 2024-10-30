jQuery(document).ready(function ($) {
	let map, autocomplete, places;
	let inputs = document.querySelector('#_jwa_address');
	let startPoint = {
		lat: Number($('#_jwa_lat_location').val()),
		lng: Number($('#_jwa_lng_location').val()),
	}
	let uluru = {lat: 43.7184034, lng: -79.5184821};
	
	if (startPoint.lat) {
		uluru = startPoint;
	}
	
	let newCoordinates = {};
	if ($('#map').length > 0) {
		map = new google.maps.Map(document.getElementById("map"), {
			center: uluru,
			zoom: 10
		});
		
		let marker = new google.maps.Marker({
			position: uluru,
			map: map,
		});
		
		
	}
});