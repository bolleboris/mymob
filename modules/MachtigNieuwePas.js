
var machtigNieuwePas = function(UserId,Naam){
	Ext.Msg.show({
	   title:'Machtiging nieuwe pas',
	   msg: 'Weet je zeker dat je <i>'+Naam+'</i> (#'+UserId+') het recht wilt geven een nieuwe pas in te voeren?<br>De gebruiker krijgt dan direct een email daarover.',
	   buttons: Ext.Msg.YESNO,
	   fn: function(e){
				if(e == 'yes'){
					machtigNieuwePasContinue(UserId,Naam);
				}
			},
	   animEl: 'header',
	   icon: Ext.MessageBox.QUESTION,
	   scope:this
	});
};

var machtigNieuwePasContinue = function(UserId,Naam){
	Ext.Ajax.request({
		url: './modules/api/machtigNieuwePas.php',
		params: { UserId : UserId },
		failure: function(){
			Ext.MessageBox.alert('Fout', 'Kan de server niet bereiken');
		},
		success: function(response, opts){
			var jsonData;
			try {
				jsonData = Ext.decode(response.responseText);
			}
			catch (e) {
				Ext.Msg.show({
					title: 'Fout', 
					msg: 'Onverwacht antwoord van de server ontvangen.',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
			if(jsonData.success === true){
			nieuwePassenStore.reload();		//Hoort wellicht niet hier?
				Ext.Msg.show({
					title: 'Gemachtigd', 
					msg: '<i>'+Naam+'</i> (#'+UserId+') is succesvol gemachtigd een nieuwe pas te introduceren.<br>Er is een automatisch mailtje verstuurd naar hem/haar.<br>Zijn pincode is: <font size=13><b>'+jsonData.code+'</b></font>',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				})
			}else{
				Ext.Msg.show({
					title: 'Kan niet machtigen', 
					msg: 'Het systeem gaf de volgende fout tijdens het uitvoeren van de machtiging:<br>'+jsonData.msg,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
		}
	});
};



var machtigNieuwePasIntrekken = function(UserId,Naam){
	Ext.Msg.show({
	   title:'Machtiging nieuwe pas intrekken?',
	   msg: 'Weet je zeker dat je <i>'+Naam+'</i> (#'+UserId+') het recht wilt <b>ontnemen</b> een nieuwe pas in te voeren?<br>De gebruiker krijgt dan <b>geen</b> geautmatiseerde email hierover.',
	   buttons: Ext.Msg.YESNO,
	   fn: function(e){
				if(e == 'yes'){
					machtigNieuwePasIntrekkenContinue(UserId,Naam);
				}
			},
	   animEl: 'header',
	   icon: Ext.MessageBox.QUESTION,
	   scope:this
	});
};

var machtigNieuwePasIntrekkenContinue = function(UserId,Naam){
	Ext.Ajax.request({
		url: './modules/api/machtigNieuwePasIntrekken.php',
		params: { UserId : UserId },
		failure: function(){
			Ext.MessageBox.alert('Fout', 'Kan de server niet bereiken');
		},
		success: function(response, opts){
			var jsonData;
			try {
				jsonData = Ext.decode(response.responseText);
			}
			catch (e) {
				Ext.Msg.show({
					title: 'Fout', 
					msg: 'Onverwacht antwoord van de server ontvangen.',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
			if(jsonData.success === true){
				nieuwePassenStore.reload();		//Hoort wellicht niet hier?
				Ext.Msg.show({
					title: 'Machtiging ingetrokken', 
					msg: '<i>'+Naam+'</i> (#'+UserId+') is vanaf nu niet meer gemachtigd een nieuwe pas te introduceren.',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				})
			}else{
				Ext.Msg.show({
					title: 'Kan niet machtigen', 
					msg: 'Het systeem gaf de volgende fout tijdens het intrekken van de machtiging:<br>'+jsonData.msg,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
		}
	});
};

