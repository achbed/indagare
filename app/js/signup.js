var signup;
if (!signup) {
	function signupObj() {
		var self = this;
        
		this.usrNameChk = false;
		this.mbSelect = false;
		this.mbySelect = false;
		this.processing = false;
		this.validatingForm = false;

		this.selfInit = function() {
			if (!self.mbSelect || !self.mbSelect.length) {
				self.mbSelect = jQuery("#membership_level");
				self.mbySelect = jQuery("#membership_years");
    }   
		};

		this.initFields = function() {
			self.selfInit();
			
			if(showTrial) {
				jQuery('.tab').addClass('is-trial');
			}
			
       document.getElementById("ln").value = acc.lastname;
       document.getElementById("fn").value = acc.firstname;
       document.getElementById("email").value = acc.email;
       document.getElementById("refCode").value = rc;
			
       if (redirect=="swifttrip") {	   
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
			}).on('change', '#username', function(e) {
				self.checkUsername();
			}).on('change', '#membership_level', function(e) {
				self.setSelectedMBYears();
			}).on('change', '#chkShip', function(e) {
				self.setAddr();
			}).on('change', 'input', function(e) {
				self.validateThisField( jQuery(this).attr('id') );
			}).on('change', 'select', function(e) {
				self.validateThisField( jQuery(this).attr('id') );
			}).on('change','#shippingBlock input', function(e) {
				self.setAddr();
			});
        };

		this.buildMembershipDD = function() {
			if(showTrial) {
				return;
			}
			
			self.selfInit();
			var s = false;
       for (var m in mbs) {
				jQuery("<option></option>").text(mbs[m].name).val(m).appendTo(self.mbSelect);
				if ( !s ) {
					s = m;
           }
				if ( mbs[m].level == mb ) {
					self.mbSelect.val(m);
					s = m;
           }
           }
			if ( !s ) {
				self.mbSelect.val(s);
       }
			
			jQuery('#membership_level').trigger("render");
			self.setSelectedMBYears();
       };

		this.createYearOption = function(p, l, d, v, m) {
			if(d) p = p * (1-(d/100));
			var t = "$" + Math.floor(p / 100) + ".00 for " + l + " year";
			if (l != 1) {
				t += 's';
           }
			if (d) {
				t += ' (' + m + ')';
           }
			jQuery('<option></option>').text(t).val(v).appendTo(self.mbySelect);
		};
        
		this.setSelectedMBYears = function() {
			if(showTrial) {
				return;
			}
			
			self.selfInit();

			var m = self.mbSelect.val(), p = self.mbySelect.val();
			if(!m) { m = "0"; }
			if(!p) { p = "1"; }
			self.mbySelect.html('');
			
			// Normal list of membership years
			self.createYearOption(mbs[m].p1, 1, dc, "1", dc_msg);
		
			// display all years with no discount code
			if ( dc == 0 ) {
				self.createYearOption(mbs[m].p2, 2, 0, "2", '');
				self.createYearOption(mbs[m].p3, 3, 0, "3", '');
				self.mbySelect.val(p);
			} else {
				self.mbySelect.val("1");
            }

			jQuery('#membership_years').trigger("render");
		};

		this.pad = function(n,l) {
		    var s = '' + n;
		    while (s.length < l) {
		        s = '0' + s;
		    }
		    return s;
		};

		this.buildCCYearDD = function() {
			var cc_year = jQuery("#cc_year"),
				cc_month = jQuery('#cc_month'),
				y = new Date().getFullYear(),
				z = y + 10,
				i;
			for (i = y; i <= z; i++) {
				jQuery('<option></option>').text(i).val(i-2000).appendTo(cc_year);
			}
			cc_year.val(y-2000).trigger("render");
			for ( i=1; i<=12; i++ ) {
				z = self.pad(i,2);
				jQuery('<option></option>').text(z).val(z).appendTo(cc_month);
			}
			cc_month.val('01').trigger("render");
		};

		this.fieldClearValidate = function(f) {
			jQuery(f).closest('.field').removeClass('validating').removeClass('validated').removeClass('validate-error').removeClass('validate-ok');
		};

		this.fieldValidating = function(f) {
			self.fieldClearValidate(f);
			jQuery(f).closest('.field').addClass('validating');
		};

		this.fieldValidated = function(f,r) {
			self.fieldClearValidate(f);
			var c = jQuery(f).closest('.field');
			c.addClass('validated');
			if(r) {
				c.addClass('validate-ok');
			} else {
				c.addClass('validate-error');
        }
		};
		
		this.validateField = function(f,p,c,m) {
			self.fieldValidating(f);
			
			var r = 0, v = jQuery(f).val();
			if(!p) {
				if(v) {
					r = -1;
        }
			} else {
				v = new String(v);
				if(v.match(p)) {
					r = -2;
				}
			}
			
			if((r == -1) && !!c) {
				r = c(f);
				if ( r === false ) { r = 1; }
				if ( r === true ) { r = -1; }
			}
			
			self.fieldValidated(f,(r <= -1));
			if(!!m && (r > -1)) {
				jQuery(f).closest('.field').find('.errmsg').html(m[r]);
			}
			return !!r;
		};
		
		this.validateThisField = function(f) {
			var c = document.getElementById("chkShip").checked;
			switch(f) {
				case 'tgCode':
				case 'username':
					// We handle these through another path because AJAX.
					return true;
				case 'password1':
				case 'password2':
					return self.validatePassword();
				case 'email':
					return self.validateField('#'+f,/^("[^"]+"|[-a-z0-9+_'][-a-z0-9+\._']*[-a-z0-9+_']|[-a-z0-9+_']+)@[a-z0-9][-a-z0-9]*[a-z0-9]\.[a-z0-9][-a-z0-9]*[a-z0-9]$/i);
				case 'cc_num':
					return self.validateCC();
				case 'ccv':
					return self.validateCC();
				case 'cc_month':
				case 'cc_year':
					return self.validateCCExp();
				case 'agree2terms':
					return self.validateTermAcceptance();
				case 'address2':
				case 's_address2':
					// Don't even bother validating these
					return true;
				case 'address1':
				case 'city':
				case 'state':
				case 'zip':
				case 'country':
					if ( c ) {
						return true;
					}
					break;
				case 'chkShip':
					if(!c) {
						// If we're turning OFF, revalidate.
						self.validateField('#address1');
						self.validateField('#city');
						self.validateField('#state');
						self.validateField('#zip');
						self.validateField('#country');
					} else {
						// If we're turning ON, clear any validation
						self.fieldClearValidate('#address1');
						self.fieldClearValidate('#city');
						self.fieldClearValidate('#state');
						self.fieldClearValidate('#zip');
						self.fieldClearValidate('#country');
					}
					// And never validate the checkbox itself.
					return true;
			}
			return self.validateField('#'+f);
		};
		
		this.validateTermAcceptance = function() {
			self.fieldValidating( "#agree2terms" );
			var r = jQuery("#agree2terms").prop("checked");
			self.fieldValidated( "#agree2terms", r );
			return r;
		};
		
		this.validateCCExp = function() {
			self.fieldValidating( "#cc_month" );
			var r = self.checkCCDate();
			self.fieldValidated( "#cc_month", r );
			return r;
		};
		
		this.usrNameValid = function() {
			if(self.usrNameChk === true) return true;
			return false;
		};
		
		this.usrNameStatus = function() {
			if(self.usrNameChk === false) return -1;
			if(self.usrNameChk === true) return 1;
			return 2;
		};
		
		this.validateUsername = function() {
			return self.validateField(
					'#username', 
					false, 
					self.usrNameStatus, 
					["Please enter a username.","That username is already taken.","Error validating user name.  Try again in a moment."] 
			);
		};
		
		this.validatePassword = function() {
			var settings = {
				minLength: 6,
				mixedCase: true,
				numbers: true,
				specialChars: false
			};
			var p = jQuery("#password1").val(), 
				r = true, 
				t = jQuery("#password1").closest(".field").find(".errmsg"),
				v = jQuery("#password2").closest(".field").find(".errmsg");
			jQuery('#passlen_num').text(settings.minLength);
			if(settings.minLength<1) jQuery('#passlen').remove();
			if(!settings.mixedCase) jQuery('#passcase').remove();
			if(!settings.numbers) jQuery('#passnum').remove();
			if(!settings.specialChars) jQuery('#passchar').remove();
			t.removeClass('faildetail');
			
			self.fieldClearValidate("#password1");
			self.fieldClearValidate("#password2");
			
			self.fieldValidating("#password1");

			if ( p.length < settings.minLength ) {
				r = false;
				t.addClass('passlen-fail').addClass('faildetail');
			} else {
				t.removeClass('passlen-fail');
			}
			
			if ( settings.mixedCase && ! p.match(/.*([a-z].*[A-Z]|[A-Z].*[a-z]).*/) ) {
				r = false;
				t.addClass('passcase-fail').addClass('faildetail');
			} else {
				t.removeClass('passcase-fail');
			}
			
			if ( settings.numbers && ! p.match(/[0-9]/) ) {
				r = false;
				t.addClass('passnum-fail').addClass('faildetail');
			} else {
				t.removeClass('passnum-fail');
			}
			
			if ( settings.specialChars && ! p.match(/[^a-z0-9]/i) ) {
				r = false;
				t.addClass('passchar-fail').addClass('faildetail');
			} else {
				t.removeClass('passchar-fail');
        }

			self.fieldValidated( "#password1", r);
			
			var pv = jQuery("#password2").val();
			if ( ( ( p != '' ) && ( pv != '' ) ) || self.validatingForm ) {
				self.fieldValidating("#password2");
				
				if ( ( p != pv ) || ( p == '' ) ) {
					self.fieldValidated( "#password2", false );
					r = false;
				} else {
					self.fieldValidated( "#password2", true );
				}
            }
				
			return r;
		};
				
		this.validateCC = function() {
			var valid = true;
			
			self.fieldValidating('#cc_num');
			self.fieldValidating('#ccv');
			
				var cc_result = jQuery("#cc_num").validateCreditCard( /* {accept:['amex','visa','mastercard','discover','jcb']} */ );
				if (!cc_result.valid) {
				self.fieldValidated('#cc_num',false);
				self.fieldValidated('#ccv', false);
				return false;
            }
				
			self.fieldValidated('#cc_num',true);
			
				var cvv = jQuery('#ccv').val();
				if ( !cvv ) {
				self.fieldValidated('#ccv', false);
				return false;
            }
				
			if ( cc_result.valid && (cvv.length != cc_result.card_type.cvv_length ) ) {
				self.fieldValidated('#ccv', false);
				return false;
            }
			
			self.fieldValidated('#ccv', true);
			return true;
		};

		this.validateForm = function() {
			var msg = "";
			var complete = true;
			self.validatingForm = true;

			//jQuery('.validate').text('');

			complete = self.validateThisField('fn') && complete;
			complete = self.validateThisField('ln') && complete;
			complete = self.validateThisField('email') && complete;
			complete = self.validateThisField('phone') && complete;
			
			complete = self.validateThisField('s_address1') && complete;
			complete = self.validateThisField('s_city') && complete;
			complete = self.validateThisField('s_state') && complete;
			complete = self.validateThisField('s_zip') && complete;
			complete = self.validateThisField('s_country') && complete;
			
			complete = self.validateUsername() && complete;
			complete = self.validatePassword() && complete;
			
			complete = self.validateTermAcceptance() && complete;
			
			if (!showTrial) {
				
				complete = self.validateThisField('address1') && complete;
				complete = self.validateThisField('city') && complete;
				complete = self.validateThisField('state') && complete;
				complete = self.validateThisField('zip') && complete;
				complete = self.validateThisField('country') && complete;
				
				complete = self.validateThisField('cc_holder') && complete;
				complete = self.validateThisField('cc_num') && complete;
				complete = self.validateThisField('cc_month') && complete;
			}

			self.validatingForm = false;
			
			if ( ! complete ) {
				var to = jQuery("#tab-container").find('.validate-error');
				if(to.length)
					jQuery.scrollTo(to.first().closest('.inputgroup').parent());
				return;
        }

			if (showTrial) {
					self.processTrialJq();
				} else {
					self.processPayJq();
            }
                        };

		this.processTrialJq = function() {
			if (self.processing) {
				return;
			}

			self.processing = true;

			jQuery('#subTab3').addClass('disabled');

			jQuery.ajax('/wp-content/themes/indagare/app/lib/iajax.php', {
				method : "POST",
				data : {
					task : "newTrial_j",
					fn : jQuery('#fn').val(),
					ln : jQuery('#ln').val(),
					email : jQuery('#email').val(),
					phone : jQuery('#phone').val(),
					username : jQuery('#username').val(),
					password : jQuery('#password1').val(),
					s_address1 : jQuery('#s_address1').val(),
					s_address2 : jQuery('#s_address2').val(),
					s_city : jQuery('#s_city').val(),
					s_state : jQuery('#s_state').val(),
					s_zip : jQuery('#s_zip').val(),
					s_country : jQuery('#s_country').val(),
					passKey : jQuery('#refCode').val()
				}
			}).done(function(d, s, x) {
				if (d.success) {
					jQuery('#memberdate>span').html(d.startdate);
					jQuery('#membercost').html('');
					jQuery('#memberlength>span').html(d.length);
					jQuery('#membercardholder').html('');
					jQuery('#membercard').html('');
					jQuery('#membertransaction').html('');
					jQuery('#memberlevel>span').html(d.name);
					jQuery('#membercomplete').on('click', function(e) {
						e.preventDefault();
						window.location = '/welcome/';
					});
                        jQuery.magnificPopup.open({
                              items: {
                                    type: 'inline',
							src : '#lightbox-signup-complete', // can be a HTML
							// string,
							// jQuery
							// object, or
							// CSS selector
                                    midClick: true
                              },
						closeOnBgClick: false,
						enableEscapeKey: false,
						showCloseBtn: false
                        });
				} else {
					jQuery('#errordetail').html( jQuery('<span class="errormsg"></span>').text(result.err) );
                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
							src : '#lightbox-signup-error', // can be a HTML
							// string, jQuery
							// object, or CSS
							// selector
                                midClick: true
						}
                        });
					jQuery('#subTab3').removeClass('disabled');
                    }
			}).fail(function(x, s, e) {
				jQuery('#errordetail').html( jQuery('<span class="errormsg"></span>').text(result.err) );
				jQuery.magnificPopup.open({
					items : {
						type : 'inline',
						src : '#lightbox-signup-error', // can be a HTML string,
						// jQuery object, or CSS
						// selector
						midClick : true
					}
				});
				jQuery('#subTab3').removeClass('disabled');
			}).always(function() {
				self.processing = false;
			});
            };

		this.processPayJq = function() {
			if (self.processing) {
				return;
			}

			self.processing = true;
			jQuery('#subTab3').addClass('disabled');
			
			var args = { 
					task: "payment_j",
					fn: jQuery("#fn").val(),
					ln: jQuery("#ln").val(),
					email: jQuery("#email").val(),
					l: self.mbSelect.val(),
					y: self.mbySelect.val(),
					username: jQuery("#username").val(),
					password: jQuery("#password1").val(),
					s_address1: jQuery("#s_address1").val(),
					s_address2: jQuery("#s_address2").val(),
					s_city: jQuery("#s_city").val(),
					s_state: jQuery("#s_state").val(),
					s_zip: jQuery("#s_zip").val(),
					s_country: jQuery("#s_country").val(),
					cc_holder: jQuery("#cc_holder").val(),
					cc_num: jQuery("#cc_num").val(),
					cc_m: jQuery("#cc_month").val(),
					cc_y: jQuery("#cc_year").val(),
					ccv: jQuery("#ccv").val(),
					tgCode: jQuery("#tgCode").val(),
					dc: jQuery("#dc").val()
			};
			var cc_month = jQuery("#cc_month");
			var cc_year = jQuery("#cc_year");
            var otherparam=["pc","gdsType","cin","cout"];
            var addparam="";
            var otherempty=false;
			for (var i = 0; i < otherparam.length; i++) {
				if (swifttriparm[otherparam[i]] != undefined) {
					args[otherparam[i]] = swifttriparm[otherparam[i]];
				} else {
					args[otherparam[i]] = '';
                otherempty=true;   
              } 	  
            }
                        	
			var result = {};
			
			jQuery.ajax('/wp-content/themes/indagare/app/lib/iajax.php', {
				method : "POST",
				data : args
			}).done(function(d, s, x) {
				result = d;
			}).fail(function(x, s, e) {
				result = {
					r_approved: 'ERROR',
					r_code: 0,
					r_error: 'Error communicating with payment system.'
                                };	
				jQuery('#subTab3').removeClass('disabled');
			}).always(function() {
				var s = false, b = '#lightbox-signup-complete';

				if ( result.r_approved == 'APPROVED' ) {
					jQuery('#memberdate>span').html(result.startdate);
					jQuery('#membercardholder>span').html(args.cc_holder);
					jQuery('#membercard>span').html(args.cc_num.substr(args.cc_num.length - 4));
					jQuery('#membertransaction>span').html(result.r_ref);
					jQuery('#memberlength>span').html(result.length);
					jQuery('#memberlevel>span').html(result.name);
					jQuery('#membercost>span').html(numeral(result.price).format('$0,000.00'));
                                    
					jQuery('#membercomplete').on('click', function() {
						window.location = "/welcome/";
                        });
					b = '#lightbox-signup-complete';
					s = false;
				} else {
					jQuery('#errordetail').html( jQuery('<span class="errormsg"></span>').text(result.r_error) );
					b = '#lightbox-signup-error';
					s = true;
					jQuery('#subTab3').removeClass('disabled');
                    }
                    	
                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
						src : b,
						midClick : true
                            },
					closeOnBgClick: s,
					enableEscapeKey: s,
					showCloseBtn : s
                        });
				
				self.processing = false;
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
                   
			jQuery("#tg_codeval").html('This is not a valid code.');
			jQuery.ajax("/wp-content/themes/indagare/app/lib/iajax.php", {
				method : 'POST',
						data : {
					task : "chkTrialKey_j",
							rc : c
                    } 
			}).done(function(result) {
								var mb_select = jQuery("#membership_level");
								var mby_select = jQuery("#membership_years");

								if (!result.valid) {
										jQuery(".inputgroup").hide();
										jQuery(".inputgrouptitle").hide();

					self.fieldValidated('#tgCode', false);
					return;
								}

				self.fieldValidated('#tgCode', true);
				
				jQuery("#refCode").val( jQuery("#tgCode").val() );

								var option = document.createElement("option");
								option.text = result.name;
								option.value = "trial - " + result.name;
								option.selected = "selected";
								var t = jQuery('#membership_level>option[value="'
										+ option.value + '"]');
								if (t.length == 0) {
									self.mbSelect.append(option).trigger(
											'render');
								} else {
									t.prop('selected', true).change().trigger(
											'render');
        }
								self.mbSelect.attr('disabled', 'true');

								var option = document.createElement("option");
								option.text = result.length;
								option.value = "trial - " + result.length;
								option.selected = 'true';
								var t = jQuery('#membership_years>option[value="'
										+ option.value + '"]');
								if (t.length == 0) {
									self.mbySelect.append(option).trigger(
											'render');
								} else {
									t.prop('selected', true).change().trigger(
											'render');
        }
								self.mbySelect.attr('disabled', 'true');

									jQuery(".inputgroup").show();
									jQuery(".inputgrouptitle").show();
			}).fail(function(x, s, e) {
				jQuery("#tg_codeval").html('Error validating code.  Please try again in a moment.');
				self.fieldValidated('#tgCode', false);
					});
		};

		this.checkUsername = function() {
			self.fieldValidating('#username');
			jQuery.ajax('/wp-content/themes/indagare/app/lib/iajax.php', {
				method: 'POST',
				data: {
					task: 'chkLogin_j',
					login: jQuery("#username").val()
            }
			}).done(function(d, s, x) {
				self.usrNameChk = d.exists;
			}).fail(function(x, s, e) {
				self.usrNameChk = null;
			}).always(function() {
				self.validateUsername();
			});
		};

		this.setAddr = function() {
        var chkbox = document.getElementById("chkShip");
        if (chkbox.checked) {
				jQuery("#address1").val(jQuery("#s_address1").val()).attr('readOnly','true');
				jQuery("#address2").val(jQuery("#s_address2").val()).attr('readOnly','true');
				jQuery("#city").val(jQuery("#s_city").val()).attr('readOnly','true');
				jQuery("#state").val(jQuery("#s_state").val()).attr('readOnly','true');
				jQuery("#zip").val(jQuery("#s_zip").val()).attr('readOnly','true');
				jQuery("#country").val(jQuery("#s_country").val()).attr('readOnly','true');
			} else {
				jQuery("#address1").removeAttr('readOnly');
				jQuery("#address2").removeAttr('readOnly');
				jQuery("#city").removeAttr('readOnly');
				jQuery("#state").removeAttr('readOnly');
				jQuery("#zip").removeAttr('readOnly');
				jQuery("#country").removeAttr('readOnly');
        }
		};

		this.checkCCDate = function() {
			var d = new Date(),
				cm = Number( jQuery('#cc_month').val() ), 
				cy = Number( jQuery('#cc_year').val() ), 
				m = Number( d.getMonth() ),
				y = Number( d.getFullYear().toString().substr(2, 2) );
			if ( cy < y ) return false;
			if ( ( cy == y ) && ( cm <= m ) ) return false;
            return true;
		};
        }   
        
	signup = new signupObj();
        }

(function() {
	function init() {
		if (!showTrial) {
			jQuery(".inputgroup").show();
			jQuery(".inputgrouptitle").show();
			jQuery(".inputgroup.trial").hide();
		} else {
			jQuery(".inputgroup").hide();
			jQuery(".inputgrouptitle").hide();
			jQuery(".trial").show();
		}
		
		signup.initFields();
		signup.buildMembershipDD();
		
		if ( ( trialCode != 'false' ) && ( trialCode != '' ) ) {
			jQuery("#tgCode").val(trialCode);
			signup.tgCodeLookup();
		}
		signup.buildButtonEventMgrs();
		signup.buildCCYearDD();

		jQuery('.customselectdyn').customSelect();
		jQuery('.customselectdyn').wrap(
				'<span class="customSelectWrap"></span>');
		jQuery('#membership_level').css("height", "25px");
		jQuery('#membership_years').css("height", "25px");
		jQuery('#cc_month').css("height", "25px");
		jQuery('#cc_year').css("height", "25px");
		jQuery('input#dc').val(dcode);
	}
	if (window.addEventListener) {
		window.addEventListener('DOMContentLoaded', init, false);
	} else {
		window.attachEvent('onload', init);
	}
}());

