var signup;
if (!signup) {
	function signupObj() {
		var self = this;
        
		this.usrNameChk = false;
		this.isTrial = false;
		this.mbSelect = false;
		this.mbySelect = false;
		this.processing = false;

		this.selfInit = function() {
			if (!self.mbSelect || !self.mbSelect.length) {
				self.mbSelect = jQuery("#membership_level");
				self.mbySelect = jQuery("#membership_years");
    }   
		};

		this.initFields = function() {
			self.selfInit();
       document.getElementById("ln").value = acc.lastname;
       document.getElementById("fn").value = acc.firstname;
       document.getElementById("email").value = acc.email;
       document.getElementById("refCode").value = rc;
       if (redirect=="swifttrip") {	   
				jQuery("#lightbox-signup-complete")
						.append(
								'<input id="backtohotel" type="submit" value="Back to Hotel" class="button">');
       }
			jQuery('body')
        };

		this.buildButtonEventMgrs = function() {
			self.selfInit();
			jQuery('body').on('click', '#tgGiftLookup', function(e) {
				e.preventDefault();
				signup.tgCodeLookup();
			}).on('click', '#subTab3', function(e) {
				e.preventDefault();
				if (jQuery('#subTab3[disabled]').length) {
					return;
				}
				jQuery('#subTab3').attr('disabled', 'true');
				signup.validateForm();
				jQuery('#subTab3').removeAttr('disabled');
			}).on('click', '#view_terms', function(e) {
            e.preventDefault();
            jQuery('#terms').show();
			}).on('change', '#membership_level', function(e) {
				self.setSelectedMBYears();
			}).on('change', '#username', function(e) {
				self.checkUsername();
			}).on('change', '#chkShip', function(e) {
				self.setAddr();
			}).on('change','#shippingBlock input', function(e) {
				self.setAddr();
			});
        };

		this.buildMembershipDD = function() {
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

		this.createYearOption = function(p, l, d, v) {
			var t = "$" + Math.floor(p / 100) + ".00 for " + l + " year";
			if (l != 1) {
				t += 's';
           }
			if (d) {
				t += ' (' + d + '% discount applied)';
           }
			jQuery('<option></option>').text(t).val(v).appendTo(self.mbySelect);
		};
        
		this.setSelectedMBYears = function() {
			self.selfInit();

			var m = self.mbSelect.val(), p = self.mbySelect.val();
			if(!m) { m = "0"; }
			if(!p) { p = "1"; }
			self.mbySelect.html('');
			self.createYearOption(mbs[m].p1, 1, dc, "1");
		
			// display all years with no discount code
			if ( dc == 0 ) {
				self.createYearOption(mbs[m].p2, 2, 0, "2");
				self.createYearOption(mbs[m].p3, 3, 0, "3");
				self.mbySelect.val(p);
			} else {
				self.mbySelect.val("1");
            }

        jQuery('#membership_years').trigger("render");
		};

		this.buildCCYearDD = function() {
			var cc_year = jQuery("#cc_year");
			var y = new Date().getFullYear();
			var z = y + 10;
			for (var i = y; i <= z; i++) {
				jQuery('<option></option>').text(i).val(i-2000).appendTo(cc_year);
			}
		};

		this.validateForm = function() {
			var msg = "";
        var complete = true;

		jQuery('.validate').text('');

        if (document.getElementById("fn").value == '') {
            msg = "Please enter a first name.";
            jQuery('#fnval').text(msg);
            complete = false;
        } 
        if (document.getElementById("ln").value == '') {
           msg = "Please enter a last name.";
            jQuery('#lnval').text(msg);
            complete = false;
        }
			if (!document.getElementById("email").value.match(/.+@.+\...+$/)) {
            msg = "Please enter a valid email address.";
            jQuery('#emailval').text(msg);
            complete = false;
        }
        if (document.getElementById("phone").value == '') {
            msg = "Please enter a phone number.";
            jQuery('#phoneval').text(msg);
            complete = false;
        }
			if (document.getElementById("s_address1").value == ''
					|| document.getElementById("s_city").value == ''
					|| document.getElementById("s_zip").value == ''
					|| document.getElementById("s_country").value == '') {
            msg = "Please enter a complete shipping address";
			jQuery('#shipval').text(msg);
            complete = false;    
        }
        
        if (document.getElementById("agree2terms").checked == false) {
				msg = "You have to agree to our Terms & Conditions.";
			jQuery('#tab3_TandC_info').text(msg);
            complete = false;
        }
        if (document.getElementById("username").value == '') {
            msg = "Please enter a username.";
			jQuery('#tab3_username_info').text(msg);
            complete = false;
        }
        if (!signup.usrNameChk) {
            msg = "Please enter a different username.";
            complete = false;
        } else {
            jQuery('#tab3_username_info').text('');
        }
        if (document.getElementById("password1").value == '') {
            msg = "Please enter a password.";
            jQuery('#passval').text(msg);
            complete = false;
        }
			if (document.getElementById("password1").value != document
					.getElementById("password2").value) {
            msg = "Passwords do not match.";
            jQuery('#passval').text(msg);
            complete = false;
        }
			if (!self.isTrial) {
				var cc_month = jQuery("#cc_month");
				var cc_year = jQuery("#cc_year");

				if (document.getElementById("address1").value == ''
						|| document.getElementById("city").value == ''
						|| document.getElementById("zip").value == ''
						|| document.getElementById("country").value == '') {
                msg = "Please enter a complete billing address";
                jQuery('#billval').text(msg);
                complete = false;    
            }
				
            if (document.getElementById("cc_holder").value == ''){
                msg = "Please enter the name on your credit card.";
                            jQuery('#ccnameval').text(msg);
                complete = false;    
            }
				
            if (document.getElementById("cc_num").value == ''){
                msg = "Please enter the number on your credit card.";
                            jQuery('#ccval').text(msg);
                complete = false;    
            }
				
				var cc_result = jQuery("#cc_num").validateCreditCard( /* {accept:['amex','visa','mastercard','discover','jcb']} */ );
				if (!cc_result.valid) {
                msg = "Please enter a valid credit card number.";
                            jQuery('#ccval').text(msg);
                complete = false;   
            }
				
				var cvv = jQuery('#ccv').val();
				if ( !cvv ) {
					msg = "Please enter the security code on the back of your credit card.";
					jQuery('#ccccvval').text(msg);
                complete = false;   
				} else {
					if ( cc_result.valid && (cvv.length != cc_result.card_type.cvv_length ) ) {
						msg = "Please enter the security code on the back of your credit card.";
						jQuery('#ccccvval').text(msg);
						complete = false;
					}
            }
				
				if ( ! self.checkCCDate() ) {
                msg = "Please enter a valid credit card expiration date.";
                            jQuery('#ccexpval').text(msg);
                complete = false;   
            }
        }

        if (complete) {
				if (self.isTrial) {
					self.processTrialJq();
				} else {
					self.processPayJq();
            }
        }
                        };

		this.processTrialJq = function() {
			if (self.processing) {
				return;
			}

			self.processing = true;

			jQuery.ajax('/wp-content/themes/indagare/app/lib/iajax.php', {
				method : "POST",
				data : {
					task : "newTrial.j",
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
					jQuery('#memberdate').html(d.startdate);
					jQuery('#membercost').html('');
					jQuery('#memberlength').html(d.length);
					jQuery('#membercardholder').html('');
					jQuery('#membercard').html('');
					jQuery('#membertransaction').html('');
					jQuery('#memberlevel').html(d.name);
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
                        });
				} else {
					jQuery('#signup-error-title').html('Processing Error');
					jQuery('#signup-error-message').html(d.errmsg);
                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
							src : '#lightbox-signup-error', // can be a HTML
							// string, jQuery
							// object, or CSS
							// selector
                                midClick: true
                            },
                        });
                    }
			}).fail(function(x, s, e) {
				jQuery('#signup-error-title').html('Processing Error');
				jQuery('#signup-error-message').html(d.errmsg);
				jQuery.magnificPopup.open({
					items : {
						type : 'inline',
						src : '#lightbox-signup-error', // can be a HTML string,
						// jQuery object, or CSS
						// selector
						midClick : true
					},
				});
			}).always(function() {
				self.processing = false;
			});
            };

		this.processPayJq = function() {
			if (self.processing) {
				return;
        }

			self.processing = true;
			var args = { 
					task: "payment.j",
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
					tgCode: jQuery("#tgCode").val()
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
					r_error: 'Error communicating with payment system'
                                };	
                        
			}).always(function() {
				var s = false, b = '#lightbox-signup-complete';

				if ( result.r_approved != 'APPROVED' ) {
					jQuery('#memberdate').html(result.startdate);
					jQuery('#membercardholder').html(args.cc_holder);
					jQuery('#membercard').html(args.cc_holder.substr(args.cc_holder.length - 4));
					jQuery('#membertransaction').html(result.r_code);
					jQuery('#membercardholder').html(args.cc_holder);
					jQuery('#memberlength').html(result.length);
					jQuery('#memberlevel').html(result.name);
                                    
					jQuery('#membercomplete').on('click', function() {
						window.location = "/welcome/";
                        });
					b = '#lightbox-signup-complete';
					s = false;
				} else {
					jQuery('<span class="errormsg"></span>').text('Error processing payment.').appendTo('#messages');
					jQuery('<span class="errormsg"></span>').text(result.r_error).appendTo('#messages');
					b = '#lightbox-signup-error';
					s = true;
                    }
                    	
                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
						src : b,
						midClick : true
                            },
					showCloseBtn : s
                        });
				
				self.processing = false;
			});
            };

		this.ShowBilling = function() {
			self.selfInit();
        
			self.mbSelect.find('option[value^=trial]').remove();
			self.mbySelect.find('option[value^=trial]').remove();
			self.mbSelect.removeAttr('disabled').trigger('render');
			self.mbySelect.removeAttr('disabled').trigger('render');
			jQuery('#billingBlock').show();
		};

		this.ShowTrialMessage = function(m,e) {
			var c = "validated";
			if (e) {
				c = "validate";
			}
			m = '<span class="' + c + '">' + m + '</span>';
			jQuery("#tg_codeval").html(m);
		};

		this.tgCodeLookup = function() {
			var c = jQuery.trim(jQuery('#tgCode').val());
			if (c == '') {
				document.getElementById("tg_codeval").innerHTML = '';
				self.ShowTrialMessage('', false);
				
				if(showTrial) {
					jQuery(".inputgroup").hide();
					jQuery(".inputgrouptitle").hide();
					jQuery(".trial").show();
				} else {
					jQuery(".inputgroup").show();
					jQuery(".inputgrouptitle").show();
					jQuery(".inputgroup.trial").hide();
        }	
        
				return;
            }
                   
			var msg = 'An error occurred validating the code.  Please try again in a moment.';
			var err = true;
			jQuery
					.ajax("/wp-content/themes/indagare/app/lib/iajax.php", {
						data : {
							task : "chkTrialKey.j",
							rc : c
                    } 
					})
					.done(
							function(result) {
								var mb_select = jQuery("#membership_level");
								var mby_select = jQuery("#membership_years");

								if (!result.valid) {
									if(showTrial) {
										jQuery(".inputgroup").hide();
										jQuery(".inputgrouptitle").hide();
										jQuery(".trial").show();
									} else {
										jQuery(".inputgroup").show();
										jQuery(".inputgrouptitle").show();
										jQuery(".inputgroup.trial").hide();
        }

									msg = 'This is not a valid code.';
									self.ShowTrialMessage(msg, err);
									return self.ShowBilling();
								}

								jQuery('#billingBlock').hide();
								msg = 'Code accepted.';
								err = false;
								self.isTrial = true;
								document.getElementById("refCode").value = document
										.getElementById("tgCode").value;

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

								if(showTrial) {
									jQuery(".inputgroup").show();
									jQuery(".inputgrouptitle").show();
									jQuery(".billing").hide();
								} else {
									jQuery(".inputgroup").show();
									jQuery(".inputgrouptitle").show();
									jQuery(".inputgroup.trial").hide();
        }
	
	
							}).always(function() {
						self.ShowTrialMessage(msg, err);
					});
		};

		this.checkUsername = function() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            //console.log(xmlhttp.responseText);
            if (xmlhttp.responseText == "true") {
                document.getElementById("tab3_username_info").innerHTML = "Username already exists.";
                signup.usrNameChk = false;
				} else {
                document.getElementById("tab3_username_info").innerHTML = "<span class=\"validated\">Username accepted.</span>";
                signup.usrNameChk = true;
            }
        };
			xmlhttp
					.open(
							"POST",
							"/wp-content/themes/indagare/app/lib/iajax.php?task=chkLogin",
							true);
			xmlhttp.setRequestHeader("Content-type",
					"application/x-www-form-urlencoded");
			xmlhttp.send("login="
					+ encodeURI(document.getElementById("username").value));
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

		this.checkCCDate = function(month, year) {
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
		signup.initFields();
		signup.buildMembershipDD();
		if (trialCode != 'false') {
			document.getElementById("tgCode").value = trialCode;
			signup.tgCodeLookup();
    }  
		if (!showTrial) {
			jQuery(".inputgroup").show();
			jQuery(".inputgrouptitle").show();
			jQuery(".inputgroup.trial").hide();
		} else {
			jQuery(".inputgroup").hide();
			jQuery(".inputgrouptitle").hide();
			jQuery(".trial").show();
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
		jQuery('#user_prefix').css("height", "25px");

	}
	if (window.addEventListener) {
		window.addEventListener('DOMContentLoaded', init, false);
	} else {
		window.attachEvent('onload', init);
	}
}());

