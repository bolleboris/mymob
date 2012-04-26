// custom vtype voor rijbewijs
	var rbTest = /^\d{10,10}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		rb: function(val, field) {
			return rbTest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		rbText: 'Uw rijbewijsnummer moet bestaan uit 10 cijfers.<br>Bijvoorbeeld: 1234567890',
		// vtype Mask property: The keystroke filter mask
		rbMask: /[\d]/i
	});
	
	//Regex voor emailadressen
	var emailaddrtest = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testEmail: function(val, field) {
			return emailaddrtest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testEmailText: 'Uw emailadres moet de volgende vorm hebben:<br>[naam]@[domein].[extentie]<br>Bijvoorbeeld: test@wheels4all.nl',
		// vtype Mask property: The keystroke filter mask
		testEmailMask: /[\w\.\@]/i
	});

	//Regex voor adressen, <straat> <huisnummer><extentie>, er mag max 1 extentieletter achter staan (bijv 11a, niet 11ab)
	var addrtest = /^[a-zA-Z\x20\-.]+\x20[0-9]+\x20{0,1}[a-zA-Z]{0,1}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testAdres: function(val, field) {
			return addrtest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testAdresText: 'Uw adres moet de volgende vorm hebben:<br>[straat] [huisnummer] [extentie]<br>Let op de spatie tussen straat, huisnummer en extentie.<br>Bijvoorbeeld: Dorpstraat 11 a',
		// vtype Mask property: The keystroke filter mask
		testAdresMask: /[\w\x20\-.]/i		// x20 = space
	});
	
	//Regex voor nederlandse postcodes
	var postcodetest = /^[0-9]{4,4}[A-Z]{2,2}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testPostCode: function(val, field) {
			return postcodetest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testPostCodeText: 'Uw postcode moet de volgende vorm hebben:<br>[4 nummers][2 hoofdletter]<br>Let op: Zonder spatie ertussen.<br>Bijvoorbeeld: 1234AA',
		// vtype Mask property: The keystroke filter mask
		testPostCodeMask: /[0-9A-Z\x20]/i		// x20 = space
	});
	
	//Regex voor telefoon nummers. Maximaal 4 cijfers voor het kerngetal, vervolgens maximaal 8 cijfers voor het nummer. Kerngetal en toestelnummer moeten zijn gescheiden met een streepje.
	var telefoontest = /^[0-9]{2,4}-[0-9]{6,8}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testTelefoon: function(val, field) {
			return telefoontest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testTelefoonText: 'Uw telefoonnummer moet de volgende vorm hebben:<br>[kerngetal]-[abonneenummer]<br>Let op het streepje tussen uw kerngetal en abonneenummer.<br>Bijvoorbeeld: 0123-456789',
		// vtype Mask property: The keystroke filter mask
		testTelefoonMask: /[0-9-]/i		// x20 = space
	});
	
	//Regex voor mobiele telefoon nummers. Moet beginnen met 06-
	var telefoonmobieltest = /^06-[0-9]{8,8}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testTelefoonMobiel: function(val, field) {
			return telefoontest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testTelefoonMobielText: 'Uw mobiele telefoonnummer moet de volgende vorm hebben:<br><b>06-[abonneenummer]</b><br>Bijvoorbeeld: 06-12345678',
		// vtype Mask property: The keystroke filter mask
		testTelefoonMobielMask: /[0-9-]/i		// x20 = space
	});
	
	//Regex voor Nederlandse plaatsnaam. Kortste plaatsnaam is 'ee' in Friesland, langste is 'Gasselterboerveenschemond', dus een nederlandse plaatsnaam moet
	//minimaal 2, en maximaal 25 letters bevatten. De enige speciale tekens toegestaan zijn de spatie en het streepje.
	var plaatsnaamtest = /^[a-zA-Z\x20-]{2,25}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testPlaatsNaam: function(val, field) {
			return plaatsnaamtest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testPlaatsNaamText: 'U mag hier alleen geen geldige plaatsnaam invullen',
		// vtype Mask property: The keystroke filter mask
		testPlaatsNaamMask: /[A-Za-z\x20-]/i		// x20 = space
	});
	
		//Regex voor mobiele telefoon nummers. Moet beginnen met 06-
//	var kentekentest = /^06-[0-9]{8,8}$/;
	var kentekentest = /^[a-zA-Z]{3}[0-9]{3}$/;
	//|\w{2}-\w{2}-\w{2}|[0-9]{2}-[a-zA-Z]{3}-[0-9]{1}|[0-9]{1}-[a-zA-Z]{3}-[0-9]{2}|[a-zA-Z]{1}-[0-9]{3}-[a-zA-Z]{2}$/;
	Ext.apply(Ext.form.VTypes, {
		//  vtype validation function
		testKenteken: function(val, field) {
			return kentekentest.test(val);
		},
		// vtype Text property: The error text to display when the validation function returns false
		testKentekenText: 'Dit is geen geldig nederlands kenteken',
		// vtype Mask property: The keystroke filter mask
		testKentekenMask: /[0-9A-Z-]/i		// 
	});
