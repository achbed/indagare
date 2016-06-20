var defaultCountry = 'United States';
var country_arr = {
	"Afghanistan":[],
	"Aland Islands":[],
	"Albania":[],
	"Algeria":[],
	"Andorra":[],
	"Angola":[],
	"Anguilla":[],
	"Antarctica":[],
	"Antigua and Barbuda":[],
	"Argentina":[],
	"Armenia":[],
	"Aruba":[],
	"Australia":["Australian Capital Territory","New South Wales","Northern Territory","Queensland","South Australia","Tasmania","Victoria","Western Australia"],
	"Austria":[],
	"Azerbaijan":[],
	"Bahamas":[],
	"Bahrain":[],
	"Bangladesh":[],
	"Barbados":[],
	"Belarus":[],
	"Belgium":[],
	"Belize":[],
	"Benin":[],
	"Bermuda":[],
	"Bhutan":[],
	"Bolivia, Plurinational State of":[],
	"Bonaire, Sint Eustatius and Saba":[],
	"Bosnia and Herzegovina":[],
	"Botswana":[],
	"Bouvet Island":[],
	"Brazil":["Acre","Alagoas","Amapá","Amazonas","Bahia","Ceará","Distrito Federal","Espírito Santo","Goiás","Maranhão","Mato Grosso","Mato Grosso do Sul","Minas Gerais","Pará","Paraíba","Paraná","Pernambuco","Piauí","Rio de Jeneiro","Rio Grande do Norte","Rio Grande do Sul","Rondônia","Roraima","Santa Catarina","São Paulo","Sergipe","Tocantins"],
	"British Indian Ocean Territory":[],
	"Brunei Darussalam":[],
	"Bulgaria":[],
	"Burkina Faso":[],
	"Burundi":[],
	"Cambodia":[],
	"Cameroon":[],
	"Canada":["Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia","Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon Territories"],
	"Cape Verde":[],
	"Cayman Islands":[],
	"Central African Republic":[],
	"Chad":[],
	"Chile":[],
	"China":["Anhui","Beijing","Chongqing","Fujian","Gansu","Guangdong","Guangxi","Guizhou","Hainan","Hebei","Heilongjiang","Henan","Hong Kong","Hubei","Hunan","Jiangsu","Jiangxi","Jilin","Liaoning","Macao","Nei Mongol","Ningxia","Qinghai","Shaanxi","Shandong","Shanghai","Shanxi","Sichuan","Taiwan","Tianjin","Xinjiang","Xizang","Yunnan","Zhejiang"],
	"Christmas Island":[],
	"Cocos (Keeling) Islands":[],
	"Colombia":[],
	"Comoros":[],
	"Congo":[],
	"Congo, the Democratic Republic of the":[],
	"Cook Islands":[],
	"Costa Rica":[],
	"Cote d’Ivoire":[],
	"Croatia":[],
	"Cuba":[],
	"Curaçao":[],
	"Cyprus":[],
	"Czech Republic":[],
	"Denmark":[],
	"Djibouti":[],
	"Dominica":[],
	"Dominican Republic":[],
	"Ecuador":[],
	"Egypt":[],
	"El Salvador":[],
	"Equatorial Guinea":[],
	"Eritrea":[],
	"Estonia":[],
	"Ethiopia":[],
	"Falkland Islands (Malvinas)":[],
	"Faroe Islands":[],
	"Fiji":[],
	"Finland":[],
	"France":[],
	"French Guiana":[],
	"French Polynesia":[],
	"French Southern Territories":[],
	"Gabon":[],
	"Gambia":[],
	"Georgia":[],
	"Germany":[],
	"Ghana":[],
	"Gibraltar":[],
	"Greece":[],
	"Greenland":[],
	"Grenada":[],
	"Guadeloupe":[],
	"Guatemala":[],
	"Guernsey":[],
	"Guinea":[],
	"Guinea-Bissau":[],
	"Guyana":[],
	"Haiti":[],
	"Heard Island and McDonald Islands":[],
	"Holy See (Vatican City State)":[],
	"Honduras":[],
	"Hungary":[],
	"Iceland":[],
	"India":["Andaman and Nicobar Islands","Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chandigarh","Chhattisgarh","Dadra and Nagar Haveli","Daman and Diu","Delhi","Goa","Gujarat","Harayana","Himachal Pradesh","Jammu and Kashmir","Jharkhand","Karnataka","Kerala","Lakshadweep","Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram","Nagaland","Odisha","Puducherry","Punjab","Rajasthan","Sikkim","Tamil Nadu","Tripura","Uttarakhand","Uttar Pradesh","West Bengal"],
	"Indonesia":[],
	"Iran, Islamic Republic of":[],
	"Iraq":[],
	"Ireland":["Carlow","Cavan","Clare","Cork","Donegal","Dublin","Galway","Kerry","Kildare","Kilkenny","Laois","Leitrim","Limerick","Longford","Louth","Mayo","Meath","Monaghan","Offaly","Roscommon","Sligo","Tipperary","Waterford","Westmeath","Wexford","Wicklow"],
	"Isle of Man":[],
	"Israel":[],
	"Italy":["Agrigento","Alessandria","Ancona","Aosta","Arezzo","Ascoli Piceno","Asti","Avellino","Bari","Barletta-Andria-Trani","Belluno","Benevento","Bergamo","Biella","Bologna","Bolzono","Brescia","Brindisi","Cagliari","Caltanissetta","Campobasso","Carbonia-Iglesias","Caserta","Catania","Catanzaro","Chieti","Como","Cosenza","Cremona","Crotone","Cuneo","Enna","Fermo","Ferrara","Florence","Foggia","Forlì-Cesena","Frosinone","Genoa","Gorizia","Grosseto","Imperia","Isernia","L'Aquila","La Spezia","Latina","Lecce","Lecco","Livorno","Lodi","Lucca","Macerata","Mantua","Massa and Carrara","Matera","Medio Campidano","Messina","Milan","Modena","Monza and Brianza","Naples","Novara","Nuoro","Ogliastra","Olbia-Tempio","Oristano","Padua","Palermo","Parma","Pavia","Perugia","Pesaro and Urbino","Pescara","Piacenza","Pisa","Pistoia","Pordenone","Potenza","Prato","Ragusa","Ravenna","Reggio Calabria","Reggio Emilia","Rieti","Rimini","Rome","Rovigo","Salerno","Sassari","Savona","Siena","Sondrio","Syracuse","Taranto","Teramo","Terni","Trapani","Trento","Treviso","Trieste","Turin","Udine","Varese","Venice","Verbano-Cusio-Ossola","Vercelli","Verona","Vibo Valentia","Vicenza","Viterbo"],
	"Jamaica":[],
	"Japan":[],
	"Jersey":[],
	"Jordan":[],
	"Kazakhstan":[],
	"Kenya":[],
	"Kiribati":[],
	"Korea, Democratic People’s Republic of":[],
	"Korea, Republic of":[],
	"Kuwait":[],
	"Kyrgyzstan":[],
	"Lao People’s Democratic Republic":[],
	"Latvia":[],
	"Lebanon":[],
	"Lesotho":[],
	"Liberia":[],
	"Libyan Arab Jamahiriya":[],
	"Liechtenstein":[],
	"Lithuania":[],
	"Luxembourg":[],
	"Macao":[],
	"Macedonia, the former Yugoslav Republic of":[],
	"Madagascar":[],
	"Malawi":[],
	"Malaysia":[],
	"Maldives":[],
	"Mali":[],
	"Malta":[],
	"Martinique":[],
	"Mauritania":[],
	"Mauritius":[],
	"Mayotte":[],
	"Mexico":["Aguascalientes","Baja California","Baja California Sur","Campeche","Chiapas","Chihuahua","Coahuila","Colima","Durango","Federal District","Guanajuato","Guerrero","Hidalgo","Jalisco","Mexico State","Michoacán","Morelos","Nayarit","Nuevo León","Oaxaca","Puebla","Querétaro","Quintana Roo","San Luis Potosí","Sinaloa","Sonora","Tabasco","Tamaulipas","Tlaxcala","Veracruz","Yucatán","Zacatecas"],
	"Moldova, Republic of":[],
	"Monaco":[],
	"Mongolia":[],
	"Montenegro":[],
	"Montserrat":[],
	"Morocco":[],
	"Mozambique":[],
	"Myanmar":[],
	"Namibia":[],
	"Nauru":[],
	"Nepal":[],
	"Netherlands":[],
	"New Caledonia":[],
	"New Zealand":[],
	"Nicaragua":[],
	"Niger":[],
	"Nigeria":[],
	"Niue":[],
	"Norfolk Island":[],
	"Norway":[],
	"Oman":[],
	"Pakistan":[],
	"Palestine":[],
	"Panama":[],
	"Papua New Guinea":[],
	"Paraguay":[],
	"Peru":[],
	"Philippines":[],
	"Pitcairn":[],
	"Poland":[],
	"Portugal":[],
	"Qatar":[],
	"Reunion":[],
	"Romania":[],
	"Russian Federation":[],
	"Rwanda":[],
	"Saint Barthélemy":[],
	"Saint Helena, Ascension and Tristan da Cunha":[],
	"Saint Kitts and Nevis":[],
	"Saint Lucia":[],
	"Saint Martin (French part)":[],
	"Saint Pierre and Miquelon":[],
	"Saint Vincent and the Grenadines":[],
	"Samoa":[],
	"San Marino":[],
	"Sao Tome and Principe":[],
	"Saudi Arabia":[],
	"Senegal":[],
	"Serbia":[],
	"Seychelles":[],
	"Sierra Leone":[],
	"Singapore":[],
	"Sint Maarten (Dutch part)":[],
	"Slovakia":[],
	"Slovenia":[],
	"Solomon Islands":[],
	"Somalia":[],
	"South Africa":[],
	"South Georgia and the South Sandwich Islands":[],
	"South Sudan":[],
	"Spain":[],
	"Sri Lanka":[],
	"Sudan":[],
	"Suriname":[],
	"Svalbard and Jan Mayen":[],
	"Swaziland":[],
	"Sweden":[],
	"Switzerland":[],
	"Syrian Arab Republic":[],
	"Taiwan":[],
	"Tajikistan":[],
	"Tanzania, United Republic of":[],
	"Thailand":[],
	"Timor-Leste":[],
	"Togo":[],
	"Tokelau":[],
	"Tonga":[],
	"Trinidad and Tobago":[],
	"Tunisia":[],
	"Turkey":[],
	"Turkmenistan":[],
	"Turks and Caicos Islands":[],
	"Tuvalu":[],
	"Uganda":[],
	"Ukraine":[],
	"United Arab Emirates":[],
	"United Kingdom":[],
	"United States":["Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District of Columbia","Florida","Georgia","Hawaii","Idaho","Illionis","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Verginia","Wisconsin","Wyoming"],
	"Uruguay":[],
	"Uzbekistan":[],
	"Vanuatu":[],
	"Venezuela, Bolivarian Republic of":[],
	"Vietnam":[],
	"Virgin Islands, British":[],
	"Wallis and Futuna":[],
	"Western Sahara":[],
	"Yemen":[],
	"Zambia":[],
	"Zimbabwe":[]
};


function populateStates( countryElementId, stateElementId, defaultState ){
	var stateElement = document.getElementById( stateElementId );
	stateElement.length = 0;
	
	var e = document.getElementById(countryElementId);
	var currentCountry = e.options[e.selectedIndex].value;
	if ( ! country_arr.hasOwnProperty( currentCountry ) ) {
		stateElement.options[0] = new Option( '', '' );
		stateElement.selectedIndex = 0;
		return;
	}
	var state_arr = country_arr[currentCountry];
	if ( state_arr.length < 1 ) {
		stateElement.options[0] = new Option( '', '' );
		stateElement.selectedIndex = 0;
		return;
	}
	
	stateElement.options[0] = new Option( 'Select State...', '' );
	stateElement.selectedIndex = 0;
	
	for (var i=0; i<state_arr.length; i++) {
		var o = new Option(state_arr[i],state_arr[i]);
		stateElement.options[stateElement.length] = o;
		if(defaultState && (state_arr[i] == defaultState)) {
			o.selected = true;
		}
	}
	
	jQuery( stateElement ).trigger('render');
}

function populateCountries( countryElementId, stateElementId, defaultCountry, defaultState ) {
	// given the id of the <select> tag as function argument, it inserts <option> tags
	var countryElement = document.getElementById(countryElementId);
	countryElement.length = 0;
	countryElement.options[0] = new Option( 'Select Country...', '' );
	countryElement.selectedIndex = 0;
	for ( c in country_arr ) {
		var o = new Option(c,c);
		o.selected = false;
		if ( defaultCountry && ( c == defaultCountry ) ) {
			o.selected = true;
		}
		countryElement.options[countryElement.length] = o;
	}

	// Assigned all countries. Now assign event listener for the states.

	if( stateElementId ) {
		if(countryElement.selectedIndex) {
			populateStates( countryElementId, stateElementId, defaultState );
		}
		countryElement.onchange = function() {
			populateStates( countryElementId, stateElementId, defaultState );
		};
	}
	
	jQuery( countryElement ).trigger('render');
}