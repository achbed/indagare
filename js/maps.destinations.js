var markers = [];

jQuery.ajax({
	dataType: 'json',
	url: uploads_path+'/maplocations.json'
})
.done(function(data) {
	markers = data;
	jQuery(document).ready(function($){ maplocations_initialize(); });
});
