var shrValidate;
if (!shrValidate) {
	function shrValidateObj() {
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
			var t = jQuery(f).attr('validate-type'),
				h = false,
				r = false;
				
			if ( jQuery.inArray( t, ['ajax','ignore'] ) ) return true;
			
			switch (t) {
				case 'password':
					r = self.validatePassword();
					
				case 'cc_num':
				case 'ccv':
					return self.validateCC();
					
				case 'cc_month':
				case 'cc_year':
				case 'cc_exp':
					return self.validateCCExp();
					
				case 'checked':
					var r = jQuery(f).prop("checked");
					self.fieldValidated(f,r);
					return r;
					
				case 'unchecked':
					return self.validateTermAcceptance();
					
				case 'exists':
					return ( jQuery(f).val() != '' );
					
				case 'exists-if-checked':
					var rf = jQuery(f).attr('validate-parent');
					var r = jQuery(f).prop("checked");
					return true;
			}
			return self.validateField('#' + f);
		};
