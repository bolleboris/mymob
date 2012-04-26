
var resendRes = function(RitNr){
	Ext.Ajax.request({
		url: './modules/api/resentReservation.php',
		params: { RitNr: RitNr},
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
				Ext.MessageBox.alert('Succes', 'Reservering is succesvol opnieuw verstuurd');
			}else{
				Ext.Msg.show({
					title: 'Kan de rit niet opnieuw versturen', 
					msg: 'Kan de rit niet opnieuw versturen, reden:<br>'+jsonData.msg,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				})
			}
		}
	});
};

