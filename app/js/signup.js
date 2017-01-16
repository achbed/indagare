var shrValidate,showTrial,trialCode;

if(typeof shrValidate !== "undefined") {
	shrValidate.emailHandleInactiveAcct = function(d) {
		var t = jQuery('#tgCode').val();
		t = t.toLowerCase();
		if ( ( t == 'elite-upgrade' ) || ( t == 'connoisseur-upgrade' ) ) {
			window.location = "http://travel.indagare.com/membership-renewal";
		}
	};
}

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

if (typeof showTrial === 'undefined') {
	showTrial = false;
}
var showBilling = !showTrial;
if( showTrial ) {
	if((typeof trialCode === "undefined") || ( trialCode === "" )) {
		trialCode = "trial";
	}
}

var signup;
if (!signup) {
	function signupObj() {
		var self = this;

		this.usrNameChk = false;
		this.usrEmailChk = -4;
		this.mbSelect = false;
		this.processing = false;
		this.validatingForm = false;
		self.progressDialog = null;

		this.selfInit = function() {
			if (!self.mbSelect || !self.mbSelect.length) {
				self.mbSelect = jQuery('#Membership_Level__c');
			}
		};
		
		this.fixBilling = function() {
			if (showBilling) {
				jQuery('.tab').removeClass('is-trial');
			} else {
				jQuery('.tab').addClass('is-trial');
			}
		};

		this.initFields = function() {
			self.selfInit();
			self.fixBilling();

			populateCountries('s_country', 's_state', 'United States');

			if (redirect == "swifttrip") {
				jQuery("#lightbox-signup-complete")
						.append(
								'<input id="backtohotel" type="submit" value="Back to Hotel" class="button">');
			}
		};

		this.buildButtonEventMgrs = function() {
			self.selfInit();
			jQuery('body').on('click', '#subTab3', function(e) {
				e.preventDefault();
				self.validateForm();
			}).on('click', '#view_terms', function(e) {
				e.preventDefault();
				jQuery('#terms').toggle();
			}).on('change', '#tgCode', function(e) {
				self.tgCodeLookup();
			}).on('change', '#wp-username', function(e) {
				//self.checkUsername();
			}).on('change', '#contact-HomePhone', function(e) {
				self.formatPhone();
			}).on('change', '#contact-Email', function(e) {
				//self.checkEmail();
			}).on('change', '#Membership_Level__c', function(e) {
				self.setSelectedMBYears();
			}).on('change', 'input,select,textarea', function(e) {
				//self.validateThisField(jQuery(this).attr('id'));
			});
		};
		
		this.formatPhone = function() {
			var n = jQuery('#contact-HomePhone').val(),r = /[^0-9]/gi,c=n.replace(r,'');
			 if(c.length != 10){
				 // We don't have 10 digits to work with, so just return the input value 
				 return;
			 }  
			 n = '('+c.substring(0,3)+') '+c.substring(3,6)+'-'+c.substring(6,10);
			 jQuery('#contact-HomePhone').val(n);
		}

		this.buildMembershipDD = function() {
			self.selfInit();
			
			if (showTrial) {
				self.mbSelect.parent().addClass('iform-row-2col').removeClass('iform-row-1col');
				return;
			}

			self.mbSelect.parent().addClass('iform-row-1col').removeClass('iform-row-2col');
			jQuery('#field-account-tgCode').hide();
			mbs = mbs.sort(function(a,b){if(a.Amount != b.Amount) return a.Amount>b.Amount; return a.Type>b.Type;});
			for ( var m in mbs ) {
				jQuery("<option></option>")
					.text(mbs[m]['Name']+' - '+numeral(mbs[m]['Amount']).format('$0,000.00'))
					.val(mbs[m]['Id'])
					.attr({
						'data-type':mbs[m]['Type'],
						'data-period':mbs[m]['Period'],
						'data-amount':mbs[m]['Amount'],
						'data-desc':mbs[m]['Description']
					})
					.appendTo(self.mbSelect);
			}

			if(!!QueryString.mb) {
				var mb = Number(QueryString.mb), mbtarget = false;
				if(mb) {
					mbtarget = '#Membership_Level__c :nth-child('+mb+')';
				} else {
					mbtarget = '#Membership_Level__c [value="'+QueryString.mb+'"]';
				}
				if(mbtarget) {
					mbtarget = jQuery(mbtarget);
					if(mbtarget.length) {
						mbtarget.prop('selected',true);
					}
				}
			}
			jQuery('#Membership_Level__c').trigger("render");
			
		};

		this.pad = function(n, l) {
			var s = '' + n;
			while (s.length < l) {
				s = '0' + s;
			}
			return s;
		};

		this.buildCCYearDD = function() {
			var cc_year = jQuery("#cc_year"), cc_month = jQuery('#cc_month'), y = new Date()
					.getFullYear(), z = y + 10, i;
			for (i = y; i <= z; i++) {
				jQuery('<option></option>').text(i).val(i).appendTo(
						cc_year);
			}
			cc_year.val(y).trigger("render");
			for (i = 1; i <= 12; i++) {
				z = self.pad(i, 2);
				jQuery('<option></option>').text(z).val(z).appendTo(cc_month);
			}
			cc_month.val('01').trigger("render");
		};

		this.fieldClearValidate = function(f) {
			jQuery(f).closest('.field').removeClass('validating').removeClass(
					'validated').removeClass('validate-error').removeClass(
					'validate-ok');
		};

		this.fieldValidating = function(f) {
			self.fieldClearValidate(f);
			jQuery(f).closest('.field').addClass('validating');
		};

		this.fieldValidated = function(f, r) {
			self.fieldClearValidate(f);
			var c = jQuery(f).closest('.field');
			c.addClass('validated');
			if (r) {
				c.addClass('validate-ok');
			} else {
				c.addClass('validate-error');
			}
		};

		this.validateField = function(f, p, c, m) {
			self.fieldValidating(f);

			var r = 0, v = jQuery(f).val();
			if (!p) {
				if (v) {
					r = -1;
				}
			} else {
				v = new String(v);
				if (v.match(p)) {
					r = -2;
				}
			}

			if ((r == -1) && !!c) {
				r = c(f);
				if (r === false) {
					r = 1;
				}
				if (r === true) {
					r = -1;
				}
			}

			self.fieldValidated(f, (r <= -1));
			if (!!m && (r > -1)) {
				jQuery(f).closest('.field').find('.errmsg').html(m[r]);
			}
			return !!r;
		};

		this.validateThisField = function(f) {
			switch (f) {
				case 'tgCode':
				case 'wp-username':
				case 'contact-Email':
					// We handle these through another path because AJAX.
					return true;
				case 'wp-password1':
				case 'wp-password2':
					return jQuery(f).closest('.field').hasClass('validated-ok');
				case 'cc_num':
					return self.validateCC();
				case 'ccv':
					return self.validateCC();
				case 'cc_month':
				case 'cc_year':
					return self.validateCCExp();
				case 'agree2terms':
					return self.validateTermAcceptance();
			}
			return self.validateField('#' + f);
		};

		this.validateTermAcceptance = function() {
			self.fieldValidating("#agree2terms");
			var r = jQuery("#agree2terms").prop("checked");
			self.fieldValidated("#agree2terms", r);
			return r;
		};

		this.validateCCExp = function() {
			self.fieldValidating("#cc_month");
			var r = self.checkCCDate();
			self.fieldValidated("#cc_month", r);
			return r;
		};

		this.usrNameValid = function() {
			if (self.usrNameChk === true)
				return true;
			return false;
		};

		this.usrEmailValid = function() {
			if (self.usrEmailChk === true)
				return true;
			return false;
		};

		this.usrNameStatus = function() {
			if (self.usrNameChk === false)
				return -1;
			if (self.usrNameChk === true)
				return 1;
			return 2;
		};

		this.usrEmailStatus = function() {
			if (self.usrEmailChk === false)
				return -1;
			if (self.usrEmailChk === true)
				return 1;
			if (self.usrEmailChk === -4)
				return 3;
			return 2;
		};

		this.validateUsername = function() {
			return self.validateField('#wp-username', false, self.usrNameStatus, [
					"Please enter a username.",
					"That username is already associated with an account. Please try again, or <a href=\"/login\">log in</a>.",
					"Error validating user name.  Try again in a moment." ]);
		};

		this.validateEmail = function() {
			return self.validateField('#contact-Email', false, self.usrEmailStatus, [
					"Please enter an email.",
					"That email is already associated with an account. Please try again, or <a href=\"/login\">log in</a>.",
					"Error validating email.  Try again in a moment.",
					"Please enter a valid email address." ]);
		};

		this.validatePassword = function() {
			var settings = {
				minLength : 8,
				mixedCase : false,
				numbers : true,
				specialChars : false
			};
			var p = jQuery("#wp-password1").val(), r = true, t = jQuery(
					"#wp-password1").closest(".field").find(".errmsg"), v = jQuery(
					"#wp-password2").closest(".field").find(".errmsg");
			jQuery('#passlen_num').text(settings.minLength);
			if (settings.minLength < 1)
				jQuery('#passlen').remove();
			if (!settings.mixedCase)
				jQuery('#passcase').remove();
			if (!settings.numbers)
				jQuery('#passnum').remove();
			if (!settings.specialChars)
				jQuery('#passchar').remove();
			t.removeClass('faildetail');

			self.fieldClearValidate("#wp-password1");
			self.fieldClearValidate("#wp-password2");

			self.fieldValidating("#wp-password1");

			if (p.length < settings.minLength) {
				r = false;
				t.addClass('passlen-fail').addClass('faildetail');
			} else {
				t.removeClass('passlen-fail');
			}

			if (settings.mixedCase
					&& !p.match(/.*([a-z].*[A-Z]|[A-Z].*[a-z]).*/)) {
				r = false;
				t.addClass('passcase-fail').addClass('faildetail');
			} else {
				t.removeClass('passcase-fail');
			}

			if (settings.numbers && !p.match(/[0-9]/)) {
				r = false;
				t.addClass('passnum-fail').addClass('faildetail');
			} else {
				t.removeClass('passnum-fail');
			}

			if (settings.specialChars && !p.match(/[^a-z0-9]/i)) {
				r = false;
				t.addClass('passchar-fail').addClass('faildetail');
			} else {
				t.removeClass('passchar-fail');
			}

			self.fieldValidated("#wp-password1", r);

			var pv = jQuery("#wp-password2").val();
			if (((p != '') && (pv != '')) || self.validatingForm) {
				self.fieldValidating("#wp-password2");

				if ((p != pv) || (p == '')) {
					self.fieldValidated("#wp-password2", false);
					r = false;
				} else {
					self.fieldValidated("#wp-password2", true);
				}
			}

			return r;
		};

		this.validateCC = function() {
			var valid = true;

			self.fieldValidating('#cc_num');
			self.fieldValidating('#ccv');

			var cc_result = jQuery("#cc_num")
					.validateCreditCard( {accept:['amex','visa','visa_electron','mastercard','maestro','discover','jcb']} );
			if (!cc_result.valid) {
				self.fieldValidated('#cc_num', false);
				self.fieldValidated('#ccv', false);
				return false;
			}

			self.fieldValidated('#cc_num', true);
			jQuery('#cc_type').val(cc_result.card_type.name);

			var cvv = jQuery('#ccv').val();
			if (!cvv) {
				self.fieldValidated('#ccv', false);
				return false;
			}

			if (cc_result.valid
					&& (cvv.length != cc_result.card_type.cvv_length)) {
				self.fieldValidated('#ccv', false);
				return false;
			}

			self.fieldValidated('#ccv', true);
			return true;
		};

		this.isFieldValidatedOk = function(id) {
			var t = jQuery('#'+id);
			if ( ! t.length ) {
				return false;
			}
			t = t.parent();
			if ( t.hasClass( 'validated' ) ) {
				return t.hasClass( 'validate-ok' );
			}
			if ( t.hasClass( 'validating' ) ) {
				return false;
			}
			return true;
		}

		this.validateForm = function() {
			var msg = "";
			var complete = true;
			self.validatingForm = true;

			// jQuery('.validate').text('');

			complete = self.isFieldValidatedOk('contact-FirstName') && complete;
			complete = self.isFieldValidatedOk('contact-LastName') && complete;
			complete = self.isFieldValidatedOk('contact-Email') && complete;
			complete = self.isFieldValidatedOk('contact-HomePhone') && complete;

			complete = self.validateUsername() && complete;
			complete = self.validatePassword() && complete;

			if (showTrial) {
				complete = jQuery('#field-account-tgCode').hasClass('validate-ok') && complete;
			}

			complete = self.validateTermAcceptance() && complete;

			if (showBilling) {
				complete = self.isFieldValidatedOk('s_address1') && complete;
				complete = self.isFieldValidatedOk('s_city') && complete;
				complete = self.isFieldValidatedOk('s_state') && complete;
				complete = self.isFieldValidatedOk('s_zip') && complete;
				complete = self.isFieldValidatedOk('s_country') && complete;

				complete = self.validateCC() && complete;
				complete = self.checkCCDate() && complete;
			}

			self.validatingForm = false;

			if (!complete) {
				var to = jQuery("#tab-container").find('.validate-error');
				if (to.length)
					jQuery.scrollTo(to.first().closest('.inputgroup').parent());
				return;
			}

			self.processPayJq();
		};

		this.processPayJq = function() {
			if (self.processing) {
				return;
			}

			self.processing = true;
			jQuery('#subTab3').addClass('disabled');

			var args = {
				action : "idj-signup",
				mode : "create",
				fn : jQuery("#contact-FirstName").val(),
				ln : jQuery("#contact-LastName").val(),
				email : jQuery("#contact-Email").val(),
				l : self.mbSelect.val(),
				phone : jQuery('#contact-HomePhone').val(),
				username : jQuery("#wp-username").val(),
				password : jQuery("#wp-password1").val(),
				s_address1 : jQuery("#s_address1").val(),
				s_city : jQuery("#s_city").val(),
				s_state : jQuery("#s_state").val(),
				s_zip : jQuery("#s_zip").val(),
				s_country : jQuery("#s_country").val(),
				cc_num : jQuery('#cc_num').val(),
				cc_mon : jQuery('#cc_month').val(),
				cc_yr : jQuery('#cc_year').val(),
				cc_cvv : jQuery('#ccv').val(),
				cc_type : jQuery('#cc_type').val(),
				trialid : jQuery("#tgCode").val(),
				hearabout : jQuery("#contact-HearAbout").val(),
				referby : jQuery("#contact-ReferredBy").val()
			};
			
			if ( ( typeof _x !== "undefined" ) & ( typeof _x.signupnonce !== "undefined" ) ) {
				args._n = jQuery("#"+_x.signupnonce).val();
			}
			
			var otherparam = [ "pc", "gdsType", "cin", "cout" ];
			var addparam = "";
			var otherempty = false;
			for (var i = 0; i < otherparam.length; i++) {
				if (swifttriparm[otherparam[i]] != undefined) {
					args[otherparam[i]] = swifttriparm[otherparam[i]];
				} else {
					args[otherparam[i]] = '';
					otherempty = true;
				}
			}

			var result = {};

			jQuery
					.ajax('/wp-admin/admin-ajax.php', {
						method : "POST",
						data : args
					})
					.done(function(d, s, x) {
						result = d;
					})
					.fail(
							function(x, s, e) {
								if(progressDialog) {
									progressDialog.close();
								}
								result = {
									r_approved : 'ERROR',
									r_code : 0,
									r_error : 'Error communicating with payment system.'
								};
								jQuery('#subTab3').removeClass('disabled');
							})
					.always(
							function() {
								if(progressDialog) {
									progressDialog.close();
								}
								var s = false, b = '#lightbox-signup-complete';

								if ( result.success ) {
									_setrm('#memberdate>span', result.data.startdate );
									_setrm('#memberenddate>span', result.data.enddate );
									_setrm('#membercardholder>span', jQuery('#contact-FirstName').val() + ' ' + jQuery('#contact-LastName').val() );
									_setrm('#membercard>span', result.data.cardnum.substr(result.data.cardnum.length - 4) );
									_setrm('#membertransaction>span', result.data.r_ref );
									_setrm('#memberlength>span', result.data.length );
									_setrm('#memberlevel>span', result.data.membertype );
									if(result.data.price>0) {
										_setrm('#membercost>span', numeral(result.data.price).format('$0,000.00') );
									} else {
										_setrm('#membercost>span', false );
									}

									jQuery('#membercomplete').on('click',
											function() {
												window.location = "/welcome/";
											});
									b = '#lightbox-signup-complete';
									s = false;
								} else {
									jQuery('#errordetail')
											.html(
													jQuery(
															'<span class="errormsg"></span>')
															.text(
																	result.data.message));
									b = '#lightbox-signup-error';
									s = true;
									jQuery('#subTab3').removeClass('disabled');
								}

								jQuery.magnificPopup.open({
									items : {
										type : 'inline',
										src : b,
										midClick : true
									},
									closeOnBgClick : s,
									enableEscapeKey : s,
									showCloseBtn : s
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

		this.tgCodeLookup = function() {
			self.fieldValidating('#tgCode');

			var c = jQuery.trim(jQuery('#tgCode').val());
			if (c == '') {
				document.getElementById("tg_codeval").innerHTML = '';
				self.fieldValidating('#tgCode');
				return;
			}

			jQuery
					.ajax('/wp-admin/admin-ajax.php', {
						method : 'POST',
						data : {
							action : "idj-trial",
							rc : c
						}
					})
					.done(
							function(result) {
								var mb_select = jQuery("#Membership_Level__c");

								showBilling = (result.payment && (result.amount > 0));
								self.fixBilling();
								jQuery("#formdescription").html( result.pagetext );
								
								if (!result.valid) {
									self.fieldValidated('#tgCode', false);
									return;
								}

								self.fieldValidated('#tgCode', true);

								jQuery("#refCode").val(jQuery("#tgCode").val());

								var option = document.createElement("option");
								option.text = result.name;
								if(showBilling) {
									option.text += ' - '+numeral(result.amount).format('$0,000.00');
								}
								option.value = result.name;
								option.selected = "selected";
								var t = jQuery('#Membership_Level__c>option[value="'
										+ option.value + '"]');
								if (t.length > 0) {
									t.remove();
								}
								self.mbSelect.append(option).trigger(
										'render');
								self.mbSelect.attr('disabled', 'true');

								jQuery(".inputgroup").show();
								jQuery(".inputgrouptitle").show();
							})
					.fail(
							function(x, s, e) {
								jQuery("#tg_codeval")
										.html(
												'Error validating code.  Please try again in a moment.');
								self.fieldValidated('#tgCode', false);
							});
		};

		this.checkUsername = function() {
			self.fieldValidating('#wp-username');
			jQuery.ajax('/wp-admin/admin-ajax.php', {
				method : 'POST',
				data : {
					action : 'idj-login',
					login : jQuery("#wp-username").val()
				}
			}).done(function(d, s, x) {
				self.usrNameChk = d.exists;
			}).fail(function(x, s, e) {
				self.usrNameChk = null;
			}).always(function() {
				self.validateUsername();
			});
		};

		this.checkEmail = function() {
			if (!self
					.validateField(
							'#contact-Email',
							/^("[^"]+"|[-a-z0-9+_'][-a-z0-9+\._']*[-a-z0-9+_']|[-a-z0-9+_']+)@([a-z0-9][-a-z0-9]*[a-z0-9]\.)+[a-z0-9][-a-z0-9]*[a-z0-9]$/i)) {
				self.usrEmailChk = -4;
				self.validateEmail();
				return;
			}
			self.fieldValidating('#contact-Email');
			jQuery.ajax('/wp-admin/admin-ajax.php', {
				method : 'POST',
				data : {
					action : 'idj-email',
					email : jQuery("#contact-Email").val()
				}
			}).done(function(d, s, x) {
				self.usrEmailChk = d.exists;
			}).fail(function(x, s, e) {
				self.usrEmailChk = null;
			}).always(function() {
				self.validateEmail();
			});
		};

		this.setAddr = function() {
			var chkbox = document.getElementById("chkShip");
			if (chkbox.checked) {
				jQuery("#address1").val(jQuery("#s_address1").val()).attr(
						'readOnly', 'true');
				jQuery("#city").val(jQuery("#s_city").val()).attr('readOnly',
						'true');
				jQuery("#zip").val(jQuery("#s_zip").val()).attr('readOnly',
						'true');
				jQuery("#country").val(jQuery("#s_country").val()).attr(
						'readOnly', 'true');
				jQuery('#country').trigger('render');
				populateStates('country', 'state');
				jQuery("#state").val(jQuery("#s_state").val()).attr('readOnly',
						'true');
				jQuery('#state').trigger('render');
			} else {
				jQuery("#address1").removeAttr('readOnly');
				jQuery("#city").removeAttr('readOnly');
				jQuery("#zip").removeAttr('readOnly');
				jQuery("#country").removeAttr('readOnly');
				jQuery('#country').trigger('render');
				jQuery("#state").removeAttr('readOnly');
				jQuery('#state').trigger('render');
			}
		};

		this.checkCCDate = function() {
			var d = new Date(), cm = Number(jQuery('#cc_month').val()), cy = Number(jQuery(
					'#cc_year').val()), m = Number(d.getMonth()), y = Number(d
					.getFullYear().toString().substr(2, 2));
			if (cy < y)
				return false;
			if ((cy == y) && (cm <= m))
				return false;
			return true;
		};
	}

	signup = new signupObj();
}

(function() {
	function init() {
		signup.selfInit();
		
		if (!showTrial) {
			jQuery('#signup-form-container').addClass('signup');
		} else {
			jQuery('#signup-form-container').addClass('trial');
			signup.mbSelect.prop('disabled',true).prop('readonly',true);
		}

		signup.initFields();
		signup.buildMembershipDD();

		if ((trialCode != 'false') && (trialCode != '')) {
			jQuery("#tgCode").val(trialCode);
			signup.tgCodeLookup();
		}
		signup.buildButtonEventMgrs();
		signup.buildCCYearDD();
	}
	if (window.addEventListener) {
		window.addEventListener('DOMContentLoaded', init, false);
	} else {
		window.attachEvent('onload', init);
	}
}());
function _setrm(t,v) {
	if(!v || v=='') {
		jQuery(t).parent().remove();
		return;
	}
	jQuery(t).html(v);
}