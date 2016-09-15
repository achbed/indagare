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
			$('.detail .view-more a.map').html(ajax_login_object.messages.showimages);
			$('.archive .view-more a.map').html(ajax_login_object.messages.hidemap);
			$('#mapcanvas').parent().addClass('show-map');
			gmap_loadmarkers();
		} else {
			$('.showmap').val('');
			$('#gallery-header').show();
			$('.detail .view-more a.map').html(ajax_login_object.messages.showmap);
			$('.archive .view-more a.map').html(ajax_login_object.messages.showmap);
			$('#mapcanvas').parent().removeClass('show-map');
		}
		return false;
	});

	// review overview page loads with map open | check to make sure there are items to draw on the map
	
	if ( $('.showmap').val() && itemcount > 0 ) {
		$('#mapcanvas').addClass('show-this');
		$('.detail .view-more a.map').html(ajax_login_object.messages.showimages);
		$('.archive .view-more a.map').html(ajax_login_object.messages.hidemap);
		$('#mapcanvas').parent().addClass('show-map');
		gmap_loadmarkers();
	}
	
	$('#map-modal-toggle').click(function(event) {
		event.preventDefault();
		$('body').toggleClass('modalmap');
		$('body #map-modal-toggle').attr('title',ajax_login_object.messages.fullscreen).html(ajax_login_object.messages.fullscreen);
		$('body.modalmap #map-modal-toggle').attr('title',ajax_login_object.messages.closemap).html(ajax_login_object.messages.closemap);
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
			wrapper.find('p').fadeOut().fadeIn().html(ajax_login_object.messages.newsletteremailerr);
			return false;
		}
	
/*
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange=function(){
//			console.log(xmlhttp.responseText);

			if (xmlhttp.readyState==4 && xmlhttp.status==200 && xmlhttp.responseText == 'true'){
				wrapper.find('h2').fadeOut().fadeIn().html('Thank you');
				if ( wrapperID == 'first' || wrapperID == 'emailsignup' ) {
					wrapper.find('p').fadeOut().fadeIn().html('Thank you for signing up.');
					setTimeout(function() {
						$.magnificPopup.close();
					}, 3500);
				} else if ( wrapperID == 'form-buzz' ) {
					wrapper.find('p').fadeOut().fadeIn().html('Indagare\'s e-Newsletter, full of travel buzz, is sent out every other week.');
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

		//grab attributes and values out of the form
		var data = {email: $(this).find('.newsletter-signup-input').val(), fname: $(this).find('.newsletter-signup-fname').val(), lname: $(this).find('.newsletter-signup-lname').val()};
		var endpoint = $(this).attr('action');
		
		//make the ajax request
		$.ajax({
		  method: 'POST',
		  dataType: "json",
		  url: endpoint,
		  data: data
		}).success(function(data){
		  if(data.id){
			//successful adds will have an id attribute on the object
//			alert('thanks for signing up');
				wrapper.find('h2').fadeOut().fadeIn().html(ajax_login_object.messages.thankyou);
				if ( wrapperID == 'first' || wrapperID == 'emailsignup' ) {
					wrapper.find('p').fadeOut().fadeIn().html(ajax_login_object.messages.thankyousignup);
					setTimeout(function() {
						$.magnificPopup.close();
					}, 3500);
				} else if ( wrapperID == 'form-buzz' ) {
					wrapper.find('p').fadeOut().fadeIn().html(ajax_login_object.messages.newsletter);
					$('#'+wrapperID).delay(1500).slideUp();
				}
		  } else if (data.title == 'Member Exists') {
			//MC wil send back an error object with "Member Exists" as the title
//			alert('thanks, but you are alredy signed up');
			wrapper.find('p').fadeOut().fadeIn().html(ajax_login_object.messages.alreadysignedup);
		  } else {
			//something went wrong with the API call
//			alert('oh no, there has been a problem');
		  }
		}).error(function(){
		  //the AJAX function returned a non-200, probably a server problem
//		  alert('oh no, there has been a problem');
		});
		
	});
	
	// login form for top nav and lockout modal
	jQuery("body").on("submit","form.ajax-login",function(e) {
		return process_login(e);
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

jQuery(document).on('click','form.processing .button',function(e){e.preventDefault;return false;});

function process_login(e,t){
	e.preventDefault();
	if(!t) { t = e.target; }
	var f = jQuery(t);
	if(!f.is('form')) {
		f = f.find('form');
	}
	if(f.hasClass('processing')) {
		return;
	}
	f.addClass('processing');
	var r = jQuery(t).attr('data-successurl');
	if( !r ) {
		r = login_redirect;
	}
	if( !r ) {
		r = window.location.href;
	}
	jQuery.ajax({
		type: "POST",
		url: ajax_login_object.ajaxurl,
		async: false,
		data: {
			'action': 'ajaxlogin',
			'username': f.find("#field1").val(),
			'password': f.find("#field2").val(),
			'security': f.find("#security").val()
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
				f.find('.message').html('<p>'+data.message+'</p>').fadeIn(1500).fadeOut(1500);
				f.removeClass('processing');
			}
		}
	});
	return false;
}
