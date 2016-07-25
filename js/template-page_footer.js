// clear booking widgets on page unload
window.onunload = function() { var e=document.getElementById("book-hotels"); if(e) e.reset(); };

jQuery().ready(function($) {
	// Menu behavior
	adjustMenu();

	// Magnific Popup
	$('.lightbox-inline').magnificPopup({
		type:'inline',
		midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
	});
	
	$('.lightbox-ajax').magnificPopup({
		type:'inline',
		midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
	});
	
	//Mobile navigation
	$('#menu-show-hide a').click(function() {
		$('.collapsible').toggleClass('show-this');
		$(this).toggleClass('close');
		return false;
	});
	//Primary navigation
	/*
	$("#nav > li > a").click(function (e) { // binding onclick
		if ($(this).parent().hasClass('open')) {
			$("#nav .open .subnav").removeClass('show-this'); // hiding popups 
			$("#nav .open").removeClass("open");
		} else {
			$("#nav .open .subnav").removeClass('show-this'); // hiding popups
			$("#nav .open").removeClass("open");
			if ($(this).next(".subnav").length) {
				$(this).parent().addClass("open"); // display popup
				$(this).next(".subnav").addClass('show-this');
			}
		}
		e.stopPropagation();
	});
	*/

	//Magazine navigation
	$("#subnav-magazine > li > a").click(function (e) { // binding onclick
		if ($(this).parent().hasClass('open')) {
			$("#subnav-magazine .open .subnav").removeClass('show-this'); // hiding popups 
			$("#subnav-magazine .open").removeClass("open");
		} else {
			$("#subnav-magazine .open .subnav").removeClass('show-this'); // hiding popups
			$("#subnav-magazine .open").removeClass("open");
			if ($(this).next(".subnav").length) {
				$(this).parent().addClass("open"); // display popup
				$(this).next(".subnav").addClass('show-this');
			}
		}
		e.stopPropagation();
	});

	//Magazine filter
	$(".button.filters").click(function (e) { // binding onclick
		if ($(this).parent().hasClass('open')) {
			$("#magazine-filters").removeClass('show-this'); // hiding popups 
			$(".header.filter").removeClass("open");
		} else {
			$("#subnav-magazine .open .subnav").removeClass('show-this'); // hiding popups 
			$("#subnav-magazine .open").removeClass("open");
			if ($(this).next("#magazine-filters").length) {
				$(this).parent().addClass("open"); // display popup
				$(this).next("#magazine-filters").addClass('show-this');
			}
		}
		e.stopPropagation();
	});
	
	//show map
	$('p.view-more a.map').click(function() {
		$('#mapcanvas').toggleClass('show-this');
		if ( $('#mapcanvas').hasClass('show-this') ) {
			$('.showmap').val('show');
			$('#gallery-header').hide();
			$('.detail .view-more a.map').text('Show Images');
			$('.archive .view-more a.map').text('Hide Map');
			$('#mapcanvas').parent().addClass('show-map');
			gmap_loadmarkers();
		} else {
			$('.showmap').val('');
			$('#gallery-header').show();
			$('.detail .view-more a.map').text('Show Map');
			$('.archive .view-more a.map').text('Show Map');
			$('#mapcanvas').parent().removeClass('show-map');
		}
		return false;
	});

	// review overview page loads with map open | check to make sure there are items to draw on the map
	
	if ( $('.showmap').val() && itemcount > 0 ) {
		$('#mapcanvas').addClass('show-this');
		$('.detail .view-more a.map').text('Show Images');
		$('.archive .view-more a.map').text('Hide Map');
		$('#mapcanvas').parent().addClass('show-map');
		gmap_loadmarkers();
	}
	
	$('#map-modal-toggle').click(function(event) {
		event.preventDefault();
		$('body').toggleClass('modalmap');
		$('body #map-modal-toggle').attr('title','Full Screen').html('Full Screen');
		$('body.modalmap #map-modal-toggle').attr('title','Close Map').html('Close Map');
		google.maps.event.trigger(map, 'resize');
		goZoom();
	});

	$('#filters p.open-close a').click(function(event) { event.preventDefault(); $('#filters').toggleClass('show-this'); });

	$('.filters p.open-close a:first-child').click(function(event) { event.preventDefault(); $(this).parent().parent().toggleClass('show-this'); });

	$('#comments p.open-close a').click(function(event) { event.preventDefault(); $('#comments').toggleClass('show-this'); });

	$('#comments p.form-submit').addClass('buttons');
	$('#comments #submit').addClass('button primary');

	$('#searchform .icon').click(function(){
		searchvars = $('#search-site').val();
		if (!!searchvars.length ) {
			$('#searchform').submit();
		}
	});

//	$('input#book-destination,input#dep_date,input#ret_date').keypress(function(event) { return event.keyCode != 13; });
	

	$('.newsletter-signup-form').submit(function(event) {

		event.preventDefault();
	
		var wrapper = $(this).closest(".newsletter-signup-wrapper");
		var wrapperID = wrapper.attr('id');
		
//		console.log ( wrapperID );
		
		if (!wrapper.find('.newsletter-signup-input').val().match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) {
			wrapper.find('p').fadeOut().fadeIn().text('Please enter a valid email address to sign up for our email newsletter.');
			return false;
		}
	
/*
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange=function(){
//			console.log(xmlhttp.responseText);

			if (xmlhttp.readyState==4 && xmlhttp.status==200 && xmlhttp.responseText == 'true'){
				wrapper.find('h2').fadeOut().fadeIn().text('Thank you');
				if ( wrapperID == 'first' || wrapperID == 'emailsignup' ) {
					wrapper.find('p').fadeOut().fadeIn().text('Thank you for signing up.');
					setTimeout(function() {
						$.magnificPopup.close();
					}, 3500);
				} else if ( wrapperID == 'form-buzz' ) {
					wrapper.find('p').fadeOut().fadeIn().text('Indagare\'s e-Newsletter, full of travel buzz, is sent out every other week.');
					$('#'+wrapperID).delay(1500).slideUp();
				}
			}

			return false;
		};
		xmlhttp.open("POST",theme_path+"/app/lib/iajax_newsletter.php?task=newsletter_signup",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		var posts = "email=" + encodeURI(wrapper.find('.newsletter-signup-input').val());
		xmlhttp.send(posts);

*/

            $.ajax({
//                type: "GET",
//                url: "//indagare.us1.list-manage.com/subscribe/post-json?u=2af8f3ad067b68a1d10009a2f&amp;id=4962a21673",
                type: "POST",
                url: "https://us1.api.mailchimp.com/3.0/lists/4962a21673/members/",
                data: $('form.newsletter-signup-form').serialize(),
                cache: false,
                dataType: "jsonp",
                jsonp: "c", // trigger MailChimp to return a JSONP response
                contentType: "application/json; charset=utf-8",
                error: function(error){
                    // According to jquery docs, this is never called for cross-domain JSONP requests
                },
                success: function(data){
                    if (data.result != "success") {
                        var message = data.msg || "Sorry. Unable to subscribe. Please try again later.";
//                        $resultElement.css("color", "red");
                        if (data.msg && data.msg.indexOf("already subscribed") >= 0) {
                            message = "You're already subscribed. Thank you.";
//                            $resultElement.css("color", "black");
                        }
//                        $resultElement.html(message);
						wrapper.find('p').fadeOut().fadeIn().text(message);
                    } else {
//                        $resultElement.css("color", "black");
//                        $resultElement.html("Thank you!<br>You must confirm the subscription in your inbox.");
						wrapper.find('h2').fadeOut().fadeIn().text('Thank you');
						if ( wrapperID == 'first' || wrapperID == 'emailsignup' ) {
							wrapper.find('p').fadeOut().fadeIn().text('Thank you for signing up.');
							setTimeout(function() {
								$.magnificPopup.close();
							}, 3500);
						} else if ( wrapperID == 'form-buzz' ) {
							wrapper.find('p').fadeOut().fadeIn().text('Indagare\'s e-Newsletter, full of travel buzz, is sent out every other week.');
							$('#'+wrapperID).delay(1500).slideUp();
						}
                    }
                }
            });


//		event.preventDefault();
		
	});
	
	// login form for top nav and lockout modal
	jQuery("#form-login").submit(function(e) {
		return process_login(e,'#form-login');
	});	// end login form
});

jQuery(window).bind('resize orientationchange', function() {
	// Menu behavior
	adjustMenu();
});

var adjustMenu = function() {
	if (jQuery(".show-subnav").css("display") == "block" ) {
		jQuery("#nav li").unbind('mouseenter mouseleave');
		//jQuery("#nav li a.parent").unbind('click');
		jQuery("#nav li .show-subnav").unbind('click').bind('click', function (e) {
			if (jQuery(this).parent().hasClass('open')) {
				jQuery("#nav .open").removeClass("open");
			} else {
				jQuery("#nav .open").removeClass("open");
				if (jQuery(this).next(".subnav").length) {
					jQuery(this).parent().addClass("open"); // display popup
				}
			}
			e.stopPropagation();
		});
	} else if (jQuery(".show-subnav").css("display") == "none" ) {
		jQuery("#nav .open .subnav").removeClass('show-this');
		jQuery("#nav li").removeClass("open");
		//jQuery("#nav li a").unbind('click');
		jQuery("#nav li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
			// must be attached to li so that mouseleave is not triggered when hover over submenu
			jQuery(this).toggleClass('open');
		});
	}
};

function show_map_buttons() {}

function hide_map_buttons() {}

function fullscreen_map() {}

function collapse_map() {}

function process_login(e,t){
	e.preventDefault();
	var r = jQuery(t).attr('data-successurl');
	if(!r || r == '') {
		r = login_redirect;
	}
	if(!r || r == '') {
		r = '/';
	}
	jQuery.ajax({
		type: "POST",
		url: ajax_login_object.ajaxurl,
		async: false,
		data: {
			'action': 'ajaxlogin',
			'username': jQuery(t+" #field1").val(),
			'password': jQuery(t+" #field2").val(),
			'security': jQuery(t+" #security").val()
		}
	}).done(function(data){
		if ( data.login ) {
			if ( data.ssotoken && data.ssotoken != '' ) {
				if(r.indexOf("?") > -1) {
					r += "&";
				} else {
					r += "?";
				}
				r += 'ssoToken='+data.ssotoken;
			}
			if(r.indexOf('/') === 0 || r.indexOf('.indagare.com/') > -1 ) {
				window.location.href = r;
			} else {
				window.open(r);
				window.location.reload(); 
			}
		} else {
			if ( ! data ) { 
				window.location.reload(); 
			} else {
				jQuery(t+' .message').html('<p>'+data.message+'</p>').fadeIn(1500).fadeOut(1500);
			}
		}
	});
	return false;
}
