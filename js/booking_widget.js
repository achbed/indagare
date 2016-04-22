jQuery().ready(function($) {
	var thisValue;

    var field = $('input#book-destination');
		field.click(function() {//Empty the field on focus
			 thisValue = $(this).val();
			$(this).attr("value","");
		});

		field.blur(function() {//Check the field if it is left empty
			fieldlength = $(this).val();
			if($(this).val()=="" || fieldlength.length <= 4) {
			$(this).val(thisValue);
			}
    });

	$("#dep_date").datepicker({
		dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
		dateFormat: 'mm/dd/yy',
		numberOfMonths: 1,
		minDate: +1,
		onSelect: function(dateText, inst) {
			var date = $.datepicker.parseDate('mm/dd/yy', dateText);
			date.setDate(date.getDate() + 1);
		
			var $ret_date = $("#ret_date");
		
			$ret_date.datepicker("setDate", date);
			$ret_date.datepicker("option", "minDate", date);
		},
	
		onClose: function() {
		
			if ($('#dep_date').val()) {
			  $('#dep_date').addClass('has-data');
			} else {
			  $('#dep_date').removeClass('has-data');
			}
		
			if ($('#ret_date').val()) {
			  $('#ret_date').addClass('has-data');
			} else {
			  $('#ret_date').removeClass('has-data');
			}
		}
	});

	$("#ret_date").datepicker({
		dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
		dateFormat: 'mm/dd/yy',
		numberOfMonths: 1,
		onClose: function() {
		
			if ($('#dep_date').val()) {
			  $('#dep_date').addClass('has-data');
			} else {
			  $('#dep_date').removeClass('has-data');
			}
		
			if ($('#ret_date').val()) {
			  $('#ret_date').addClass('has-data');
			} else {
			  $('#ret_date').removeClass('has-data');
			}
		}
	});
	
	// build booking url
	$('#book-hotels .button').click(function(event) {

		event.preventDefault();

		destinationparse = '';
		baseurl = swifttripurl+'/do/hotel/';
		departval = $('#dep_date').val();
		returnval = $('#ret_date').val();
		departurl = '';
		returnurl = '';
		
		if ( $('.autocompletedestination').val() ) {
			destinationparse = $('.autocompletedestination').val();
			destination = destinationparse.split(',');
		}
		
		if ( !!destination.length ) {
			destinationid = destination[0];
			destinationtype = destination[1];
		}
		
		if ( destinationtype == 'destination' ) {
			basebooking = 'ListByDestination?destinationId=' + destinationid;		
		} else if ( destinationtype == 'hotel' ) { 
			basebooking = 'CheckHotelAvailability?pc=' + destinationid + '&gdsType=sabre';
		}
		
		ssotoken = '&ssoToken=' + ssotokenvalue;
		
		if ( !!departval.length ) {
			departurl = '&cin=' + departval;
		}
		
		if ( !!returnval.length ) {
			returnurl = '&cout=' + returnval;
		}
		
		if ( !!basebooking.length ) {
			bookingurl = baseurl + basebooking + ssotoken + departurl + returnurl;

			if(ssotokenvalue != ssotokenvalue_default) {
				window.location.href = bookingurl;
			} else {
				$.magnificPopup.open({
				  items: {
					type: 'inline',
					src: '#lightbox-interstitial', // can be a HTML string, jQuery object, or CSS selector
					midClick: true
				  },
				});
			}
		}
		
	}); // end build booking url
	
	// build flight url
	$('#bookflights').click(function(event) {

		event.preventDefault();

		destinationparse = '';
		baseurl = swifttripurl+'/do/flight/RoundTripSearch?';

		ssotoken = '&ssoToken=' + ssotokenvalue;
		bookingurl = baseurl + ssotoken;

		if(ssotokenvalue != ssotokenvalue_default) {
			window.location.href = bookingurl;
		} else {
			$.magnificPopup.open({
			  items: {
				type: 'inline',
				src: '#lightbox-interstitial-flights', // can be a HTML string, jQuery object, or CSS selector
				midClick: true
			  },
			});
		}
	}); // end build flight url

	// book as guest
	$('.book-interstitial .button').click(function(event) {
	
		event.preventDefault();
		
		window.location.href = bookingurl;
	
	}); // end book as guest
	
	// book as user
	$("#form-interstitial").submit(function(event) {

		var url = theme_path+'/process_login_ajax.php';

		$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#form-interstitial").serialize(),
			   success: function(data)
			   {
			   	   var json = $.parseJSON(data);
			   	   
				   if ( json.login == true ) {
				   
				   		ssotoken = '&ssoToken=' + json.ssotoken;
				   		
				   		bookingurl = baseurl + basebooking + ssotoken + departurl + returnurl;
				   
						window.location.href = bookingurl;

				   } else {

				   		$('#form-interstitial .message').html('<p>Incorrect login - please try again</p>').fadeIn(1500).fadeOut(1500);
				   }
			   }
			 });

		event.preventDefault();
	
	});

	$("#form-interstitial-flights").submit(function(event) {

		var url = theme_path+'/process_login_ajax.php';

		$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#form-interstitial-flights").serialize(),
			   success: function(data)
			   {
			   	   var json = $.parseJSON(data);
			   	   
				   if ( json.login == true ) {
				   
				   		ssotoken = '&ssoToken=' + json.ssotoken;
				   		
				   		bookingurl = baseurl + ssotoken;
				   
						window.location.href = bookingurl;

				   } else {

				   		$('#form-interstitial-flights .message').html('<p>Incorrect login - please try again</p>').fadeIn(1500).fadeOut(1500);
				   }
			   }
			 });

		event.preventDefault();
	
	});	// end book as user	
});
