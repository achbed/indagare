var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
  return query_string;
}();

var signup;
if (!signup) {
	function signupObj() {
		var self = this;

		this.processing = false;
		self.progressDialog = null;

		this.validateForm = function() {
			var complete = shrValidate.validateForm('#accountinfo-form');

			if ( complete !== true ) {
				var to = jQuery("#tab-container").find('.validate-error');
				if (to.length)
					jQuery.scrollTo(to.first().closest('.inputgroup').parent());
				return;
			}

			if (self.processing) {
				return;
			}

			self.processing = true;
			jQuery('#subTab3').addClass('disabled'); // Disable the submit button

			var args = {
				action : "idj-invite",
				cid : jQuery("#ContactID").val(),
				username : jQuery("#wp-username").val(),
				password : jQuery("#wp-password1").val()
			};

			var result = {};

			jQuery
					.ajax('/wp-admin/admin-ajax.php', {
						method : "POST",
						data : args
					})
					.done(function(d, s, x) {
						result = d;
					})
					.fail(function(x, s, e) {
								if(progressDialog) {
									progressDialog.close();
								}
								result = {
									r_approved : 'ERROR',
									r_code : 0,
									r_error : 'Error communicating with account system.'
								};
								jQuery('#subTab3').removeClass('disabled');
							})
					.always(function() {
								if(progressDialog) {
									progressDialog.close();
								}

								if ( result.success ) {
									window.location = "/welcome/";
									return;
								}
								
								jQuery('#errordetail').html(
										jQuery('<span class="errormsg"></span>')
												.text(result.data.message));
								jQuery('#subTab3').removeClass('disabled');

								jQuery.magnificPopup.open({
									items : {
										type : 'inline',
										src : '#lightbox-signup-error',
										midClick : true
									},
									closeOnBgClick : true,
									enableEscapeKey : true,
									showCloseBtn : true
								});

								self.processing = false;
							});
    		progressDialog = jQuery.alert({
    			title:'Creating account',
    			content:'Please wait...',
    			closeIcon: false,
    			confirmButton:'',
    			cancelButton:'',
    			confirmButtonClass:'hidden',
    			cancelButtonClass:'hidden',
    		});

		};
	}

	signup = new signupObj();
}

jQuery('body').on('click','#subTab3',function(e) {
	signup.validateForm();
});

function _setrm(t,v) {
	if(!v || v=='') {
		jQuery(t).parent().remove();
		return;
	}
	jQuery(t).html(v);
}