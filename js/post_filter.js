if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}
jQuery().ready(function($) {

	// set checkbox click event to include parent li
	$("ul.filter li").click(function(e) {
		if (e.target.type == "checkbox") {
			e.stopPropagation();
		} else {
			// destination level
			var $checkbox = $(this).find(':checkbox');
			if ( $checkbox ) {
				$checkbox.attr('checked', !$checkbox.attr('checked'));
			}

			// region level
			var $checkbox = $(this).find(':radio');
			if ( $checkbox ) {
				var $checkboxparent = $(this).parent().find(':radio');
				$checkboxparent.attr('checked',false);
				$checkbox.attr('checked', !$checkbox.attr('checked'));
			}
		}
	});

	// check for urlvars for post types, neighborhoods, benefits and editor's picks
	var posttypeschecked = getURLParameter(posttype);
	var neighborhoodschecked = getURLParameter('destinations');
	var benefitschecked = getURLParameter('benefit');
	var editorschecked = getURLParameter('editorspick');
	var mealschecked = getURLParameter('mealtype');

//	var posttypeslist = posttypeschecked.split(",");
	var posttypeslist = posttypeschecked.split("+");
	var neighborhoodslist = neighborhoodschecked.split(",");
	var benefitslist = benefitschecked.split(",");
	var editorslist = editorschecked.split(",");
	var mealslist = mealschecked.split(",");
	
	if ( posttypeslist.length > 0 ) {
		posttypeslist.forEach(function(item) {
			var ele = $('#posttypes').find('input[value='+item+']');
			if(ele.is(':checked')){ } else {
				ele.prop('checked', true);
			}
		});
	}

	if ( neighborhoodslist.length > 0 ) {
		neighborhoodslist.forEach(function(item) {
			var ele = $('#neighborhoods').find('input[value='+item+']');
			if(ele.is(':checked')){ } else {
				ele.prop('checked', true);
			}
		});
	}
	
	if ( benefitslist.length > 0 ) {
		benefitslist.forEach(function(item) {
			var ele = $('#benefits').find('input[value='+item+']');
			if(ele.is(':checked')){ } else {
				ele.prop('checked', true);
			}
		});
	}

	if ( editorslist.length > 0 ) {
		editorslist.forEach(function(item) {
			var ele = $('#editors').find('input[value='+item+']');
			if(ele.is(':checked')){ } else {
				ele.prop('checked', true);
			}
		});
	}

	if ( mealslist.length > 0 ) {
		mealslist.forEach(function(item) {
			var ele = $('#meals').find('input[value='+item+']');
			if(ele.is(':checked')){ } else {
				ele.prop('checked', true);
			}
		});
	}

	// apply review filters
    $('#applyfilters').click(function(event) {
   		event.preventDefault();
		var posttypes = $("#posttypes input:checkbox:checked").map(function(){
	        return $(this).val();
	    }).toArray();
	    if ( $('ul#neighborhoods').find(':checkbox') ) { // destination level
			var neighborhoodsdest = $("#neighborhoods input:checkbox:checked").map(function(){
				return $(this).val();
			}).toArray();
	    }
	    if ( $('ul#neighborhoods').find(':radio') ) { // region level
			var neighborhoodsreg = $("#neighborhoods input:radio:checked").map(function(){
				return $(this).val();
			}).toArray();
	    }
		var benefits = $("#benefits input:checkbox:checked").map(function(){
	        return $(this).val();
	    }).toArray();
		var editors = $("#editors input:checkbox:checked").map(function(){
	        return $(this).val();
	    }).toArray();
		var meals = $("#meals input:checkbox:checked").map(function(){
	        return $(this).val();
	    }).toArray();

    	posttypesurl = '';
    	neighborhoodsurl = '';
    	benefitsurl = '';
    	editorsurl = '';
    	mealsurl = '';
    	neighborhoods = '';
    	
    	if ( !!posttypes.length )
    		posttypesurl = posttypes.join('+');
    	// get post types
/*		if ( posttypes.length > 1 ) {
		
			i = 1;
			posttypes.forEach(function(item) {

				if ( i !== 1 ) {
//					posttypesurl += ','+item;
					posttypesurl += '+'+item;
				} else {
					posttypesurl += item;
				}
				i++;

			});			

		} else if ( posttypes.length == 1 ) {
				posttypesurl = posttypes[0];
		}
*/
    	// get neighborhoods
    	if ( !!neighborhoodsdest.length ) { // destination level
    		neighborhoods = neighborhoodsdest;
    	} else if ( !!neighborhoodsreg.length ) { // region level
    		neighborhoods = neighborhoodsreg;
    	}
    	
    	if ( !!neighborhoods.length )
    		neighborhoodsurl = neighborhoods.join(',');
		/*
		if ( !!neighborhoods.length && neighborhoods.length > 1 ) {
		
			i = 1;
			neighborhoods.forEach(function(item) {

				if ( i !== 1 ) {
					neighborhoodsurl += ','+item;
				} else {
					neighborhoodsurl += item;
				}
				i++;

			});			

		} else if ( !!neighborhoods.length && neighborhoods.length == 1 ) {
				neighborhoodsurl = neighborhoods[0];
		}
*/
    	
    	// get benefits
    	if ( !!benefits.length )
    		benefitsurl = benefits.join(','); /*
		if ( benefits.length > 1 ) {
		
			i = 1;
			benefits.forEach(function(item) {

				if ( i !== 1 ) {
					benefitsurl += ','+item;
				} else {
					benefitsurl += item;
				}
				i++;

			});			

		} else if ( benefits.length == 1 ) {
				benefitsurl = benefits[0];
		}
*/
    	
    	// get editors
    	if ( !!editors.length )
    		editorsurl = editors.join(','); /*
		if ( editors.length > 1 ) {
		
			i = 1;
			editors.forEach(function(item) {

				if ( i !== 1 ) {
					editorsurl += ','+item;
				} else {
					editorsurl += item;
				}
				i++;

			});			

		} else if ( editors.length == 1 ) {
				editorsurl = editors[0];
		}
*/
    	// get meals
    	if ( !!meals.length )
    		mealsurl = meals.join(','); /*
		if ( meals.length > 1 ) {
		
			i = 1;
			meals.forEach(function(item) {

				if ( i !== 1 ) {
					mealsurl += ','+item;
				} else {
					mealsurl += item;
				}
				i++;

			});			

		} else if ( meals.length == 1 ) {
				mealsurl = meals[0];
		}
*/
    	urlvars = '';
    	urlvara = new Array();

		// build urlvars based on filters selected
		if ( !!posttypesurl.length ) {
			urlvara.push(posttype + '=' + posttypesurl);
		}

		if ( !!neighborhoodsurl.length ) {
			urlvara.push('destinations=' + neighborhoodsurl);
		}

		if ( !!benefitsurl.length ) {
			urlvara.push('benefit=' + benefitsurl);
		}

		if ( !!editorsurl.length ) {
			urlvara.push('editorspick=' + editorsurl);
		}

		if ( !!mealsurl.length ) {
			urlvara.push('mealtype=' + mealsurl);
		}
		
		if ( $('.showmap').val() ) {
			urlvara.push('map=show');
		}

		if(urlvara.length)
			urlvars += '?' + urlvara.join('&');
		
		var currenturl = new String(window.location.href);
		currenturl = currenturl.replace(/#.*$/, "");
		if ( !currenturl.endsWith( urlvars ) ) {
			window.location.href = urlvars+'#content';
		}
    });

	// clear filters
    $('#clearfilters').click(function(event) {
   		event.preventDefault();

		var params = window.location.search.replace(/^\?/,'').split(/&/);

		if ( $('.showmap').val() ) {
			var f = false;
			for(var i in params) {
				if(params[i].indexOf('map=') === -1) {
					f = true;
					break;
				}
			}
			if(!f) {
				params.push('map=show');
			}
		}
		
		params = params.join("&");
		if(params.length>0) {
			params = "?" + params;
		}

		window.location.href = window.location.pathname + params + '#content';

    });

});
