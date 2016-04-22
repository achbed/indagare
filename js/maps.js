function processMarkers(mapDataArray) {
	for(var z=0;z<mapDataArray.length;z++) {
		var data = mapDataArray[z];
		var mm = {};
		
		mm.i = new google.maps.InfoWindow({ content: data.title });
		mm.ll = new google.maps.LatLng( data.coord );
	  bounds.extend(mm.ll);
	  mm.m = new google.maps.Marker({ position: mm.ll, map: map, title: data.title });
	  google.maps.event.addListener(mm.m, 'click', function() { mm.i.open(map, mm.m ); });
	  mapMarkerInstances.push(mm);
	}
}

function createMarker(options) {
  var defaults = {
		    title: '',
		    content: '',
		    coordinates: '0,0',
		    type: 'feature',
		    show: 0,
		    zmin: 0
  };
  options = jQuery.extend(defaults, options);

  latlng = options.coordinates.split(",");
  var placeLoc = new google.maps.LatLng(latlng[0], latlng[1]);
  var zIndex = false;
  var iconUrl = theme_path + '/images/';
  if (options.type == "hotel") {
    iconUrl += "nearby-icon-hotel.png";
  } else if (options.type == "restaurant") {
    iconUrl += "nearby-icon-restaurant.png";
  } else if (options.type == "shop") {
    iconUrl += "nearby-icon-shop.png";
  } else if (options.type == "activity") {
    iconUrl += "nearby-icon-activity.png";
  } else {
	iconUrl += 'map-icon.png';
	zIndex = lat_to_zindexoffset(latlng[0]) - options.zmin + 7000;
  }

  var markerargs = {};
  markerargs.position = placeLoc;
  markerargs.icon = iconUrl;
  if(zIndex !== false)
	  markerargs.zIndex = zIndex;
  if(options.title != '')
	  markerargs.title = options.title;
  
  var marker = new google.maps.Marker(markerargs);
  marker.set('postid',options.id);
  marker.set('showAtOpen',(options.show == 1));

  if (options.type == "hotel") {
	  Hotel.push(marker);
  } else if (options.type == "restaurant") {
	  Restaurant.push(marker);
  } else if (options.type == "shop") {
	  Shop.push(marker);
  } else if (options.type == "activity") {
	  Activity.push(marker);
  } else {
	  OtherMarkers.push(marker);
  }

  var w = new google.maps.InfoWindow({content: options.content, maxWidth: 150});
  w.set('isOpen',false);
  w.set('postid',options.id);
  google.maps.event.addListener(w, "domready", function(id) {
	  return function() {
		  var p = jQuery('#markercontent-'+id).parent().parent().parent().parent();
		  p.addClass('infoWindow').attr('postid',id);
		  p.click(function() {
			  setFirstPopup(jQuery(this).attr('postid'));
		  });
	  };
  }(options.id));
  google.maps.event.addListener(w, "closeclick", function(ww,id){
	  return function() {
		  for(var x=0;x<openPopups.length;x++) {
			  if(openPopups[x].get('postid') == id) {
				  openPopups[x].set('isOpen',false);
				  openPopups.splice(x,1);
				  setPopupIndexes();
				  return;
			  }
		  }
	  };
  }(w,options.id));
  infowindows.push(w);
  
  google.maps.event.addListener(marker, "click", function(themap, marker, ww) {
		return function() {
			if(ww.get('isOpen')) return;
			if(openPopups.length >= maxpopups) {
				var x = openPopups.shift();
				x.close();
				x.set('isOpen',false);
			}
			ww.open(themap, marker);
			ww.set('isOpen',true);
			openPopups.push(ww);
			setPopupIndexes();
		};
  }(options.map, marker, w));

  return placeLoc;
}// function createMarker()

function setFirstPopup(id) {
	if(!openPopups) return;
	if(openPopups.length <= 0) return;
	var t = null;
	for (var i = 0; i < openPopups.length; i++) {
		if(openPopups[i].get('postid') == id ) {
			t = i;
			i = openPopups.length;
		}
	}
	var x = openPopups.splice(t,1);
	openPopups.push(x[0]);
	setPopupIndexes();
}

function setPopupIndexes() {
	if(!openPopups) return;
	if(openPopups.length <= 0) return;
	var offset = 9000;
	for (var i = 0; i < openPopups.length; i++) {
		openPopups[i].setZIndex(offset+(i* 10));
	}
}

function closeAllPopups() {
	if(!openPopups) return;
	if(openPopups.length <= 0) return;
	while(openPopups.length) {
		var x = openPopups.shift();
		x.close();
		x.set('isOpen',false);
	}
}

function ToggleLocales(checkbox, localDestType) {
  localDestType = eval(localDestType);
  //				console.log ( 'ToggleLocales ' + localDestType );

  if (jQuery(checkbox).is(":checked")) {
    SetMarkers(localDestType);
  } else {
    RemoveMarkers(localDestType);
  }
}//function ToggleLocales()

function SetMarkers(localDestType) {
  localDestType = eval(localDestType);
  for (var i = 0; i < localDestType.length; i++) {
    //					console.log ( 'on: ' + localDestType.length );
	  var isOpen = false;
	  for (var x = 0; x < openPopups.length; x++) {
		  if(openPopups[x].get('postid') == localDestType[i].get('postid')) {
			  isOpen = openPopups[x].get('isOpen');
		  }
	  }
	  if(!isOpen)
		  localDestType[i].setMap(map);
  }
}//function SetMarkers()

function RemoveMarkers(localDestType) {
  localDestType = eval(localDestType);
  for (var i = 0; i < localDestType.length; i++) {
    //					console.log ( 'off: ' + localDestType.length );
	  var isOpen = false;
	  for (var x = 0; x < openPopups.length; x++) {
		  if(openPopups[x].get('postid') == localDestType[i].get('postid')) {
			  isOpen = openPopups[x].get('isOpen');
		  }
	  }
	  if(!isOpen)
		  localDestType[i].setMap(null);
  }
}//function RemoveMarkers()

jQuery(document).ready(function($) {
  $(".poicategory").each(function(index, element) {
    var poiarray = $(this).val();
    //				console.log ( $(this) );

    $(this).change(function() {
      //					console.log ( $(this).val() );
      ToggleLocales($(this), $(this).val());
    });
  });

  // show map on nearby link click if it is not visible
  $('.togglelayer input').click(function() {
    if ($('#mapcanvas').hasClass('show-this')) {
    } else {
      $('p.view-more a.map').click();
    }
  });

  $('#Hotel').click(function() {
    $('#Hotel input').click();
  });
  $('#Restaurant').click(function() {
    $('#Restaurant input').click();
  });
  $('#Shop').click(function() {
    $('#Shop input').click();
  });
  $('#Activity').click(function() {
    $('#Activity input').click();
  });

  $('#Hotel input').click(function() {
    $('#Hotel').toggleClass('toggleactive');
  });
  $('#Restaurant input').click(function() {
    $('#Restaurant').toggleClass('toggleactive');
  });
  $('#Shop input').click(function() {
    $('#Shop').toggleClass('toggleactive');
  });
  $('#Activity input').click(function() {
    $('#Activity').toggleClass('toggleactive');
  });

});

function maplocations_initialize() {
	var marker, i;
	var minZoomLevel = 2;

	var myLatlng = new google.maps.LatLng(0,0);

	var myStyles =[
		{
			featureType: "poi.business",
			elementType: "labels",
			stylers: [
				  { visibility: "on" }
			]
		}
	];

    var styledMap = new google.maps.StyledMapType(myStyles,
    {name: "Styled Map"});

	var myOptions = {
	  zoom: 15,
	  minZoom: minZoomLevel,
	  center: myLatlng,
	  mapTypeControlOptions: {
		mapTypeIds: [google.maps.MapTypeId.ROADMAP,google.maps.MapTypeId.SATELLITE]
	  }
	};

  var markerImage = theme_path+'/images/map-icon.png';

	mapheader = new google.maps.Map(document.getElementById("mapcanvas"), myOptions);
	
	mapheader.mapTypes.set('mapstyle', styledMap);
	mapheader.setMapTypeId('mapstyle');

	var bounds = new google.maps.LatLngBounds();
	
	j=0;
	
	var infowindow = new google.maps.InfoWindow();

	for (i = 0; i < markers.length; i++) {  

		bounds.extend(new google.maps.LatLng(markers[i][1], markers[i][2]));

		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(markers[i][1], markers[i][2]),
			map: mapheader,
			title: markers[i][0],
			icon: markerImage
		});
		
		marker.set("class", markers[i][4]);
		content = "<div class=\"markercontent\">";
		content += "<h3>";
		content += "<a class=\"more\" href=\"" + markers[i][3] + "\">";
		content += markers[i][0];
		content += "</a></h3></div>";
		
		google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
			return function() {
				infowindow.close();
				infowindow.setContent(content);
				infowindow.open(mapheader,marker);
			};
		})(marker,content,infowindow));

	   markersArray.push(marker);

		j++;
		
	}
	
	mapheader.fitBounds(bounds);
} // end initialize()

function should_show_marker(m, args) {
	var d, c, a, i, f;
	
	c = m['class'];
	a = c.split( ' ' );
	
	if( args.region == 'all' ) {
		d = args.destination;
	} else {
		d = args.region;
	}
	
	if ( d != 'all' ) {
		// Only filter by destination if we're not looking for "all"
		if ( jQuery.inArray( d, a ) == -1 ) {
			// We're not in the destination list
			return false;
		}
	}
	
	if( filterArray(args.interests, a, true) == false) {
		return false;
	}
	if( filterArray(args.seasons, a, false) == false) {
		return false;
	}

	return true;
}

// update markers and zoom map
function markersdisplay( args ) {
	var c, b, i, m, l;
	c = 0;
	b = new google.maps.LatLngBounds();
	
	for (i = 0; i < markersArray.length; i++) {  
		m = markersArray[i];
		
		if ( should_show_marker( m, args ) ) {
			// We match the filters
			m.setMap(mapheader);
			//var l = new google.maps.LatLng(m.position.lat(), m.position.lng());
			b.extend( m.position );
			c++;
		} else {
			m.setMap( null );
		}
	}
	
	mapheader.fitBounds( b );
	
	if ( c == 1 ) {
		mapheader.setZoom( 8 );
	}
}

var map_marker_list = [];

function gmap_initialize() {
	var t = jQuery('#mapmarkerjson').text();
	t = t.replace('&lt;','<');
	map_marker_list = jQuery.parseJSON(t);
	
	infowindow = new google.maps.InfoWindow();
    if (map_marker_list.length <= 0) {
    	// no markers, no map
	    jQuery('.detail p.view-more').hide();
	    return;
    }
    
	var myLatlng = new google.maps.LatLng(0,0);
	var mapDataArray = new Array();
	
	var myStyles = [
		{
			featureType: "poi.business",
			elementType: "labels",
			stylers: [
				{ visibility: "off" }
			]
		}
	];
	
	var styledMap = new google.maps.StyledMapType(myStyles, {name: "Styled Map"});
	
	var myOptions = {
		zoom: 15,
		center: myLatlng,
		mapTypeControlOptions: {
			mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'mapstyle']
		}
	};
	map = new google.maps.Map(document.getElementById("mapcanvas"), myOptions);
	
	map.mapTypes.set('mapstyle', styledMap);
	map.setMapTypeId('mapstyle');
	
	//setTimeout(function() {
		gmap_loadmarkers();
	//}, 2500); 
}

function lat_to_zindexoffset(lat) {
	return parseInt( (180 - ( +(lat) + 90 ) ) * 100000 );
}

function popup_openindex(id) {
	for(var i=0;i<openPopups.length;i++){
		if( openPopups[x].get('postid') == id ) {
			return i;
		}
	}
	return -1;
}

var _loadedmarkers = false;

function gmap_loadmarkers() {
	//if(_loadedmarkers)
	//	return;
	if(!_loadedmarkers) {
		_loadedmarkers=true;
		if ( map_marker_list.length <= 0 ) return;
		
		var latlng = map_marker_list[0].coordinates.split(",");
		var lat = lat_to_zindexoffset(latlng[0]);
		var min = lat;
		for(var i=1;i<map_marker_list.length;i++) {
			latlng = map_marker_list[i].coordinates.split(",");
			lat = lat_to_zindexoffset(latlng[0]);
			if(lat < min) min = lat;
		}
		
		for(var i=0;i<map_marker_list.length;i++) {
			var k = map_marker_list[i];
			k.zmin = min;
			k.map = map;
			createMarker(k);
		}
	}

	goZoom();
	/*
	//zoomChangeBoundsListener = google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
	var bounds = new google.maps.LatLngBounds();
	var markercount = 0;

	for (var i = 0; i < Hotel.length; i++) {
		if(Hotel[i].get('showAtOpen')) {
			Hotel[i].setMap(map);
			bounds.extend(Hotel[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Restaurant.length; i++) {
		if(Restaurant[i].get('showAtOpen')) {
			Restaurant[i].setMap(map);
			bounds.extend(Restaurant[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Shop.length; i++) {
		if(Shop[i].get('showAtOpen')) {
			Shop[i].setMap(map);
			bounds.extend(Shop[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Activity.length; i++) {
		if(Activity[i].get('showAtOpen')) {
			Activity[i].setMap(map);
			bounds.extend(Activity[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < OtherMarkers.length; i++) {
		if(OtherMarkers[i].get('showAtOpen')) {
			OtherMarkers[i].setMap(map);
			bounds.extend(OtherMarkers[i].getPosition());
			markercount++;
		}
	}
	
	zoomChangeBoundsListener = google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
		google.maps.event.removeListener(zoomChangeBoundsListener);
		if ( markercount == 1 )
			this.setZoom(15);
	});
	
	map.fitBounds(bounds);
		//if(OtherMarkers.length == 1)
			//this.setZoom(15);
	//});
	
	//setTimeout(function() {
	//	google.maps.event.removeListener(zoomChangeBoundsListener);
	//}, 2000);
	*/
}

function goZoom() {
	var bounds = new google.maps.LatLngBounds();
	var markercount = 0;
	
	for (var i = 0; i < Hotel.length; i++) {
		if(Hotel[i].get('showAtOpen')) {
			Hotel[i].setMap(map);
			bounds.extend(Hotel[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Restaurant.length; i++) {
		if(Restaurant[i].get('showAtOpen')) {
			Restaurant[i].setMap(map);
			bounds.extend(Restaurant[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Shop.length; i++) {
		if(Shop[i].get('showAtOpen')) {
			Shop[i].setMap(map);
			bounds.extend(Shop[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < Activity.length; i++) {
		if(Activity[i].get('showAtOpen')) {
			Activity[i].setMap(map);
			bounds.extend(Activity[i].getPosition());
			markercount++;
		}
	}
	
	for (var i = 0; i < OtherMarkers.length; i++) {
		if(OtherMarkers[i].get('showAtOpen')) {
			OtherMarkers[i].setMap(map);
			bounds.extend(OtherMarkers[i].getPosition());
			markercount++;
		}
	}
	
	for(var x=0;x<openPopups.length;x++) {
		if(openPopups[x].get('isOpen') == true) {
			bounds.extend(openPopups[x].getPosition());
			markercount++;
		}
	}
	
	zoomChangeBoundsListener = google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
		google.maps.event.removeListener(zoomChangeBoundsListener);
		if ( markercount == 1 )
			this.setZoom(15);
	});
	
	map.fitBounds(bounds);
	return false;
}

if(typeof filterArray !== 'function') {
	window.filterArray = function(needle, haystack, all) {
		var m,x,y,a,b;
		if (needle.length == 0) {
			return true;
		}
		
		if(haystack.length == 0) {
			return false;
		}
		
		m = 0;
		for( x = 0; x < needle.length; x++ ) {
			a = needle[x];
			if(a === Object(a)) {
				if("value" in a) {
					a = a.value();
				} else {
					a = a.toString();
				}
			}
			for( y = 0; y < haystack.length; y++ ) {
				b = haystack[y];
				if(b === Object(b)) {
					if("value" in b) {
						b = b.value();
					} else {
						b = b.toString();
					}
				}
				if( a == b ) {
					if(!all) {
						return true;
					}
					m++;
				}
			}
		}
		
		return (m == needle.length);
	};
}