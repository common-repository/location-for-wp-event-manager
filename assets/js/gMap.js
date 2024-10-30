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
			draggable: true,
			title: "Drag me!"
		});
		
		google.maps.event.addListener(marker, 'dragend', function (e) {
			newCoordinates.lat = e.latLng.lat()
			newCoordinates.lng = e.latLng.lng()
			
			getAddress(newCoordinates);
		});
	}
	
	
	function getAddress(coordinates) {
		$('#_jwa_lat_location').val(coordinates.lat);
		$('#_jwa_lng_location').val(coordinates.lng);
		
		$.ajax({
			type: 'POST',
			url: 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + coordinates.lat + ',' + coordinates.lng + '&language=en&key=' + gKey.key,
			success: function (res) {
				
				$('#_jwa_address').val(res.results[0].formatted_address);
				
				// console.log('lat', $('#_jwa_lat_location').value, 'cod', coordinates.lat)
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log('error...', xhr);
				//error logging
			}
		});
	}
	
	if (map) {
		autocomplete = new google.maps.places.Autocomplete(inputs, {componentRestrictions: {country: 'ca'}});
		
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			marker.setVisible(false);
			
			places = autocomplete.getPlace();
			if (!places.geometry) {
				window.alert("Error");
				return;
			}
			if (places.geometry.viewport) {
				map.fitBounds(places.geometry.viewport);
			} else {
				window.alert("Error");
			}
			
			marker.setIcon(({
				url: places.icon,
				scaledSize: new google.maps.Size(35, 35)
			}));
			
			marker.setPosition(places.geometry.location);
			$('#_jwa_lat_location').val(places.geometry.location.lat());
			$('#_jwa_lng_location').val(places.geometry.location.lng());
			marker.setVisible(true);
			
			var address = '';
			if (places.address_components) {
				address = [
					(places.address_components[0] && places.address_components[0].short_name || ''),
					(places.address_components[1] && places.address_components[1].short_name || ''),
					(places.address_components[2] && places.address_components[2].short_name || '')
				].join(' ');
			}
			
		});
	}
	
});