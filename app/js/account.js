/*Polyfills!*/
if (!Array.isArray) {
  Array.isArray = function(arg) {
    return Object.prototype.toString.call(arg) === '[object Array]';
  };
}

var SFData;
if(!SFData) {
	SFData = {};
	SFData.Account = null;
	SFData.Contacts = [];
	SFData.def = {};
}
var getting = {};

jQuery(document).on('click','#profile-passport-new',function(e) {
	e.preventDefault();
	var i = newCardItem('passport',getBlank('PassportVisa'));
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
	var t = jQuery('#profile-passports');
	var y = t.find('.card-item-freq').last();
	if( !y.length ) {
		t.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
});

jQuery(document).on('click','.card-save-button',function(e) {
	e.preventDefault();
	jQuery(e.target).addClass('processing').prop({disabled:true});
	var f = jQuery(e.target).parents('form'),
		type = f.attr('form-object'),
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
		jQuery(e.target).removeClass('processing').prop({disabled:false});
	});
});

jQuery(document).on('click','.card-delete-button',function(e) {
	e.preventDefault();
	var f = jQuery(e.target).parents('form'),
		type = f.attr('form-object'),
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

jQuery(document).on('change','#contactselect',function(){
	if ( jQuery('#contactselect').val() == '' ) {
		// New account creation selected.  Pop up the dialog!
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
	}
	
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

jQuery(document).on('click','#contact-select-bar-item',function(e){
	e.preventDefault();
	e.stopPropagation();
});

jQuery(document).on('click','.bar-element',function(e){
	var p = jQuery(e.currentTarget),
		a = p.attr('collapse-match'),
		b = p.attr('collapse-open'),
		c = p.attr('collapse-close');
	if(p.attr('id') == 'status-bar') {
		return;
	}
	e.preventDefault();
	if(b) {
		jQuery(b).removeClass('collapsed');
	}
	if(c) {
		jQuery(c).addClass('collapsed');
	}
	if(p.hasClass('collapsed')){
		p.removeClass('collapsed');
		if(a) {
			jQuery(a).removeClass('collapsed');
		}
		jQuery.scrollTo('#account-bar',300,{offset:-25});
	} else {
		p.addClass('collapsed');
		if(a) {
			jQuery(a).addClass('collapsed');
		}
	}
});
	
jQuery(document).ready(function(){
	getAccount();
});

jQuery.ajax("/wp-admin/admin-ajax.php",{ data:{ action:'wpsf-getdef' } })
	.done(function(result) {
		if(result.success) {
			SFData.def = result.data;
		}
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
		updateAccountBar();
		getting.account = false;
		updateContacts();
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
	if ( SFData.Account.IsPrimaryContact__x ) {
		jQuery('<option></option>')
			.attr({value:''})
			.html('Add new contact...')
			.appendTo('#contactselect');
		jQuery('#contact-select-bar-item').show();
	}
	jQuery('#contactselect').val( SFData.Contacts[first].Id ).trigger('change');
}

function contactSelectionChange() {
	if ( ! SFData.Contacts ) {
		return;
	}
	var id = jQuery('#contactselect').val();
	jQuery('[form-object=Contact]').attr('form-record-id', id);
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

function updateAccountBar() {
	jQuery('[form-object=Account]').attr('form-record-id', SFData.Account.Id);
	jQuery('[form-object=Membership]').attr('form-record-id', SFData.Membership.Id);

	jQuery('.bar-element').find('[form-data-field]').each(function(x,f){
		var d = jQuery(f).attr('form-data-source');
		if(!d || !SFData.hasOwnProperty(d)) return;
		d = SFData[d];
		var i = jQuery(f).attr('form-data-field'), h='';
		if(d.hasOwnProperty(i)) {
			h = d[i];
		}
		jQuery(f).html(h);
	});
}

function newCardItem(y,i) {
	var c=jQuery('<div></div>').addClass('card-item card-item-'+y),
		f=jQuery('<form></form>'),
		p='carditem-'+getIdFromObject(i),
		z=[],x,o;
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
			     'Name',
			     'Carrier_Hotel_Operator__c',
			     'Frequent_Traveler_Program__c',
			     'Frequent_Flyer_Number__c',
			     'Assistant_Email__c',
			     'Assistant_Name__c',
			     'Assistant_Phone__c'
		    ];
			break;
	}
	f.attr('form-object',o);
	for(x in i) {
		if(jQuery.inArray(x,z) >= 0)
			f.append(makeInput(SFData.def[o][x],p,i[x],false));
	}
	f.find('input[name="Contact__c"]').val( jQuery('#contactselect').val() );
	
	jQuery('<a></a>').attr('href','#').html('Delete Item').addClass('card-delete-button card-button-large').appendTo(f);
	jQuery('<a></a>').attr('href','#').html('Save Item').addClass('card-save-button card-button-large').appendTo(f);
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
	});
}

function updateForms() {
	jQuery('[form-fields] .auto-created-field').remove();
	jQuery('[form-fields]').each(function(x,f){
		var i, 
			p = SFData.Account.IsPrimaryContact__x,
			d = null,
			y = jQuery(f).attr('id'),
			z = jQuery(f).attr('form-fields'),
			a = jQuery(f).attr('form-object');
		if(!y||!y.length) return;
		switch(a) {
			case 'Contact':
				d = contactData(jQuery(f).attr('form-record-id'));
				p = p || d.IsCurrentUser__x;
				p = !p;
				
				jQuery('#profile-passports').empty();
				
				if(!!d && d['Passport_Visa__x']) {
					for ( i in d['Passport_Visa__x'] ) {
						jQuery('#profile-passports').append(newCardItem('passport',d['Passport_Visa__x'][i]));
					}
				}
				
				if(!!d && d['Frequent_Travel__x']) {
					for ( i in d['Frequent_Travel__x'] ) {
						jQuery('#profile-passports').append(newCardItem('freq',d['Frequent_Travel__x'][i]));
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
		var tgt = jQuery(f).find('.card-save-button');
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
	jQuery.scrollTo('#account-bar',300,{offset:-25});
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
				if ( ! y['active'] ) 
					continue;
				
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
						'validfor':y['validFor']
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
				jQuery('<option></option>').html('Choose an option...').attr('value','').appendTo(s);
			}
			
			for ( i in a['picklistValues'] ) {
				y = a['picklistValues'][i];
				if ( ! y['active'] ) 
					continue;
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':y['validFor']
					})
					.appendTo(s);
			}
			
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
				jQuery('<option></option>').html('Choose an option...').attr('value','').appendTo(s);
			}
			
			for ( i in SFData.def.Countries ) {
				y = SFData.def.Countries[i];
				if ( ! y['active'] ) 
					continue;
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':y['validFor']
					})
					.appendTo(s);
			}
			
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
				jQuery('<option></option>').html('Choose an option...').attr('value','').appendTo(s);
			}
			
			for ( i in SFData.def.States ) {
				y = SFData.def.States[i];
				if ( ! y['active'] ) 
					continue;
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':y['validFor']
					})
					.appendTo(s);
			}
			
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