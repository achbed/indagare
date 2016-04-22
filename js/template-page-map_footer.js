jQuery().ready(function($) {
	jQuery.ajax({
		url: "/export/datacities_ac.json"
	}).done(function(d) {
		$("input#inputdestination").autocomplete({
			resultsContainer: '.autocomplete',
			 onItemSelect: function(item) {
				$('.autocompletedestination').val(item.data);
			},
			onNoMatch: function() {
			}, 
			data: d
		});
	});

	$('#search-destinations .icon').click(function(){
		urlvars = $('.autocompletedestination').val();
		if (!!urlvars.length ) {
			window.location.href = '/destinations/?destinations='+urlvars;
		}
	});

	$('#search-destinations #inputdestination').keydown(function (e) {
	  var keyCode = e.keyCode || e.which;
    urlvars = $('.autocompletedestination').val();
	  if (keyCode == 13 && !!urlvars.length) {
	  	window.location.href = '/destinations/?destinations='+urlvars;
			return false;
	  }
	});
});