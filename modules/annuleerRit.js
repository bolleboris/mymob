
var annuleerRit = function(RitNr, UserId){
	Ext.Msg.show({
	   title:'Annuleren',
	   msg: 'Weet je zeker dat je rit '+RitNr+' van gebruiker '+UserId+' wilt annuleren?',
	   buttons: Ext.Msg.YESNO,
	   fn: function(e){
				if(e == 'yes'){
					annuleerRitContinue(RitNr, UserId);
				}
			},
	   animEl: 'header',
	   icon: Ext.MessageBox.QUESTION,
	   scope:this
	});
};

var annuleerRitContinue = function(RitNr, UserId){
	Ext.Ajax.request({
		url: './modules/api/annuleerRit.php',
		params: { RitNr: RitNr, UserId : UserId },
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
				Ext.Msg.show({
					title: 'Geannuleerd', 
					msg: 'Reservering '+RitNr+' is succesvol geannuleerd',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				})
			}else{
				Ext.Msg.show({
					title: 'Kan niet annuleren', 
					msg: 'Het reserveersysteem gaf de volgende fout tijdens het annuleren:<br>'+jsonData.msg,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
		}
	});
};

