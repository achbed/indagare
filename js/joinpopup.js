jQuery(document).ready(function($) {

	$.magnificPopup.open({
	  items: {
		type: 'inline',
		src: '#lightbox-join', // can be a HTML string, jQuery object, or CSS selector
		midClick: true
	  },
	  modal: true
	});

});
