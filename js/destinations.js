var kodelayedRender = false;
var koPostRenderAnimating = 0;
var bindPoint;
var selectionDelayedChange = false;
var firstPass = 1;

var koKeyedIDItem = function(v, n, i) {
	var self = this;
	self.name = ko.observable(n);
	self.value = ko.observable(v);
	self.tid = ko.observable((!i ? 0 : i));

	self.compareName = function(o) {
		if (self.name() < o.name())
			return -1;
		if (self.name() > o.name())
			return 1;
		return 0;
	};

	self.compareValue = function(o) {
		if (self.value() < o.value())
			return -1;
		if (self.value() > o.value())
			return 1;
		return 0;
	};

	self.compareID = function(o) {
		if (self.tid() < o.tid())
			return -1;
		if (self.tid() > o.tid())
			return 1;
		return 0;
	};

};

var koKeyedItem = function(v, n) {
	var self = this;
	self.name = ko.observable(n);
	self.value = ko.observable(v);
	self.iconimg = ko.observable('');
	self.active = ko.observable(true);
	self.selected = ko.observable(false);
	self.type = ko.observable('');
	
	self.toggleSelected = function() {
		var i = self.selected();
		self.selected(!i);
	};

	self.compareName = function(o) {
		if (self.name() < o.name())
			return -1;
		if (self.name() > o.name())
			return 1;
		return 0;
	};

	self.compareValue = function(o) {
		if (self.value() < o.value())
			return -1;
		if (self.value() > o.value())
			return 1;
		return 0;
	};

	self.icon = ko.computed(function() {
		if ( self.iconimg() != '' ) {
			return self.iconimg();
		}
		if (!theme_path) {
			return '';
		}
		return theme_path + '/img/icon-' + self.type() + '-' + self.value() + ".png";
	});

	self.classes = ko.pureComputed(function() {
		var r = new Array();
		r.push(self.value());

		if (self.active() === true) {
			r.push('active');
		} else {
			r.push('inactive');
		}

		if (self.selected() === true) {
			r.push('selected');
		} else {
			r.push('deselected');
		}

		return r.join(' ');
	});

};

var koDestItem = function(a) {
	var self = this;
	self.id = ko.observable((!a.id ? '' : a.id));
	self.topslug = ko.observable(new koKeyedIDItem(a.topslug, a.topname,
			a.topid));
	self.regslug = ko.observable(new koKeyedIDItem(a.regslug, a.regname,
			a.regid));
	self.slug = ko.observable(a.slug);
	self.image = ko.observable(a.image);
	self.name = ko.observable(a.name);
	self.url = ko.observable(a.url);
	self.season = ko.observableArray();
	self.interest = ko.observableArray();

	self.valid = ko.computed(function() {
		return self.image() != '';
	});

	self.classes = ko.pureComputed(function() {
		var c = 'article type-article status-publish hentry contain all';
		c += ' ' + self.topslug().value();
		c += ' ' + self.regslug().value();
		c += ' ' + self.slug();
		var i = self.interest();
		for (var x = 0; x < i.length; x++)
			c += ' interest-' + i[x];
		i = self.season();
		for (var x = 0; x < i.length; x++)
			c += ' season-' + i[x];
		if (self.name() == 'Other Recommended Hotels') {
			c += ' otherhidden';
		}
		return c;
	});
	
	// Initialize the season and interest values
	var arr;
	
	if (a.season !== false) {
		arr = self.season();
		for (var x = 0; x < a.season.length; x++) {
			var i = new koKeyedItem(a.season[x].value, a.season[x].name);
			if ( a.season[x].icon != '' ) {
				i.iconimg( a.season[x].icon );
			}
			arr.push(i);
		}
		self.season.valueHasMutated();
	}
	
	if (a.interest !== false) {
		arr = self.interest();
		for (var x = 0; x < a.interest.length; x++) {
			var i = new koKeyedItem(a.interest[x].value, a.interest[x].name);
			if ( a.interest[x].icon != '' ) {
				i.iconimg( a.interest[x].icon );
			}
			arr.push(i);
		}
		self.interest.valueHasMutated();
	}
};

var koDestDetails = function(a) {
	var self = this;

	self.allDest = ko.observableArray([]).extend({
		notify : 'always', rateLimit : {timeout : 50,method : "notifyWhenChangesStop"}
	});
	self.selectedTopslug = ko.observable('all');
	self.selectedRegslug = ko.observable('all');

	self.allInterests = ko.observableArray([]).extend({
		notify : 'always', rateLimit : {timeout : 50,method : "notifyWhenChangesStop"}
	});
	self.allSeasons = ko.observableArray([]).extend({
		notify : 'always', rateLimit : {timeout : 50,method : "notifyWhenChangesStop"}
	});

	// A complete list of destinations
	self.listTopslug = ko.computed(function() {
		var topslug = new Array();
		topslug[0] = new koKeyedIDItem('all', 'All Destinations', 0);

		var a = self.allDest();
		for (var x = 0; x < a.length; x++) {
			var found = 0;
			for (var y = 0; y < topslug.length; y++) {
				if (topslug[y].value() == a[x].topslug().value()) {
					y = topslug.length;
					found = 1;
				}
			}
			if (found == 0)
				topslug[topslug.length] = a[x].topslug();
		}

		return topslug;
	}, {
		deferEvaluation : true
	}).extend({
		notify : 'always',
		rateLimit : {
			timeout : 50,
			method : "notifyWhenChangesStop"
		}
	});

	// filteredRegslug is the filtered list of regions
	self.filteredRegslug = ko.computed(function() {
		var regslug = new Array();
		if (self.selectedTopslug() == 'caribbean')
			regslug[0] = new koKeyedIDItem('all', 'All Destinations', 0);
		else
			regslug[0] = new koKeyedIDItem('all', 'All Regions', 0);

		var a = self.allDest();
		for (var x = 0; x < a.length; x++) {
			var found = 0;
			for (var y = 0; y < regslug.length; y++) {
				if (regslug[y].value() == a[x].regslug().value()) {
					y = regslug.length;
					found = 1;
				}
			}
			if (found == 0) {
				var use = 0;
				if (self.selectedTopslug() == 'all') {
					use = 1;
				} else if (self.selectedTopslug() == a[x].topslug().value()) {
					use = 1;
				}

				if (use != 0) {
					regslug[regslug.length] = a[x].regslug();
				}
			}
		}

		regslug.sort(koCompare.name);

		return regslug;
	}, {
		deferEvaluation : true
	}).extend({
		notify : 'always',
		rateLimit : {
			timeout : 50,
			method : "notifyWhenChangesStop"
		}
	});
	
	self.selectedInterests = ko.computed(function() {
		var l = self.allInterests();
		var o = new Array();
		for(var x=0;x<l.length;x++) {
			if(l[x].selected() === true)
				o.push(l[x]);
		}
		return o;
	}).extend({
		notify : 'always', rateLimit : {timeout : 50,method : "notifyWhenChangesStop"}
	});

	self.selectedSeasons = ko.computed(function() {
		var l = self.allSeasons();
		var o = new Array();
		for(var x=0;x<l.length;x++) {
			if(l[x].selected() === true)
				o.push(l[x]);
		}
		return o;
	}).extend({
		notify : 'always', rateLimit : {timeout : 50,method : "notifyWhenChangesStop"}
	});

	// filteredDest is the blocks shown to the user
	self.filteredDest = ko.computed(function() {
		var i = self.allDest();
		var d = ko.utils.arrayFilter(i, function(a) {
			var t,r,n,s;
			
			t = ((self.selectedTopslug() == 'all') || (a.topslug().value() == self.selectedTopslug()));
			r = ((self.selectedRegslug() == 'all') || (a.regslug().value() == self.selectedRegslug()));
			n = filterArray(self.selectedInterests(), a.interest(), true);
			s = filterArray(self.selectedSeasons(), a.season(), false);
			
			return (t && r && s && n);
		});
		d.sort(koCompare.name);
		return d;
	}, {
		deferEvaluation : true
	}).extend({
		rateLimit : {
			timeout : 50,
			method : "notifyWhenChangesStop"
		}
	});

	self.setTid = function(option, item) {
		ko.applyBindingsToNode(option, {
			attr : {
				tid : item.tid
			}
		}, item);
	};

	// Animation callbacks
	self.showElement = function(elem) {
		koPostRender_Call();
		selectPostChange_Call();
	};

	self.hideElement = function(elem) {
		jQuery(elem).remove();
		koPostRender_Call();
		selectPostChange_Call();
	};

	self.postRenderHook = function() {
		koPostRender_Call();
		selectPostChange_Call();
	};

	// listInterests is the list of all interests
	self.listInterests = ko.computed(function() {
		var a, x, i, ii, found, y, all, itm;
		all = self.allInterests();
		for (y = 0; y < all.length; y++) {
			all[y].active(false);
		}

		a = self.filteredDest();
		for (x = 0; x < a.length; x++) { // Loop through all visible
											// destinations
			i = a[x].interest();
			for (ii = 0; ii < i.length; ii++) { // Loop through all interests in
												// a destination
				for (y = 0; y < all.length; y++) {
					if (all[y].value() == i[ii].value()) {
						all[y].active(true);
					}
				}
			}
		}

//		return all;

		return all.sort(function(a,b){
			return a.name() == b.name() ? 0 : (a.name() < b.name() ? -1 : 1);
		});

	}).extend({
		rateLimit : {
			timeout : 50,
			method : "notifyWhenChangesStop"
		}
	});

	// listSeasons is the list of all seasons with deselected/inactive applied
	self.listSeasons = ko.computed(function() {
		var a, x, i, ii, found, y, all, itm;
		all = self.allSeasons();
		for (y = 0; y < all.length; y++) {
			all[y].active(false);
		}
		
		a = self.filteredDest();
		for (x = 0; x < a.length; x++) { // Loop through all visible
											// destinations
			i = a[x].season();
			for (ii = 0; ii < i.length; ii++) { // Loop through all interests in
												// a destination
				for (y = 0; y < all.length; y++) {
					if (all[y].value() == i[ii].value()) {
						all[y].active(true);
					}
				}
			}
		}

//		return all;

		return all.sort(function(a,b){
			return a.value() == b.value() ? 0 : (a.value() < b.value() ? -1 : 1);
		});

	}).extend({
		rateLimit : {
			timeout : 50,
			method : "notifyWhenChangesStop"
		}
	});

	self.toggle_interest = function(e) {
		self.toggle(self.allInterests, e);
		return false;
	};

	self.toggle_season = function(e) {
		self.toggle(self.allSeasons, e);
		return false;
	};

	self.toggle = function(a, e) {
		var v, x, l, y;

		y = a();
		l = y.length;
		v = e.value();

		for (x = (l - 1); x >= 0; x--) {
			if (y[x].value() == v) {
				y[x].toggleSelected();
			}
		}
	};
	
	// Finally, initialize the destination array.
	if (!(!a)) {
		if (a.length > 0) {
			for (var x = 0; x < a.length; x++) {
				var i = new koDestItem(a[x]);
				self.allDest.push(i);
				var s = i.season();
				for(var y=0; y<s.length; y++) {
					s[y].selected(false);
					var found = false;
					var z = 0;
					while((z<self.allSeasons().length) && !found) {
						found = (self.allSeasons()[z].value() == s[y].value());
						z++;
					}
					if(!found)
						self.allSeasons.push(s[y]);
				}
				s = i.interest();
				for(var y=0; y<s.length; y++) {
					s[y].selected(false);
					var found = false;
					var z = 0;
					while((z<self.allInterests().length) && !found) {
						found = (self.allInterests()[z].value() == s[y].value());
						z++;
					}
					if(!found)
						self.allInterests.push(s[y]);
				}
					
			}
		}
	}
};

var koCompare = {
	name : function(a, b) {
		if (a.name() < b.name())
			return -1;
		if (a.name() > b.name())
			return 1;
		return 0;
	}
};

function selectChange_Handle() {
	var r = jQuery('#selectregion2').selected().val();
	var d = jQuery('#selecttop2').selected().val();
	var y = bindPoint.selectedSeasons();
	var s = new Array();
	for(var x=0;x<y.length;x++) {
		s.push(y[x].value());
	}
	var y = bindPoint.selectedInterests();
	var n = new Array();
	for(var x=0;x<y.length;x++) {
		n.push(y[x].value());
	}
	var args = {
		destination: d, 
		region: r, 
		interests: n,
		seasons: s
	};
	
	markersdisplay(args);
	var t = jQuery('#selectregion2').parent().parent().find(
			'.customSelectInner');
	var s = jQuery('#selectregion2 option[value="' + r + '"]').html();
	t.html(s);
}

function selectPostChange_Call() {
	if (selectionDelayedChange != false) {
		window.clearTimeout(selectionDelayedChange);
	}
	selectionDelayedChange = window.setTimeout(function() {
		selectionDelayedChange = false;
		selectChange_Handle();
	}, 100);
}

function koPostRender_Call() {
	if (kodelayedRender != false) {
		window.clearTimeout(kodelayedRender);
	}
	kodelayedRender = window.setTimeout(function() {
		kodelayedRender = false;
		koPostRender();
	}, 100);
}

function koPostRender_StartOut() {
	switch (firstPass) {
	case 1:
		if (jQuery('#selectregion2').children().length > 0) {
			firstPass = 2;
			jQuery('#destinationsfilter').css({
				height : ''
			});
			return;
		}
		koPostRender_Call();
		return;
	case 2:
		firstPass = 0;
		koPostRender_Complete();
		return;
	}

	koPostRenderAnimating = 1;
	jQuery('#destinationlist').velocity({
		p : {
			opacity : 0
		},
		o : {
			duration : 500,
			complete : function() {
				koPostRender_StartIn();
			}
		}
	});
}

function koPostRender_StartIn() {
	koPostRenderAnimating = 2;
	jQuery('#destinationlist').empty().html(jQuery('#destinationstage').html())
			.css({
				display : 'block'
			});

	jQuery('#destinationlist').velocity({
		p : {
			opacity : 1
		},
		o : {
			duration : 500,
			complete : function() {
				koPostRender_Complete();
			}
		}
	});
}

function koPostRender_Complete() {
	koPostRenderAnimating = 0;
}

function koPostRender() {
	switch (koPostRenderAnimating) {
	case 0: // No animation
		koPostRender_StartOut();
		break;
	case 1: // Animating a fade out.
		// We have another request to change content, but we're still
		// animating in the outbound direction. Do nothing - we'll pick up
		// the changes in short order without doing any special handling.
		break;
	case 2: // Animating a fade in.
		// We're still animating, but in the inbound direction.
		// We need to reverse the animation and then immediately fade in the new
		// content.
		koPostRenderAnimating = 3;
		jQuery('#destinationlist').velocity('reverse', {
			complete : function() {
				koPostRender_StartIn();
			}
		});
		return;
	case 3: // Animating a fade out after a mid-animation change
		// We have another request to change content, but we're still
		// animating in the outbound direction. Do nothing - we'll pick up
		// the changes in short order without doing any special handling.
		break;
	}
}

jQuery(document).ready(function($) {
	jQuery("#selecttop2").change(function() {
		/*
		 * jQuery("#destinationlist article:not(." +
		 * top[jQuery(this).selected().val()] + ")").fadeOut();
		 * jQuery("#destinationlist
		 * article.otherhidden").removeClass("othervisible").fadeOut();
		 * jQuery("#destinationlist article." +
		 * top[jQuery(this).selected().val()] + ":not(.otherhidden)").fadeIn();
		 */
		// markersdisplay(top[jQuery('#selecttop2').selected().attr('tid')]);
		jQuery('#selectregion2').val('all').prop('selected', true);
		jQuery('#selectregion2').change();
		/*
		 * var jQueryselectregion = jQuery("#selectregion");
		 * jQueryselectregion.empty(); if (jQuery(this).selected().val() == 35) {
		 * jQueryselectregion.append("<option value=" +
		 * top[jQuery(this).selected().val()] + ">All Destinations</option>");
		 * jQueryselectregion.parent().next().children().children().text("All
		 * Destinations"); } else { jQueryselectregion.append("<option value=" +
		 * top[jQuery(this).selected().val()] + ">All Regions</option>");
		 * jQueryselectregion.parent().next().children().children().text("All
		 * Regions"); if (jQuery(this).selected().val() != 0) {
		 * jQuery.each(regions[jQuery(this).selected().val()], function(index,
		 * value) { optionval = value.split("|"); jQueryselectregion.append("<option
		 * value=" + optionval[1] + ">" + optionval[0] + "</option>"); }); } };
		 */
	});

	// region select
	jQuery("#selectregion2").change(function() {/*
												 * jQuery("#destinationlist
												 * article:not(." +
												 * jQuery(this).selected().val() +
												 * ")").fadeOut();
												 * jQuery("#destinationlist
												 * article.otherhidden").removeClass("othervisible").fadeOut();
												 * jQuery("#destinationlist
												 * article." +
												 * jQuery(this).selected().val() +
												 * ":not(.otherhidden)").fadeIn();
												 * if
												 * (jQuery(this).find(":selected").index() !=
												 * 0) { jQuery("#destinationlist
												 * article." +
												 * jQuery(this).selected().val() +
												 * ".otherhidden").addClass("othervisible").fadeIn(); }
												 */
		// selectPostChange_Call();
	});

	jQuery.ajax({
		dataType : 'json',
		url : uploads_path + '/destinations_details.json'
	}).done(function(data) {
		var d = [];
		if (!(!data)) {
			for (i in data) {
				data[i].id = i;
				d.push(data[i]);
			}
		}
		bindPoint = new koDestDetails(d);
		ko.applyBindings(bindPoint);
		koPostRender_StartOut();
	});

});
/*
jQuery.ajax({
	dataType : 'json',
	url : uploads_path + '/destinations_top.json'
}).done(function(data) {
	top = data;
});

jQuery.ajax({
	dataType : 'json',
	url : uploads_path + '/destinations_regions.json'
}).done(function(data) {
	regions = data;
});
*/

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
