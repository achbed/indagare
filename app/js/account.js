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
var tabber1;


var getting = {};

jQuery(document).on('click','#profile-passport-new',function(e) {
	e.preventDefault();
	var i = newCardItem('passport',getBlank('PassportVisa'));
	i.find('form').addClass('editing');
	var t = jQuery('#profile-passports');
	var y = t.find('.card-item-passport').last();
	if( !y.length ) {
		t.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
});

jQuery(document).on('click','#profile-freq-new',function(e) {
	e.preventDefault();
	var i = newCardItem('freq',getBlank('FrequentTravel'));
	i.find('form').addClass('editing');
	var t = jQuery('#profile-freq');
	var y = t.find('.card-item-freq').last();
	if( !y.length ) {
		t.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
});

jQuery(document).on('click','.form-edit-link',function(e){
	e.preventDefault();
	var y=jQuery(e.target).closest('form');
	y.addClass('editing');
	updateDisabledState(y);
});

jQuery(document).on('click','.form-save-button',function(e) {
	if(jQuery(e.target).attr('id') == 'upgrade-button') return;
	e.preventDefault();
	jQuery(e.target).addClass('processing').prop({disabled:true});
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
				title:'Save failed',
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
        title: 'Are you sure?',
        content: 'This cannot be un-done.',
        keyboardEnabled: true,
        confirmKeys:[],
        confirmButton: 'Yes, Delete it',
        confirmButtonClass: 'btn-danger',
        cancelButton: 'Cancel',
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
    						title:'Delete failed',
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

var progressDialog = null;

jQuery(document).on('click','#new-contact-link',function(e){
	e.preventDefault();
    jQuery.confirm({
        title: 'Create New Contact',
        content: jQuery('#new-contact-form').html(),
        keyboardEnabled: false,
        confirmButton: 'Create',
        confirmButtonClass: 'btn-info',
        cancelButton: 'Cancel',
    	confirm: function () {
    		createNewContact(this.$content);
    		progressDialog = jQuery.alert({
    			title:'Creating contact',
    			content:'Please wait...',
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

jQuery(document).on('click','#upgrade-button',function(e){
	e.preventDefault();
	var p=jQuery('#account-membership-upgrade-select').find(':selected'),a=p.attr('amount'),n=numeral(p.attr('amount')).format('$0,000.00');

    jQuery.confirm({
        title: 'Are you sure?',
        content: 'We will now charge your credit card on file for '+n,
        keyboardEnabled: true,
        confirmKeys:[],
        confirmButton: jQuery('#upgrade-button').html(),
        confirmButtonClass: 'btn-danger',
        cancelButton: 'Cancel',

    	confirm: function () {
    		upgradeAccount(e);
    		progressDialog = jQuery.alert({
    			title:'Updating account',
    			content:'Please wait...',
    			closeIcon: false,
    			confirmButton:'',
    			cancelButton:'',
    			confirmButtonClass:'hidden',
    			cancelButtonClass:'hidden',
    		});
    	}
	});
	return false;
});

function upgradeAccount(e){
	var p=jQuery('#account-membership-upgrade-select'),t=p.val(),postdata = [];
	postdata.push({name:'action',value:'idj-renew'});
	postdata.push({name:'l',value:t});
	
	jQuery(e.target).addClass('processing').prop({disabled:true});
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		method: "POST",
		data : postdata 
	}).done(function(result) {
		jQuery(e.target).removeClass('processing').prop({disabled:false});
		if(progressDialog) {
			progressDialog.close();
		}
		if(!result.success) {
			jQuery.alert({
				title:'Account Update Failed',
				content:'Failed to update the account.  '+result.data.message
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
			title:'Account Update Failed',
			content:'Failed to update the account.'
		});
	});
}

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

jQuery(document).ready(function(){
	getAccount();
});

function getFormData(f) {
	var r = [];
	jQuery(f).find('select,input,textarea').each(function(x,i){
		var j = jQuery(i), n = j.attr('name'), v = j.val(), t;
		if(n) {
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
			alert('Error loading account data!');
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
	
	jQuery('<a></a>').attr('href','#').html('Delete').addClass('card-delete-button card-button-large').prependTo(f);
	jQuery('<a></a>').attr('href','#').html('Save').addClass('form-save-button card-save-button card-button-large').appendTo(f);
	jQuery('<a></a>').attr('href','#').html('Edit').addClass('form-edit-link card-edit-link').appendTo(f);
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
	var c=SFData.Membership.Amount__c,i,r=[];
	for(i in SFData.MembershipList) {
		if( (SFData.MembershipList[i].Amount > SFData.Membership.Amount__c) || (SFData.MembershipList[i].Id == SFData.Membership.Id) ) {
			r.push(SFData.MembershipList[i]);
		}
	}
	
	var t = jQuery('#account-membership-upgrade-select');
	t.html('');
	for(i in r) {
		var o = jQuery('<option></option>')
		.val(r[i].Id)
		.attr({
			amount:r[i].Amount
		})
		.html(r[i].Name + ' - ' + numeral(r[i].Amount).format('$0,000.00'))
		.appendTo(t);
	}
	t.val(SFData.Membership.Id);
	fixUpgradeButtonText();
}

function fixUpgradeButtonText() {
	var t = jQuery('#account-membership-upgrade-select').val(),x="Upgrade Now";
	if(t == SFData.Membership.Id) {
		x = "Renew Now";
	}
	jQuery('#upgrade-button').html(x);
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
				title:'Creation failed',
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
		var i = jQuery(f).attr('data-display-if'), q=i.split('='), i=q[0], v=q[1], h='';
		if(d.hasOwnProperty(i)) {
			if(v == 'null') {
				if(!d[i]) {
					jQuery(f).show();
				} else {
					jQuery(f).hide();
				}
				return;
			}
			
			if(v == '!null') {
				if(!!d[i]) {
					jQuery(f).show();
				} else {
					jQuery(f).hide();
				}
				return;
			}
			
			if(v == 'true') v=true;
			if(v == 'false') v=false;
			if(v == d[i]) {
				jQuery(f).show();
			} else {
				jQuery(f).hide();
			}
		} else {
			jQuery(f).hide();
		}
	});
}

function handleDisplayField() {
	jQuery('[data-display-field]').each(function(x,f){
		var d = jQuery(f).attr('data-source-object');
		if(!d || !SFData.hasOwnProperty(d)) return;
		d = SFData[d];
		var i = jQuery(f).attr('data-display-field'), h='';
		if(d.hasOwnProperty(i)) {
			h = d[i];
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
		var i, 
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
			if(d.hasOwnProperty(k) && SFData.def[a].hasOwnProperty(k)) {
				jQuery(f).find('[field-instance="'+SFData.def[a][k].name+'"]').remove();
				var element = makeInput(SFData.def[a][k],y,d[k],p);
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

/**
 * Returns a rendered form element for a given field object and prefix
 * @param a object. The definition object for the field to render
 * @param p string. ID prefix for the rendered field input box
 * @param l mixed. The value for the field
 * @param t boolean.  Whether or not the field should be text only and not changable.
 * @returns string. The form element.
 */
function makeInput(a,p,l,t) {
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
					.addClass('checkbox')
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
				jQuery('<option></option>').html('Choose an option...').attr('value',' ').appendTo(s);
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
				jQuery('<option></option>').html('Choose an option...').attr('value',' ').appendTo(s);
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
				jQuery('<option></option>').html('Choose an option...').attr('value',' ').appendTo(s);
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

		case 'date':
			s = jQuery('<input></input>')
				.prop('disabled',( ! a['updateable'] ) )
				.attr({
					'id':d,
					'name':a['name'],
					'type':(a['type']=='hidden'?'hidden':'text'),
					'picker':'date'
			});
			
			v = new String(v).split('T')[0];
			if(v && v !='null')
				jQuery(s).val(v);

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			} else {
				var args = {
						dateFormat:"yy-mm-dd",
						changeYear:true,
						changeMonth:true
				};
				if(a['name'] == 'Birthdate') {
					var year = new Date().getFullYear();
					var minyear = Math.max(1900,year - 125);
					args.yearRange = ''+minyear+':'+year;
				}
				if(jQuery(s).val()) {
					args.defaultDate = new Date(jQuery(s).val());
				}
				jQuery(s).datepicker(args);
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