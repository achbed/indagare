var shrValidate;
if (!shrValidate) {
	function shrValidateObj() {
		var self = this;
		
		this.ccYearAllowTwoDigit = false;
		
		this.init = function() {
			jQuery(document)
				.on('change','[validate-type]',function(e){
					self.validateField(e.target);
				});
		}

		this.formatPhone = function(f) {
			var n = jQuery(f).val(),r = /[^0-9]/gi,c=n.replace(r,'');
			 if(c.length != 10){
				 // We don't have 10 digits to work with, so just ignore the input value 
				 return;
			 }  
			 n = '('+c.substring(0,3)+') '+c.substring(3,6)+'-'+c.substring(6,10);
			 jQuery(f).val(n);
		}
		
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
			if ( r === true ) {
				c.addClass('validate-ok');
			} else if ( r === false ) {
				c.addClass('validate-error');
			} else {
				c.addClass('validate-unchanged');
			}
		};

		this.validateField = function(f) {
			var t = jQuery(f).attr('validate-type'),
				e = false,
				h = false,
				r = false;
			if ( jQuery(f).attr('validate-empty') !== undefined ) {
				e = (!!jQuery(f).attr('validate-empty'));
			}
				
			if ( jQuery.inArray( t, ['ajax','ignore'] ) >= 0 ) {
				return true;
			}
			
			switch (t) {
				case 'password':
				case 'password-verify':
					return self.validatePassword(f);
					
				case 'cc_type':
					// Only do this so we can have it in the CC group as an output.
					return true;
					
				case 'cc_num':
				case 'cvv':
					return self.validateCC(f);
					
				case 'cc_month':
				case 'cc_year':
				case 'cc_exp':
					return self.validateCCExp(f);
					
				case 'checked':
					r = !!jQuery(f).prop("checked");
					break;
					
				case 'unchecked':
					r = !jQuery(f).prop("checked");
					break;
					
				case 'phone':
					self.formatPhone(f);
					r = ( ( jQuery(f).val() == '' ) ? e : true );
					break;
					
				case 'exists':
					r = ( ( jQuery(f).val() == '' ) ? e : true );
					break;
					
				case 'email':
					self.fieldValidating(f);
					if ( jQuery(f).val() == '' ) {
						if ( e ) {
							self.fieldValidated( f, null );
							return true;
						}
						jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(1) );
						self.fieldValidated( f, false );
						return false;
					} else if ( ! self.checkEmailString( jQuery(f).val() ) ) {
						jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(2) );
						self.fieldValidated( f, false );
						return false;
					}
					
					self.fieldValidated( f, true );
					return true;
					
				case 'wp-unique-email':
					r = 0;
					self.fieldValidating(f);
					if ( jQuery(f).val() == '' ) {
						r = 1;
					} else if ( ! self.checkEmailString( jQuery(f).val() ) ) {
						r = 2;
					}
					if(r) {
						jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(r) );
						self.fieldValidated( f, false );
						return false;
					}
					self.checkEmail(f);
					return null;
					
				case 'wp-unique-username':
					self.fieldValidating(f);
					if ( jQuery(f).val() == '' ) {
						jQuery(f).closest('.field').find('.errmsg').html( self.getUsernameMessage(1) );
						self.fieldValidated( f, false );
						return false;
					}
					self.checkUsername(f);
					return null;
				
				default:
					r = true;
					break;
					
			}
			
			self.fieldValidated(f,r);
			return r;
		};
		
		/**
		 * Individual validation routines go here
		 */
		
		this.validatePassword = function(f) {
			var t = jQuery(f),
				g = t.attr('validate-group'),
				pw = jQuery('input[validate-type="password"][validate-group="'+g+'"]'),
				pv = jQuery('input[validate-type="password-verify"][validate-group="'+g+'"]'),
				settings = {
					minLength : 8,
					mixedCase : false,
					numbers : true,
					specialChars : false
				};
			if ( ! pw.length || ! pv.length ) {
				return null;
			}
			var p = pw.val(),
				v = pv.val(),
				r = true;
			
			t = t.closest('.field');
			t.removeClass('faildetail');
			if(settings.minLength > 0)
				t.find('.passlen-num').html(settings.minLength);

			self.fieldClearValidate(pw);
			self.fieldClearValidate(pv);

			self.fieldValidating(pw);

			if ( settings.minLength <= 0 ) {
				t.addClass('passlen-hide');
			} else if ( p.length < settings.minLength ) {
				r = false;
				t.addClass('passlen-fail faildetail');
			} else {
				t.removeClass('passlen-fail');
			}

			if ( ! settings.mixedCase ) {
				t.addClass('passcase-hide');
			} else if ( ! p.match( /.*([a-z].*[A-Z]|[A-Z].*[a-z]).*/ ) ) {
				r = false;
				t.addClass('passcase-fail faildetail');
			} else {
				t.removeClass('passcase-fail');
			}

			if ( ! settings.numbers ) {
				t.addClass('passnum-hide');
			} else if ( ! p.match(/[0-9]/)) {
				r = false;
				t.addClass('passnum-fail faildetail');
			} else {
				t.removeClass('passnum-fail');
			}

			if ( ! settings.specialChars ) {
				t.addClass('passchar-hide');
			} else if ( ! p.match( /[^a-z0-9]/i ) ) {
				r = false;
				t.addClass('passchar-fail faildetail');
			} else {
				t.removeClass('passchar-fail');
			}

			self.fieldValidated(pw, r);

			self.fieldValidating(pv);

			if ((p != v) || (p == '')) {
				self.fieldValidated(pv, false);
				r = false;
			} else {
				self.fieldValidated(pv, true);
			}

			return r;
		};


		this.validateCCExp = function(f) {
			var t = jQuery(f),
				g = t.attr('validate-group'),
				ccm = jQuery('[validate-type="cc_month"][validate-group="'+g+'"]'),
				ccy = jQuery('[validate-type="cc_year"][validate-group="'+g+'"]');
			
			if ( ! ccm.length || ! ccy.length ) {
				return null;
			}
			
			self.fieldValidating(ccm);
			self.fieldValidating(ccy);
			
			ry = true;
			rm = true;
			
			if ( ( ccm.val().substring(0,1) == '*' ) && ( ccy.val().substring(0,1) == '*' ) ) {
				// We have a value that's obfuscated on both fields.  Let it go.
				self.fieldValidated(ccm, null);
				self.fieldValidated(ccy, null);
				return true;
			} else {
				if ( ccm.val().substring(0,1) == '*' ) {
					// We have an obfuscated value on the month field.
					// We can validate the year, and mark the month as bad.
					rm = false;
				} else if ( ccy.val().substring(0,1) == '*' ) {
					// We have an obfuscated value on the year field.  We
					// have no reference point for checking the month field, so leave
					// everything as skipped but return false.
					self.fieldValidated(ccy, null);
					self.fieldValidated(ccm, null);
					return false;
				}
				
				var d = new Date(), 
					cm = Number(ccm.val()), 
					cy = Number(ccy.val()), 
					m = Number(d.getMonth()), 
					y = Number(d.getFullYear().toString());
				if ( ( ccy.val().length == 2 ) && self.ccYearAllowTwoDigit ) {
					y = Number(d.getFullYear().toString().substr(2, 2));
				}
				if(!cm) { cm=0; }
				
				if ( cy < y ) {
					ry = false;
					rm = false;
				} else if ( ( cm == 0 ) || ( ( cy == y ) && ( cm <= m ) ) ) {
					ry = ry & true;
					rm = false;
				} else {
					ry = ry & true;
					rm = ry & true;
				}
			}

			self.fieldValidated(ccm, rm);
			self.fieldValidated(ccy, ry);
			
			return (rm&&ry);
		};

		this.validateCC = function(f) {
			var t = jQuery(f),
				r = false,
				y = t.attr('validate-type'),
				g = t.attr('validate-group'),
				ccn = jQuery('input[validate-type="cc_num"][validate-group="'+g+'"]'),
				cct = jQuery('[validate-type="cc_type"][validate-group="'+g+'"]'),
				ccv = jQuery('input[validate-type="cc_cvv"][validate-group="'+g+'"]');

			self.fieldValidating(ccn);
			self.fieldValidating(ccv);

			if ( ( ccn.val().substring(0,1) == '*' ) && ( ccv.val().substring(0,1) == '*' ) ) {
				// We have a value that's obfuscated on both fields.  Let it go.
				r = true;
			} else {
				var cc_result = ccn.validateCreditCard( {accept:['amex','visa','visa_electron','mastercard','maestro','discover','jcb'] } );
				if (cc_result.valid) {
					r = true;
				}
			}
			
			self.fieldValidated(ccn, r);
			cct.val(cc_result.card_type.type);

			var cvv = ccv.val();
			if (!cvv) {
				self.fieldValidated(ccv, false);
				return false;
			}

			if (cc_result.valid
					&& (cvv.length != cc_result.card_type.cvv_length)) {
				self.fieldValidated(ccv, false);
				return false;
			}

			self.fieldValidated(ccv, true);
			return true;
		};

		this.getEmailMessage = function(a) {
			switch(a) {
				case 1:
					return "Please enter an email.";
				case 2:
					return "Please enter a valid email address.";
				case 3:
					return "Error validating email.  Try again in a moment.";
				case 4:
					return "That email is already associated with an account. Please try again, or <a href=\"/wp-login.php\">log in</a>.";
			}
			return "";
		};
		
		this.getUsernameMessage = function(a) {
			switch(a) {
				case 1:
					return "Please enter a username.";
				case 3:
					return "Error validating username.  Try again in a moment.";
				case 4:
					return "That username is already associated with an account. Please try again, or <a href=\"/wp-login.php\">log in</a>.";
			}
			return "";
		};
		
		this.checkEmailString = function(a) {
			var i = new String(a);
			return !!a.match(/^("[^"]+"|[-a-z0-9+_'][-a-z0-9+\._']*[-a-z0-9+_']|[-a-z0-9+_']+)@([a-z0-9][-a-z0-9]*[a-z0-9]\.)+[a-z0-9][-a-z0-9]*[a-z0-9]$/i);
		};
		
		this.checkUsername = function(f) {
			var a = {
					method : 'POST',
					data : {
						action : 'idj-login',
						login : jQuery(f).val()
					}
				};
			jQuery.ajax('/wp-admin/admin-ajax.php', a)
				.done(function(d, s, x) {
					r = false;
					if(d.exists) {
						jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(4) );
					} else {
						r = true;
					}
					self.fieldValidated( f, r );
				})
				.fail(function(x, s, e) {
					jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(3) );
					self.fieldValidated( f, false );
				});
		};
		
		this.checkEmail = function(f) {
			var a = {
					method : 'POST',
					data : {
						action : 'idj-email',
						email : jQuery(f).val()
					}
				};
			jQuery.ajax('/wp-admin/admin-ajax.php', a)
				.done(function(d, s, x) {
					r = false;
					if(d.exists) {
						jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(4) );
					} else {
						r = true;
					}
					self.fieldValidated( f, r );
				})
				.fail(function(x, s, e) {
					jQuery(f).closest('.field').find('.errmsg').html( self.getEmailMessage(3) );
					self.fieldValidated( f, false );
				});
		};
		
		this.validateForm = function(f) {
			var r = self.formOk(f);
			if ( r != -2 ) {
				return r;
			}
			jQuery(f).find('[validate-type]').each(function(i,e){
				self.validateField(e);
			});
			return self.formOk(f);
		};

		this.formOk = function(f) {
			var t = jQuery(f).find('[validate-type]').length,
				p = jQuery(f).find('.validating').length,
				v = jQuery(f).find('.validate-ok').length,
				u = jQuery(f).find('.validate-unchanged').length,
				c = jQuery(f).find('.validated').length;
			if ( t == 0 ) {
				// We have nothing to validate.  Return ok.
				return true;
			}
			if ( ( v + u ) == t ) {
				// We have the same number of validations as OKs or unchanged.  All clear.
				return true;
			}
			if ( c == t ) {
				// All fields are validated but not all are OK or skipped.
				return false;
			}
			if ( p > 0 ) {
				// We have validations in progress.  Return that.
				return -1;
			}
			
			// Something was never checked.
			return -2;
		};
	}
	
	shrValidate = new shrValidateObj();
	shrValidate.init();
}
