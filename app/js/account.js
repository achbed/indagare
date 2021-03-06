/*Polyfills!*/
if (!Array.isArray) {
  Array.isArray = function(arg) {
    return Object.prototype.toString.call(arg) === '[object Array]';
  };
}
jQuery.fn.isBlank = function() {
    var fields = jQuery(this).serializeArray();

    for (var i = 0; i < fields.length; i++) {
        if (fields[i].value && jQuery.trim(fields[i].value) != '') {
            return false;
        }
    }

    return true;
};

var tabber1,
	getting = {},
	progressDialog = null,
	dialog_instance = 0;

/* Event connections */

jQuery(document)
	.ready(function(){
		getAccount();
		tabber1 = new Yetii({
			id: 'account-tab-container',
			callback: function() {
				//jQuery.scrollTo(jQuery('body'),0);
			}
		});
	})
	.on('click','#profile-passport-new',function(e) {
		e.preventDefault();
		appendCardItem(newCardItem('passport',getBlank('PassportVisa')), '#profile-passports');
	})
	.on('click','#profile-freq-new',function(e) {
		e.preventDefault();
		appendCardItem(newCardItem('freq',getBlank('FrequentTravel')), '#profile-freq');
	})
	.on('click','.form-edit-link',function(e){
		e.preventDefault();
		var y=jQuery(e.target).closest('form').addClass('editing');
		updateDisabledState(y);
	})
	.on('click','[action="autorenew"]',function(e){
		e.preventDefault();
		postdata = [
            {name:'action',value:"wpsf-putobject"},
            {name:'objectType',value:"Account"},
            {name:'Id',value:SFData.Account.Id},
            {name:'Is_Renewal__c',value:true}
        ];
		jQuery.ajax("/wp-admin/admin-ajax.php", {
			method: "POST",
			data : postdata 
		}).done(function(result) {
			if(!result.success) {
				jQuery.alert({
					title:'Save failed',
					content:result.data[0]
				});
			} else {
				window.location.reload(true);
			}
		}).fail(function(){
			jQuery.alert({
				title:_x.savefailed,
				content:_x.commerror
			});
		});
	})
	.on('click','[action="upgrade"]',function(e){
		e.preventDefault();
		var p=jQuery('#account-membership-upgrade-select').find(':selected'),a=p.attr('amount'),n=numeral(p.attr('amount')).format('$0,000.00');
	
	    jQuery.confirm({
	        title: _x.areyousure,
	        content: _x.nowcharge+' '+n,
	        keyboardEnabled: true,
	        confirmKeys:[],
	        confirmButton: jQuery('#upgrade-button').html(),
	        confirmButtonClass: 'btn-danger',
	        cancelButton: _x.cancel,
	
	    	confirm: function () {
	    		upgradeAccount(e);
	    		progressDialog = jQuery.alert({
	    			title:_x.updating,
	    			content:_x.pleasewait,
	    			closeIcon: false,
	    			confirmButton:'',
	    			cancelButton:'',
	    			confirmButtonClass:'hidden',
	    			cancelButtonClass:'hidden',
	    		});
	    	}
		});
		return false;
	})
	.on('click','[action="save"]',function(e) {
		if(jQuery(e.target).attr('id') == 'upgrade-button') return;
		e.preventDefault();
		jQuery(e.target).prop({disabled:true}).closest('form').addClass('processing');
		var f = jQuery(e.target).closest('form'),
			type = f.attr('data-source-object'),
			postdata = getFormData(f),
			i,id_set=false,tr_set=(type!='PassportVisa');
		
		for(i in postdata) {
			if(i == 'Id') {
				id_set = !!(postdata[i]);
			}
		}
		
		postdata.push({name:'action',value:'wpsf-putobject'});
		postdata.push({name:'objectType',value:type});
		
		jQuery.ajax("/wp-admin/admin-ajax.php", {
			method: "POST",
			data : postdata 
		}).done(function(result) {
			if(!result.success) {
				jQuery.alert({
					title:_x.savefailed,
					content:result.data[0]
				});
			} else {
				var newId = result.data.Id;
				f.find('[name=Id]').val(newId);
				switch(type) {
					case 'Contact':
						if(!id_set) {
							SFData.Contacts.push(result.data);
							return;
						}
						
						for ( i in SFData.Contacts ) {
							if ( SFData.Contacts[i].Id == newId ) {
								SFData.Contacts[i] = result.data;
								return;
							}
						}
						break;
						
					case 'PassportVisa':
						for ( i in SFData.Contacts ) {
							if ( SFData.Contacts[i].Id != result.data.Contact__c )
								continue;
							
							if(!id_set) {
								SFData.Contacts[i].Passport_Visa__x.push(result.data);
								return;
							}
							
							for ( j in SFData.Contacts[i].Passport_Visa__x ) {
								if ( SFData.Contacts[i].Passport_Visa__x[j].Id == newId ) {
									SFData.Contacts[i].Passport_Visa__x[j] = result.data;
									return;
								}
							}
						}
						break;
						
					case 'FrequentTravel':
						for ( i in SFData.Contacts ) {
							if ( SFData.Contacts[i].Id != result.data.Contact__c )
								continue;
							
							if(!id_set) {
								SFData.Contacts[i].Frequent_Travel__x.push(result.data);
								return;
							}
							
							for ( j in SFData.Contacts[i].Frequent_Travel__x ) {
								if ( SFData.Contacts[i].Frequent_Travel__x[j].Id != newId )
									continue;
								
								SFData.Contacts[i].Frequent_Travel__x[j] = result.data;
								return;
							}
						}
						break;
				}
			}
		}).always(function(){
			var t=jQuery(e.target),y=t.closest('form');
			t.removeClass('processing').prop({disabled:false});
			y.removeClass('editing');
			updateDisabledState(y);
		});
	});

jQuery(document).on('click','.form-cancel-button',function(e) {
	e.preventDefault();
	jQuery(e.target).closest('form').removeClass('editing');
});

jQuery(document).on('click','.card-close-button',function(e) {
	e.preventDefault();
	var f = jQuery(e.target).closest('form');
	if(f.isBlank()) {
		f.parent().remove();
		return;
	}
	f.removeClass('editing');
});

jQuery(document).on('click','.card-delete-button',function(e) {
	e.preventDefault();
	var f = jQuery(e.target).closest('form'),
		type = f.attr('data-source-object'),
		id = f.find('input[name=Id]').first().val(),
		postdata = {action:"wpsf-delobject",Id:id,objectType:type};
	
		if((type == 'PassportVisa')||(type == 'FrequentTravel')) {
			postdata['Contact__c'] = f.find('[name=Contact__c]').first().val();
		}
    jQuery.confirm({
        title: _x.areyousure,
        content: _x.cannotundo,
        keyboardEnabled: true,
        confirmKeys:[],
        confirmButton: _x.yesdelete,
        confirmButtonClass: 'btn-danger',
        cancelButton: _x.cancel,
    	confirm: function () {
    		if ( id != '' ) {
    			jQuery(e.target).addClass('processing').prop({disabled:true});
    			// We have an Id.  We need to actually delete this.
    			jQuery.ajax("/wp-admin/admin-ajax.php", {
    				method: "POST",
					data : postdata 
    			}).done(function(result) {
    				if(!result.success) {
    					jQuery.alert({
    						title:_x.deletefailed,
    						content:result.data
    					});
    				} else {
            			f.parent().remove();
            			// @TODO: Remove from the data stream too.
    				}
    			}).always(function(){
    				jQuery(e.target).removeClass('processing').prop({disabled:false});
    			});
    		} else {
				f.parent().remove();
    		}
    	}
	});
});


jQuery(document).on('click','#new-contact-link',function(e){
	e.preventDefault();
	dialog_instance++;
	var diagid = 'new-contact-form-'+dialog_instance,
	 	c = jQuery('<div></div>').attr('id',diagid).html( jQuery('#new-contact-form').html() ).prop('outerHTML'),
	 	e = c.replace(/(validate-group=)"([^"]*)"/g,'$1"$2-'+dialog_instance+'"');
	
    jQuery.confirm({
        title: _x.addtravelcomp,
        content: e,
        keyboardEnabled: false,
        confirmButton: _x.create,
        confirmButtonClass: 'btn-info',
        cancelButton: _x.cancel,
    	confirm: function () {
    		if(shrValidate.validateForm('#'+diagid) !== true) return false;
    		createNewContact(this.$content);
    		progressDialog = jQuery.alert({
    			title:_x.createcontact,
    			content:_x.pleasewait,
    			closeIcon: false,
    			confirmButton:'',
    			cancelButton:'',
    			confirmButtonClass:'hidden',
    			cancelButtonClass:'hidden',
    		});
        }
    });
});

jQuery(document).on('click','#renew-link',function(e){
	e.preventDefault();
	//stupid yetii hack
	tabber1.show(2); 
	return false;
});


jQuery(document).on('change','#account-membership-upgrade-select',function(){
	fixUpgradeButtonText();
}); 

jQuery(document).on('change','#contactselect',function(){
	contactSelectionChange();
});

jQuery(document).on('change','select',function(e){
	var n=jQuery(e.target).attr('name');
	updateControlledSelect(n);
});

function appendCardItem(i,t) {
	var p = jQuery(t),
		y = p.find('.card-item').last();
	i.find('form').addClass('editing');
	if( !y.length ) {
		p.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
}

function upgradeAccount(e){
	var p=jQuery('#account-membership-upgrade-select'),t=p.val(),postdata = [];
	postdata.push({name:'action',value:'idj-renew'});
	if(t != SFData.Membership.Id) {
		postdata.push({name:'l',value:t});
	}
	
	jQuery(e.target).addClass('processing').prop({disabled:true});
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		method: "POST",
		data : postdata 
	}).done(function(result) {
		jQuery(e.target).removeClass('processing').prop({disabled:false});
		if(!result.success) {
			if(progressDialog) {
				progressDialog.close();
			}
			jQuery.alert({
				title:_x.upgradefailedtitle,
				content:_x.upgradefailed+' '+result.data.message
			});
		} else {
			location.reload(true);
		}
	}).fail(function(){
		jQuery(e.target).removeClass('processing').prop({disabled:false});
		if(progressDialog) {
			progressDialog.close();
		}
		jQuery.alert({
			title:_x.upgradefailedtitle,
			content:_x.upgradefailed
		});
	});
}

function updateControlledSelect(n) {
	if(!n) {
		jQuery('select[controlfield]').each(function(x,i){
			updateControlledSelect(jQuery(i).attr('controlfield'));
		})
		return;
	}

	jQuery('select[controlfield="'+n+'"]').each(function(x,i){
		i = jQuery(i);
		if(!i.siblings('.select-data').length) {
			jQuery('<div></div>').addClass('hidden select-data').html(i.html()).appendTo(i.parent());
		}
		
		var p = parseInt(jQuery('select[name="'+n+'"]').prop('selectedIndex'));
		i.html('');
		i.siblings('.select-data').first().children('option').each(function(x,c){
			c = jQuery(c);
			var f = c.attr('validfor');
			if(!f) {
				i.append(c.clone());
				return;
			}
			
			var jj=hexMapMatch(f,p);
			if(jj)
				i.append(c.clone());
		});
		if(i.children().length == 1) {
			i.html('').append(jQuery('<option></option>').attr('value',''));
		}
		var r = i.attr('startvalue');
		if(r && i.find('[value="'+r+'"]').length) {
			i.val(r);
		}
	});
}

function hexMapMatch(a,b) {
	var c=a.split(''),d,e=0;
	d=parseInt(c.pop(),16);
	e=1;
	b--;
	while(b>0) {
		e <<= 1;
		b--;
		if(e>8) {
			if(!c.length) 
				return 0;
			d=parseInt(c.pop(),16);
			e=1;
		}
	};
	return d&e;
}

function getFormData(f) {
	var r = [];
	jQuery(f).find('select,input,textarea').each(function(x,i){
		var j = jQuery(i), n = j.attr('name'), v = j.val(), t;
		if(n) {
			if(v.substring(0,1)=='*') {
				// Anything starting with * is ignored.
				return;
			}
			if ( j.attr('picker') == 'date' ) {
				v = j.datepicker('getDate');
				if(v)
					v = v.toISOString();
			} else if( j.attr('type') == 'checkbox' ) {
				// Checkbox!  
				if ( ! j.prop('checked') ) {
					return;
				}
				
				// Try to append the value
				for(t in r) {
					if(t == n) {
						r[t] += ';' + j.attr('check-value');
						return;
					}
				}
				v = j.attr('check-value');
			}
			r.push({name:n,value:v});
		}
	});
	return r;
}

function setProcessing(b) {
	var a = jQuery(b);
	if(!jQuery(b).is('form')) {
		a = a.closest('form');
	}
	if(!a.length) return;
	a.addClass('processing').find('[action]').prop({disabled:true});
}

function unsetProcessing(b) {
	var a = jQuery(b);
	if(!jQuery(b).is('form')) {
		a = a.closest('form');
	}
	if(!a.length) return;
	a.removeClass('processing').find('[action]').prop({disabled:false});
}


function getAccount() {
	if(SFData.initLoad) {
		if(!SFData.Account.IsPrimaryContact__x) {
			jQuery('.billingtab').remove();
		} else {
			jQuery('.billingtab').removeClass('hidden');
		}
		updateContacts();
		applyMembershipUpgradeOptions();
		SFData.initLoad = false;
		return;
	}
	if(getting.account) return;
	getting.account = true;
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		data : {
			action : "wpsf-getaccount",
		} 
	}).done(function(result) {
		if(!result.success) {
			alert(_x.accountloadfailed);
			return;
		}
		SFData.Account = result.data;
		SFData.Membership = SFData.Account.Membership__x;
		SFData.Contacts = SFData.Account.Contacts__x;
		getting.account = false;
		updateContacts();
		applyMembershipUpgradeOptions();
	});
}

function getBlank(i) {
	if(!SFData.def.hasOwnProperty(i)) {
		return {};
	}
	var r={},x;
	for(x in SFData.def[i]) {
		r[x] = SFData.def[i][x].defaultValue;
	}
	return r;
}

function updateContacts() {
	if ( ! SFData.Contacts ) {
		return;
	}
	jQuery('#contactselect').html('');
	var first = null;
	for( c in SFData.Contacts ) {
		if(!first) first = c;
		var i = SFData.Contacts[c];
		jQuery('<option></option>')
			.attr({value:i.Id})
			.html(i.Name)
			.appendTo('#contactselect');
	}
	jQuery('#contactselect').val( SFData.Contacts[first].Id ).trigger('change');
}

function contactSelectionChange() {
	if ( ! SFData.Contacts ) {
		return;
	}
	var id = jQuery('#contactselect').val();
	jQuery('[data-source-object=Contact]').attr('form-record-id', id);
	updateForms();
}

function contactData(id) {
	for(c in SFData.Contacts) {
		if ( id == SFData.Contacts[c].Id ) {
			return SFData.Contacts[c];
		}
	}
	return null;
}

function newCardItem(y,i) {
	var c=jQuery('<div></div>').addClass('card-item card-item-'+y),
		f=jQuery('<form></form>').addClass('clearfix'),
		p='carditem-'+getIdFromObject(i),
		z=[],x,o,l;
	switch(y) {
		case 'passport':
			o = "PassportVisa";
			z = [
			     'Id',
			     'Contact__c',
			     'RecordTypeId',
			     'Country__c',
			     'Legal_Name__c',
			     'Number__c',
			     'Expiry_Date__c'
		    ];
			break;
			
		case 'freq':
			o = "FrequentTravel";
			z = [
			     'Id',
			     'Contact__c',
			     'Carrier_Hotel_Operator__c',
			     'Frequent_Traveler_Program__c',
			     'Name',
			     'Frequent_Flyer_Number__c'
		    ];
			break;
	}
	f.attr('data-source-object',o);
	for(x in z) {
		if(i.hasOwnProperty(z[x])) {
			l = makeInput(SFData.def[o][z[x]],p,i[z[x]],false);
			l.addClass('field-carditem-'+z[x]);
			f.append(l);
		}
	}
	f.find('input[name="Contact__c"]').val( jQuery('#contactselect').val() );
	
	jQuery('<a></a>').attr('href','#').html(_x.del).addClass('card-delete-button card-button-large').prependTo(f);
	jQuery('<a></a>').attr('href','#').html(_x.save).attr('action','save').addClass('form-save-button card-save-button card-button-large').appendTo(f);
	jQuery('<a></a>').attr('href','#').html(_x.edit).addClass('form-edit-link card-edit-link').appendTo(f);
	f.appendTo(c);
	
	return c;
}

function getIdFromObject(i){
	var x,d;
	for(x in i) {
		if(x=='Id') {
			return (i[x]?i[x]:'noid');
		}
	}
}

function applyMembershipUpgradeOptions() {
	var t = jQuery('#account-membership-upgrade-select');
	if(!t || !SFData.MembershipList) {
		fixUpgradeButtonText();
		return;
	}
	
	var i,r=[],n=false,ma=parseFloat( SFData.Membership.Amount__c ),renew=isRenewal();
	if(isExpired()) ma = 0;
	for(i in SFData.MembershipList) {
		if ( SFData.MembershipList[i].Amount > ma ) {
			SFData.MembershipList[i].upgradeMode = 'upgrade';
			if(renew||ma==0) {
				SFData.MembershipList[i].upgradeMode = 'renew';
			}
			r.push(SFData.MembershipList[i]);
			if ( !n ) {
				n = SFData.MembershipList[i].Id;
			}
		} else if ( renew && ( SFData.MembershipList[i].Id == SFData.Membership.Id ) ) {
			SFData.MembershipList[i].upgradeMode = 'renew';
			r.push(SFData.MembershipList[i]);
			if ( SFData.MembershipList[i].Id == SFData.Membership.Id ) {
				n = SFData.Membership.Id;
			}
		}
	}
	
	if(!r.length) {
		t.remove();
		fixUpgradeButtonText();
		return;
	}
	
	t.html('');
	for(i in r) {
		var a = parseFloat(r[i].Amount);
		if ( r[i].upgradeMode == 'upgrade' ) {
			// This is an upgrade. Adjust the amount.
			a = a - ma;
			a = Math.max(a,0);
		}
		var o = jQuery('<option></option>')
		.val(r[i].Id)
		.attr({amount:a,upgradeMode:r[i].upgradeMode})
		.html(r[i].Name + ' - ' + numeral(a).format('$0,000.00'))
		.appendTo(t);
	}
	if(n) {
		t.val(n);
	}
	fixUpgradeButtonText();
}

function fixUpgradeButtonText() {
	var s=jQuery('#account-membership-upgrade-select'),b=jQuery('#upgrade-button');
	if(b.length) {
		if(!s.length) {
			b.remove();
			jQuery('#field-account-membership-upgrade-select label').html('No more upgrade options available');
		} else {
			var t=s.val(),x=_x.upgradenow,i,tm=false,cm;
			for(i in SFData.MembershipList) {
				if(SFData.MembershipList[i].Id == t) {
					tm=SFData.MembershipList[i];
					break;
				}
			}
			if(!!tm && ((SFData.Membership.Id == tm.Id) || (SFData.Membership.Amount__c >= tm.Amount))) {
				x = _x.renewnow;
			}
			b.html(x);
		}
	}
	
	var end = renewMode();
	if ( end === false ) {
		// Show only the "Renew" option (taken care of in the previous function)
	} else if ( end === true ) {
		// Show the "Enable Autorenew" button
		jQuery('#autorenew-button').show();
	} else {
		// We should have a date object instead of true/false.  Output it as the renewal date.
		jQuery('#account-bar-autorenew').html(end.toLocaleDateString());
	}
}

function createNewContact(f) {
	var postdata = {
		action: 'idj-newcontact',
		fn: f.find('[name="FirstName"]').val(),
		ln: f.find('[name="LastName"]').val(),
		phone: f.find('[name="Phone"]').val(),
		email: f.find('[name="Email"]').val(),
		username: f.find('[name="Username"]').val(),
		password: f.find('[name="Password"]').val()
	};
	
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		method: "POST",
		data : postdata
	}).done(function(result) {
		if(progressDialog) {
			progressDialog.close();
		}
		if(!result.success) {
			jQuery.alert({
				title:_x.createfail,
				content:result.data[0]
			});
			return false;
		}
		
		SFData.Contacts.push(result.data);
		updateContacts();
		jQuery('#contactselect').val( result.data.Id ).trigger('change');
	}).always(function(){
		if(progressDialog) {
			progressDialog.close();
		}
	});
}

function handleDisplayIf() {
	jQuery('[data-display-if]').each(function(x,f){
		var d = jQuery(f).attr('data-source-object');
		if(!d || !SFData.hasOwnProperty(d)) return;
		d = SFData[d];
		var i = jQuery(f).attr('data-display-if'),q="",i,v,h="",ops=["=>","<=",">=","=<","<>","!=","=!","=",">","<"],op;
		for ( var o in ops ) {
			if ( i.indexOf(ops[o]) > -1 ) {
				op = ops[o];
				break;
			}
		}
		q=i.split(op);
		i=q[0];
		v=q[1];
		if(d.hasOwnProperty(i)) {
			if(v == 'null') {
				if( op == "=") {
					if(!d[i]) {
						jQuery(f).show();
					} else {
						jQuery(f).hide();
					}
				} else {
					if(!!d[i]) {
						jQuery(f).show();
					} else {
						jQuery(f).hide();
					}
				}
				return;
			}

			var truth = null;

			if(v == 'true') {
				if(["=","<=","=>","=<",">="].indexOf(op) === -1) {
					if(d[i]) {
						truth = false;
					} else {
						truth = true;
					}
				} else {
					if(d[i]) {
						truth = true;
					} else {
						truth = false;
					}
				}
			} else if(v == 'false') {
				if(["=","<=","=>","=<",">="].indexOf(op) === -1) {
					if(d[i]) {
						truth = true;
					} else {
						truth = false;
					}
				} else {
					if(d[i]) {
						truth = false;
					} else {
						truth = true;
					}
				}
			} else {
				switch(op) {
					case "=":
						if(v == d[i]) {
							truth = true;
						} else {
							truth = false;
						}
						break;
						
					case "!=":
					case "=!":
					case "<>":
						if(v != d[i]) {
							truth = true;
						} else {
							truth = false;
						}
						break;
						
					case "=>":
					case ">=":
						if(d[i] >= v) {
							truth = true;
						} else {
							truth = false;
						}
						break;
						
					case "=<":
					case "<=":
						if(d[i] <= v) {
							truth = true;
						} else {
							truth = false;
						}
						break;
						
					case "<":
						if(d[i] < v) {
							truth = true;
						} else {
							truth = false;
						}
						break;
						
					case ">":
						if(d[i] > v) {
							truth = true;
						} else {
							truth = false;
						}
						break;
				}
			}
			if(truth) {
				jQuery(f).show();
			} else {
				jQuery(f).hide();
			}
		} else {
			jQuery(f).hide();
		}
	});
}

function newDate(a) {
	if(!a) {
		return new Date();
	}
	a = String(a);
	var d = new Date(a);
	if(a.indexOf('T')==-1) {
		// No time zone in the date.  Offset to current time zone.
		d.setMinutes(d.getMinutes() + d.getTimezoneOffset());
	}
	return d;
}

function handleDisplayField() {
	jQuery('[data-display-field]').each(function(x,f){
		var d = jQuery(f).attr('data-source-object');
		if(!d || !SFData.hasOwnProperty(d)) return;
		d = SFData[d];
		var i = jQuery(f).attr('data-display-field'), h='';
		if(d.hasOwnProperty(i)) {
			h = d[i];
			switch(i) {
				case 'Birthdate':
				case 'Anniversary_Date__c':
				case 'Membership_Start_Date__c':
				case 'Membership_End_Date__c':
				case 'Member_Since__c':
					if(h && h != '') {
						var t = newDate(h);
						h = t.toLocaleDateString();
					}
			}
		}
		jQuery(f).html(h);
	});
}

function updateDisabledState(d) {
	if(!d) { d = document; }
	jQuery(d).find('input,select,textarea').each(function(x,i){
		var j=jQuery(i), f=j.closest('form');
		if(!f.length){
			j.prop({disabled:false,readonly:false});
			return;			
		}
		
		if(f.hasClass('editing')) {
			j.prop({disabled:false,readonly:false});
			return;
		}
		
		j.prop({disabled:true,readonly:true});
	});
}

function updateForms() {
	handleDisplayIf();
	handleDisplayField();
	
	jQuery('[data-edit-field] .auto-created-field').remove();
	jQuery('[data-edit-field]').each(function(x,f){
		var i, r,
			p = SFData.Account.IsPrimaryContact__x,
			d = null,
			y = jQuery(f).attr('id'),
			z = jQuery(f).attr('data-edit-field'),
			a = jQuery(f).attr('data-source-object');
		if(!y||!y.length) return;
		switch(a) {
			case 'Contact':
				d = contactData(jQuery(f).attr('form-record-id'));
				p = p || d.IsCurrentUser__x;
				p = !p;
				
				jQuery('#profile-passports').empty();
				jQuery('#profile-freq').empty();
				
				if(!!d && d['Passport_Visa__x']) {
					for ( i in d['Passport_Visa__x'] ) {
						jQuery('#profile-passports').append(newCardItem('passport',d['Passport_Visa__x'][i]));
					}
				}
				
				if(!!d && d['Frequent_Travel__x']) {
					for ( i in d['Frequent_Travel__x'] ) {
						jQuery('#profile-freq').append(newCardItem('freq',d['Frequent_Travel__x'][i]));
					}
				}
				
				break;
				
			case 'Account':
				d = SFData.Account;
				p = false;
				break;
				
			case 'Membership':
				d = SFData.Membership;
				p = true;
				break;
		}
		z = new String(z).split(',');
		jQuery(f).find('.loading').remove();
		jQuery(f).parent().find('.hide-until-load').removeClass('hide-until-load');
		if(!d) return;
		var tgt = jQuery(f).find('.form-save-button');
		if(!tgt.length) p = true;
		for(i in z) {
			var k = jQuery.trim(z[i]);
			if(k.indexOf('|')!== false) {
				r = k.split('|');
				k = r.shift();
				r = arrayToProps(r);
			} else {
				r = false;
			}
			if(d.hasOwnProperty(k) && SFData.def[a].hasOwnProperty(k)) {
				jQuery(f).find('[field-instance="'+SFData.def[a][k].name+'"]').remove();
				var element = makeInput(SFData.def[a][k],y,d[k],p,r);
				if(tgt.length) {
					tgt.before(element);
				} else {
					jQuery(f).append(element);
				}
			}
		}
		
		jQuery(f).removeClass('needs-reload');
	});
	
	updateControlledSelect();
	updateDisabledState();
}

function arrayToProps(a) {
	var i,r={},v,k;
	for(i in a) {
		v=a[i].split('=',2);
		k=v.shift();
		r[k]=v[0];
	}
	return r;
}

/**
 * Determines what mode the various form buttons should be in based on the autorenew and pulldown selections.
 * 
 *  False means Autorenew is disabled OR we're past the renewal processing date, and we should show the "Renew" option
 *  True means Autorenew is disabled, and we should show the "Enable Autorenew" button
 *  A date object means Autorenew is enabled, and we should not show either "Renew" or "Enable Autorenew"
 */
function renewMode() {
	var end = newDate(SFData.Account.Membership_End_Date__c);
	end.setTime( end.getTime() - 8 * 86400000 );
	// Adjust math for Daylight Savings
	end.setTime( end.getTime() + 12 * 1000 * 60 * 60 ); 
	end.setHours(0);
	
	if ( SFData.Account.Is_Renewal__c ) {
		// Autorenew is enabled.
		if ( end > newDate() ) {
			return end;
		}
		return false;
	}
	
	if ( end > newDate() ) {
		// The threshold is in the future.  Allow Autorenew Enable.
		return true;
	}
	
	return false;
}

function isExpired() {
	if(!!SFData.Membership.Membership_Type__c) {
		if ( SFData.Membership.Membership_Type__c == 'Trial' ) {
			// A trial membership is always "expired"
			return true;
		}
	}
	
	var end = newDate(SFData.Account.Membership_End_Date__c);
	end.setTime( end.getTime() - 8 * 86400000 );
	// Adjust math for Daylight Savings
	end.setTime( end.getTime() + 12 * 1000 * 60 * 60 ); 
	end.setHours(0);
	
	if ( end > newDate() ) {
		// The threshold is in the future.
		return false;
	}
	
	return true;
}

function isRenewal() {
	if(!!SFData.Membership.Membership_Type__c) {
		if ( SFData.Membership.Membership_Type__c == 'Trial' ) {
			// Never allow a renewal of a trial membership
			return false;
		}
	}
	
	var end = newDate(SFData.Account.Membership_End_Date__c);
	end.setTime( end.getTime() - 8 * 86400000 );
	// Adjust math for Daylight Savings
	end.setTime( end.getTime() + 12 * 1000 * 60 * 60 ); 
	end.setHours(0);
	
	if ( SFData.Account.Is_Renewal__c ) {
		// Autorenew is enabled.
		if ( end > newDate() ) {
			// And the renewal processing date is in the future.
			return false;
		}
		
		// We've passed the renewal date.  Allow a renewal.
		return true;
	}
	
	if ( end > newDate() ) {
		// The threshold is in the future.
		return false;
	}
	
	return true;
}

function isUpgrade() {
	return ( ! isRenewal() );
}


/**
 * Returns a jQuery.datepicker format string for the current browser locale
 * @returns string The format string.
 */
function localeDateFormat() {
	return (new Date(2003,0,2)).toLocaleDateString().replace('2003','yy').replace('03','y').replace('01','mm').replace('1','m').replace('02','dd').replace('2','d');
}

/**
 * Returns a rendered form element for a given field object and prefix
 * 
 * @param a object. The definition object for the field to render
 * @param p string. ID prefix for the rendered field input box
 * @param l mixed. The value for the field
 * @param t boolean.  Whether or not the field should be text only and not changable.
 * @param r object.  An attribute set to pass to the wrapper object.
 * 
 * @returns string. The form element.
 */
function makeInput(a,p,l,t,r) {
	var i=1,y,s,o,e,d=p+'-'+a['name'],f;

	while(jQuery('#'+d).length>0) {
		i++;
		d = p + '-' + a['name']+'-'+i;
	}
	i = 1;
	f = 'field-' + d;
	while(jQuery('#'+f).length>0) {
		i++;
		f = 'field-' + d + '-' + i;
	}

	var neverShow = ['Id','Contact__c','AccountId'];
	
	if ( jQuery.inArray( a['name'], neverShow ) >= 0 ) {
		// Never show ID fields
		a['type'] = 'hidden';
	}
	
	o = jQuery('<div></div>').addClass('input-field field clearfix auto-created-field').attr({'id':f,'field-instance':a['name']});
	if(!!r) {
		if(r.hasOwnProperty('type')) {
			a['type'] = r['type'];
			delete r['type'];
		}
	}
	
	e = a['type'];
	if(t) {
		switch(e) {
			case 'select':
			case 'multipicklist':
			case 'picklist':
			case 'country':
			case 'state':
				e = 'text';
		}
	}
	
	v = l;
	if( !v || ( v == 'null' ) ) v = a['defaultValue'];
	if( v == 'null' ) v = '';
	
	switch(e) {
		case 'boolean':
			s = jQuery('<input></input>')
				.prop({
					'disabled':( ! a['updateable'] ),
					'checked':!!v
				})
				.attr({
					'id':d,
					'name':a['name'],
					'maxlength':a['length'],
					'type':'checkbox'
			});
			if(t) {
				s.prop('disabled',true);
			}
			break;
			
		case 'textarea':
			s = jQuery('<textarea></textarea>')
			.prop('disabled',( ! a['updateable'] ) )
			.attr({
				'id':d,
				'name':a['name'],
				'maxlength':a['length']
			});
			
			if(v && v !='null')
				jQuery(s).val(v);
			
			if(t) {
				s.prop({'disabled':true,'readonly':true});
			}
			
			break;
			
		case 'multipicklist':
			s = jQuery('<div></div>')
				.addClass('checkboxes clearfix')
				.attr({'id':d});
			var values = new String(v).split(';');
			
			for ( i in a['picklistValues'] ) {
				var w = jQuery('<div></div>').addClass('checkbox-item');
				y = a['picklistValues'][i];
				
				var chkbox = jQuery('<input></input>').attr({
					'name':a['name'],
					'type':'checkbox',
					'check-value':y['value']
				});
				
				if(jQuery.inArray(y['value'],values) != -1) {
					chkbox.prop({'checked':true});
				}
				
				var label = jQuery('<div></div>')
					.addClass('checkbox-label')
					.html(y['label']);
				
				jQuery('<label></label>')
					.append(chkbox)
					.append(label)
					.addClass('checkbox clearfix')
					.attr({
						'value':y['value'],
						'validfor':(y.hasOwnProperty('validFor')?y['validFor']:'')
					})
					.appendTo(w);
				w.appendTo(s);
			}
			break;
		
		case 'select':
		case 'picklist':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':a['name'],
					'startvalue':v
				})
				.prop('disabled',( ! a['updateable'] ) );
			
			if(a['controllingField']) {
				s.attr('controlfield',a['controllingField']);
			}
			
			if ( a['nillable'] ) {
				jQuery('<option></option>').html(_x.chooseoption).attr('value',' ').appendTo(s);
				if(!v || v == 'null' || v == '') v = ' ';
			}
			
			for ( i in a['picklistValues'] ) {
				y = a['picklistValues'][i];
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':(y.hasOwnProperty('validFor')?y['validFor']:'')
					})
					.appendTo(s);
			}
			
			if(v && v !='null')
				jQuery(s).val(v);

			break;

		case 'country':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':a['name'],
					'startvalue':v
				})
				.prop('disabled',( ! a['updateable'] ) );
			
			if ( a['nillable'] ) {
				jQuery('<option></option>').html(_x.chooseoption).attr('value',' ').appendTo(s);
				if(!v || v == 'null' || v == '') v = ' ';
			}
			
			for ( i in SFData.def.Countries ) {
				y = SFData.def.Countries[i];
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':(y.hasOwnProperty('validFor')?y['validFor']:'')
					})
					.appendTo(s);
			}
			
			if(v && v !='null')
				jQuery(s).val(v);
	
			break;


		case 'state':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':a['name'],
					'controlfield':a['controllingField'],
					'startvalue':v
				})
				.prop('disabled',( ! a['updateable'] ) );
			
			if ( a['nillable'] ) {
				jQuery('<option></option>').html(_x.chooseoption).attr('value',' ').appendTo(s);
				if(!v || v == 'null' || v == '') v = ' ';
			}
			
			for ( i in SFData.def.States ) {
				y = SFData.def.States[i];
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':(y.hasOwnProperty('validFor')?y['validFor']:'')
					})
					.appendTo(s);
			}
			
			if(v && v !='null')
				jQuery(s).val(v);
	
			break;

		case 'month':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':a['name'],
					'startvalue':v
				})
				.prop('disabled',( ! a['updateable'] ) );
			
			if(!v || v == 'null' || v == '' ) {
				jQuery('<option></option>').html(_x.month_elip).attr('value',' ').appendTo(s);
				v = ' ';
			}
			if(v.substring(0,1) == '*' ) { 
				jQuery('<option></option>').html(_x.onfile).attr('value','**').appendTo(s);
				v = '**';
			}
			
			var options = {'January':'01','February':'02','March':'03','April':'04','May':'05','June':'06',
					'July':'07','August':'08','September':'09','October':'10','November':'11','December':'12'};
			for ( i in options ) {
				jQuery('<option></option>')
					.html(i)
					.attr({'value':options[i]})
					.appendTo(s);
			}
			
			if(v && v !='null')
				jQuery(s).val(v);
	
			break;

		case 'cc_year':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':a['name'],
					'startvalue':v
				})
				.prop('disabled',( ! a['updateable'] ) );
			
			if(!v || v == 'null' || v == '' ) {
				jQuery('<option></option>').html(_x.year_elip).attr('value',' ').appendTo(s);
				v = ' ';
			}
			if(v.substring(0,1) == '*' ) { 
				jQuery('<option></option>').html(_x.onfile).attr('value','**').appendTo(s);
				v = '**';
			}
			
			var dte = newDate();
			y = dte.getFullYear();
			maxy = y + 10;
			for (;y<maxy;y++) {
				jQuery('<option></option>')
					.html(y.toString())
					.attr({'value':y})
					.appendTo(s);
			}
			
			if(v && v !='null')
				jQuery(s).val(v);
	
			break;

		case 'date':
			s = jQuery('<input></input>')
				.prop('disabled',( ! a['updateable'] ) )
				.attr({
					'id':d,
					'name':a['name'],
					'type':(a['type']=='hidden'?'hidden':'text'),
					'picker':'date'
			});
			
			v = new String(v);
			if(v && v !='null' && v != '') {
				v = newDate(v);
				jQuery(s).val(v.toLocaleDateString());
			}

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			} else {
				var args = {
						dateFormat:localeDateFormat(),
						changeYear:true,
						changeMonth:true
				};
				if(a['name'] == 'Birthdate') {
					var year = newDate().getFullYear();
					var minyear = Math.max(1900,year - 125);
					args.yearRange = ''+minyear+':'+year;
				}
				if( v instanceof Date ) {
					args.defaultDate = v;
				}
				jQuery(s).datepicker(args);
			}

			break;

		case 'tel':
			s = jQuery('<input></input>')
				.prop('disabled',( ! a['updateable'] ) )
				.attr({
					'id':d,
					'name':a['name'],
					'maxlength':a['length'],
					'type':'tel',
					'validate-type':'phone'
			});
		
			if(v && v !='null')
				jQuery(s).val(v);

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			}

			break;

		default:
			s = jQuery('<input></input>')
				.prop('disabled',( ! a['updateable'] ) )
				.attr({
					'id':d,
					'name':a['name'],
					'maxlength':a['length'],
					'type':(a['type']=='hidden'?'hidden':'text')
			});
		
			if(v && v !='null')
				jQuery(s).val(v);

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			}

			break;
	}
	
	if ( a['nillable'] ) {
		jQuery(s).attr('validate-empty',1);
	} else {
		jQuery(s).attr('validate-empty',0);
	}
	if(!!r) {
		jQuery(s).attr(r);
	}
	if( jQuery(s).attr('validate-type') !== undefined ) {
		jQuery('<span class="errmsg"></span>').appendTo(s);
	}
	jQuery(s).appendTo(o);
	if( a['type'] != 'hidden' ) {
		jQuery('<label></label>').html(a['label']).attr({
			'for':d
		}).appendTo(o);
	} else {
		o.addClass('hidden');
	}
	return o;
}