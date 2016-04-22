/* admin js */

jQuery().ready(function($) {

	function getURLParameter(name) {
		return decodeURIComponent(
			(location.search.match(RegExp("[?|&]"+name+'=(.+?)(&|$)'))||[,null])[1]
		);  
	}

	// list view of ACF gallery items
	if ( $('.acf-gallery .view-list') ) {
		$('.acf-gallery .view-list').click();
	}

    // check every media library assistant checkbox for search
    $('#mla-filter').find('p.search-box :checkbox').prop('checked', true);
    

	// media library assistant - set all items to checked
	function mlasearchcheck () {
		$('.mla-search-box').find('p.search-box :checkbox').prop('checked', true);

	}
	
	// tool tips - qtip jquery plugin
	$.fn.qtip.zindex = 200000;

	// target inner div so the li element can still handle mouse click
	$(document).on('mouseover', 'li.attachment div div', function(event) {
		// Bind the qTip within the event handler
		$(this).qtip({
			overwrite: false, // Make sure the tooltip won't be overridden once created
			content: {
				text: $(this).find('img').attr('src')
			},
			position: {
				target: $(this)
			},
			show: {
				event: event.type, // Use the same show event as the one that triggered the event handler
				ready: true // Show the tooltip as soon as it's bound, vital so it shows up the first time you hover!
			}
		}, event); // Pass through our original event to qTip
	});
	// end tool tips

	// ACF gallery add image button
    $('.add-image-li .add-image').click(function() {
		$('.media-modal').ready(function(){
			setTimeout(mlasearchcheck, 2000);
	    });
    });

	// WP add media button
	// $('#insert-media-button').click(function() {
	// changed to class instead of id to handle multiple WP add media buttons on pages like itinerary
    $('.insert-media').click(function() {
		$('.media-modal').ready(function(){
			setTimeout(mlasearchcheck, 2000);
	    });
    });

	// WP set featured image button
    $('#set-post-thumbnail').click(function() {
		$('.media-modal').ready(function(){
			setTimeout(mlasearchcheck, 2000);
	    });
    });
    
    // media overlay side nav - insert, create gallery, set featured image
	$(document).on('click', '.media-menu a.media-menu-item', function(event) {
		mlasearchcheck();
	});

	// ACF add image button
	setTimeout(acfimagecheck, 2000);

	function acfimagecheck () {
    
		acfimageuploader = $('.acf-image-uploader').find('input.button');
	
		acfimageuploader.each(function() {
			$(this).click(function() {
				$('.media-modal').ready(function(){
				setTimeout(mlasearchcheck, 2000);
				});
			});
		});
	
	}	    

    // media overlay top nav loaded from WP featured image or ACF gallery image - media library button
	$(document).on('click', '.media-router a.media-menu-item', function(event) {
		mlasearchcheck();
	});



    // hide slug field on Destinations edit
	$('#edittag #slug').parent().parent().remove();	

	// hide description field on Destinations edit
	$('#edittag #description').parent().parent().remove();	

	// hide description field on Destinations creation
	$('#addtag #tag-description').parent().remove();	

	// insert gallery settings - link to nothing
	$('.media-frame-toolbar .media-toolbar-primary a.media-button-gallery').click(function() {
		$('.gallery-settings').ready(function(){
			setTimeout(gallerysettings, 2000);
		});
	});

	function gallerysettings() {
		$('.media-sidebar .gallery-settings select option[value=none]').attr('selected','selected');
	}
	
	// sidebar sorting of item types
	$('ul#hoteltypechecklist').ready(function(){
		$('ul#hoteltypechecklist>li').tsort();
	});

	$('ul#restauranttypechecklist').ready(function(){
		$('ul#restauranttypechecklist>li').tsort();
	});

	$('ul#shoptypechecklist').ready(function(){
		$('ul#shoptypechecklist>li').tsort();
	});

	$('ul#activitytypechecklist').ready(function(){
		$('ul#activitytypechecklist>li').tsort();
	});

	$('ul#mealtypechecklist').ready(function(){
		$('ul#mealtypechecklist>li').tsort();
	});

	$('ul#benefitchecklist').ready(function(){
		$('ul#benefitchecklist>li').tsort();
	});

	$('ul#editorspickchecklist').ready(function(){
		$('ul#editorspickchecklist>li').tsort();
	});

	$('ul#columnchecklist').ready(function(){
		$('ul#columnchecklist>li').tsort();
	});

	$('ul#interestchecklist').ready(function(){
		$('ul#interestchecklist>li').tsort();
	});


	// quick edit sorting of item types
	$('ul.hoteltype-checklist').ready(function(){
		$('ul.hoteltype-checklist>li').tsort();
	});

	$('ul.restauranttype-checklist').ready(function(){
		$('ul.restauranttype-checklist>li').tsort();
	});

	$('ul.shoptype-checklist').ready(function(){
		$('ul.shoptype-checklist>li').tsort();
	});

	$('ul.activitytype-checklist').ready(function(){
		$('ul.activitytype-checklist>li').tsort();
	});

	$('ul.mealtype-checklist').ready(function(){
		$('ul.mealtype-checklist>li').tsort();
	});

	$('ul.benefit-checklist').ready(function(){
		$('ul.benefit-checklist>li').tsort();
	});

	$('ul.editorspick-checklist').ready(function(){
		$('ul.editorspick-checklist>li').tsort();
	});

	$('ul.column-checklist').ready(function(){
		$('ul.column-checklist>li').tsort();
	});

	$('ul.interest-checklist').ready(function(){
		$('ul.interest-checklist>li').tsort();
	});


});

