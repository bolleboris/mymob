var AutoDetailId = '';

var autoDetailStoreReader = new Ext.data.JsonReader({
	idProperty: 'AutoId',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'AutoId'},
		{name: 'BeheerdersId'},
		{name: 'Merk'},
		{name: 'Model'},
		{name: 'Bijnaam'},
		{name: 'Kenteken'},
		{name: 'Brandstof'},
		{name: 'Kluiscode'},
		{name: 'Tankpascode'},
		{name: 'ToeslagPerKilometer'},
		{name: 'ToeslagPerUur'},
		{name: 'Opmerkingen'},
		{name: 'Afbeelding'},
		{name: 'ToeslagPerUur'},
		{name: 'Opmerkingen'},
		{name: 'Handleiding'},
		{name: 'Afbeelding'},
		{name: 'Actief'},
		{name: 'Latitude'},
		{name: 'Longitude'},			
		{name: 'AantalZitplaatsen'},
		{name: 'Boordcomputer'},
		{name: 'Airco'},
		{name: 'Trekhaak'},
		{name: 'MP3Aansluiting'},									
		{name: 'OptiesAanvullend'},
		{name: 'Adres'},
		{name: 'Plaats'},
		{name: 'Postcode'},
		{name: 'ToestemmingVereist'}			
	]
})
var autoDetailStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	storeId: 'autoDetails',
	reader: autoDetailStoreReader
});

autoDetailStore.on('exception',handleProxyException);

var brandstofStore = new Ext.data.SimpleStore({
	fields: ['num', 'desc'],
	data: [[1, 'benzine'],
	[2, 'diesel'],
	[3, 'LPG'],
	[4, 'aardgas'],
	[5, 'waterstof'],
	[6, 'elektrisch']]
});
	
var toestemmingVereistStore = new Ext.data.SimpleStore({
	fields: ['num', 'desc'],
	data: [
		[1, 'Ja'],
		[0, 'Nee']
	]
});

/*var brandstofStore = new Ext.data.ArrayStore({
	fields: ['num', 'desc'],
	idIndex: 0 // id for each record will be the first element
});

var brandstofTypen = [
	[1, 'benzine'],
	[2, 'diesel'],
	[3, 'LPG'],
	[4, 'aardgas'],
	[5, 'waterstof'],
	[6, 'elektrisch']
];*/

//brandstofStore.loadData(brandstofTypen);

var autoImagesStore = new Ext.data.JsonStore({
	autoLoad: false,
	remoteSort : false,
	storeId: 'autoDetails',
	url : './modules/api/getCarImages.php',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'Num'},
		{name: 'Filename'}
	]
});

// Custom rendering Template voor afbeelding selector
var imageSelectorDropdownTpl = new Ext.XTemplate(
    '<tpl for="."><div class="image-select">',
        '<table width=300 border=0 style=\'font-size:12;\'><tr><td><b>{Filename}</b></td><td width=125 align="left"><img scr="https://www.wheels4all.nl/images-autos/groot/{Filename}.gif"></td></tr></table>',
    '</div></tpl>'
);

var autoDetailWindowBottomBar = new Ext.Toolbar({			//autoDetailWindowBottomBar voor main window
   items : [ 
	'->',
	{
	//	iconCls : 'icon-arrow_refresh',
		iconCls : 'icon-arrow_undo',		
		text: 'Herstel',
		handler: function(){
			Ext.Msg.show({
			   title:'Ongedaan maken',
			   msg: 'Weet je zeker dat je je wijzigingen ongedaan wilt maken?',
			   buttons: Ext.Msg.YESNO,
			   fn: function(e){
					if(e == 'yes') autoDetailForm.getForm().load({url:'./modules/api/getCar.php', params: {AutoId : AutoDetailId}});
				},
			   animEl: 'header',
			   icon: Ext.MessageBox.QUESTION,
			   scope:this
			})	
		}
	},'-',{
     	iconCls : 'icon-disk',
		text: 'Opslaan',
		handler: function(){
			autoDetailForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/putCar.php',
				waitMsg: 'Bezig met opslaan...',
				success: function(){
					Ext.Msg.show({
					   title:'Opgeslagen',
					   msg: 'Je wijzigingen zijn succesvol opgeslagen',
					   icon: Ext.MessageBox.INFO,
					   buttons: Ext.Msg.OK,
					   scope:this
					});
				},
				failure: function(form,action){
					if(action.failureType == 'client'){
						Ext.Msg.show({
						   title:'Fout',
						   msg: 'Je hebt het formulier niet correct ingevuld.<br>Verbeter de velden met rode onderstreping.',
						   icon: Ext.MessageBox.ERROR,
						   buttons: Ext.Msg.OK,
						   scope:this
						});
					}else{
						Ext.Msg.show({
						   title:'Fout',
						   msg: 'Er is iets fout gegaan.<br>De gegevens zijn niet opgeslagen!<br>Failure: '+action.failureType,
						   icon: Ext.MessageBox.ERROR,
						   buttons: Ext.Msg.OK,
						   scope:this
						});
					}
				}
			});
		}
	},'-',{
		iconCls : 'icon-cross',
		text: 'Sluit',
		handler: function(){
			this.findParentByType('window').hide();
		}
	}]
});


var autoDetailForm = new Ext.FormPanel({
	id: 'autoDetailForm',
	layout:'column',
	bodyStyle:'padding:0px 0px 0px 0px',
	reader: autoDetailStoreReader,
	items:[{
		xtype:'fieldset',
		columnWidth:.35,
		height: 420,
		layout: 'form',
		bodyStyle:'padding:5px 5px 5px 5px',
		items: [{
			xtype:'numberfield',
			fieldLabel: 'AutoId',
			name: 'AutoId',
			dataIndex: 'AutoId',
			allowBlank: false,
			anchor: '100%',
			readOnly: true
		}, {
			xtype:'textfield',
			fieldLabel: 'Kenteken',
			name: 'Kenteken',
			allowBlank: false,
			anchor: '100%'
		}, {
			xtype: 'checkbox',
			fieldLabel: 'Actief',
			name: 'Actief',
			anchor: '100%'
		}, {
			xtype: 'numberfield',
			fieldLabel: 'Toeslag KM',
			name: 'ToeslagPerKilometer',
			anchor: '100%'
		}, {
			xtype: 'numberfield',
			fieldLabel: 'Toeslag uur',
			name: 'ToeslagPerUur',
			anchor: '100%'
		}, {
			xtype: 'numberfield',
			fieldLabel: 'Latitude',
			name: 'Latitude',
			allowDecimals: true,
			decimalPrecision: 7,
			anchor: '100%'
		}, {
			xtype: 'numberfield',
			fieldLabel: 'Longitude',
			allowDecimals: true,
			decimalPrecision: 7,
			
			name: 'Longitude',
			anchor: '100%'
		}, {
			xtype: 'numberfield',
			fieldLabel: 'BeheerdersId',
			name: 'BeheerdersId',
			anchor: '100%'
		}, {
			xtype: 'button',
			name: 'BeheerdersId',
			text: 'Zoek een beheerder',
			disabled: true,
			anchor: '100%'
		}, {
			xtype: 'textfield',
			fieldLabel: 'Adres',
			name: 'Adres',
			vtype: 'testAdres',
			//disabled: true,
			anchor: '100%'
		}, {
			xtype: 'textfield',
			fieldLabel: 'Postcode',
			name: 'Postcode',
			vtype: 'testPostCode',
			//disabled: true,
			anchor: '100%'
		}, {
			xtype: 'textfield',
			fieldLabel: 'Plaats',
			name: 'Plaats',
			vtype: 'testPlaatsNaam',
			//disabled: true,
			anchor: '100%'
		}, {
			xtype: 'button',
			name: 'Adres',
			text: 'Gebruik adres van beheerder',
			disabled: true,
			anchor: '100%'
		}]
	},{
	 	xtype:'fieldset',
		columnWidth:.35,
		height: 420,
		layout: 'form',
		bodyStyle:'padding:5px 5px 5px 5px',
		items: [{
			xtype:'textfield',
			fieldLabel: 'Merk',
			name: 'Merk',
			dataIndex: 'Merk',
			anchor: '100%'
		},{
			xtype:'textfield',
			fieldLabel: 'Model',
			name: 'Model',
			dataIndex: 'Model',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Bijnaam',
			name: 'Bijnaam',
			dataIndex: 'Bijnaam',
			allowBlank: false,
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Kluiscode',
			name: 'Kluiscode',
			dataIndex: 'Kluiscode',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Tankpascode',
			name: 'Tankpascode',
			dataIndex: 'Tankpascode',
			anchor: '100%'
		}, {
			xtype: 'combo',
			fieldLabel: 'Brandstof',
			store: brandstofStore,
			displayField: 'desc',
			valueField: 'num',
			name: 'Brandstof',
			emptyText:'Kies brandstof...',
			mode: 'local',
			editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
			forceSelection: true,
			triggerAction: 'all',
			selectOnFocus: true,
			lazyInit: false,		//Init items direct, niet op dropdown event
			anchor: '100%'
		}, {
			xtype:'numberfield',
			fieldLabel: 'Aantal zitpl.',
			name: 'AantalZitplaatsen',
			anchor: '100%'
		}, {
			xtype: 'checkbox',
			fieldLabel: 'Boordcomputer',
			name: 'Boordcomputer',
			anchor: '100%'
		}, {
			xtype: 'checkbox',
			fieldLabel: 'Trekhaak',
			name: 'Trekhaak',
			anchor: '100%'
		}, {
			xtype: 'checkbox',
			fieldLabel: 'Airco',
			name: 'Airco',
			anchor: '100%'
		}, {
			xtype: 'checkbox',
			fieldLabel: 'MP3 aansluiting',
			name: 'MP3Aansluiting',
			anchor: '100%'
		}, {
			xtype:'textarea',
			fieldLabel: 'Overige opties',
			name: 'OptiesAanvullend',
			height: 70,
			anchor: '100%'
		}]
	},{
		xtype:'fieldset',
		columnWidth:.3,
		labelAlign: 'top',
		height: 420,
		layout: 'form',
		bodyStyle:'padding:5px 5px 5px 5px',
		items: [{
			xtype:'textarea',
			fieldLabel: 'Opmerkingen',
			name: 'Opmerkingen',
			height: 100,
			anchor:'100%'
		},{
			xtype:'textarea',
			fieldLabel: 'Handleiding',
			name: 'Handleiding',
			height: 100,
			anchor:'100%'
		},{
			xtype: 'combo',
			fieldLabel: 'Afbeelding',
			store: autoImagesStore,
			displayField: 'Filename',
			valueField: 'Num',
			name: 'Afbeelding',
			emptyText:'Kies afbeelding...',
			mode: 'local',
			editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
			forceSelection: true,
			triggerAction: 'all',
			selectOnFocus: true,
			lazyInit: false,		//Init items indirect, op dropdown event
			anchor: '100%',
			tpl: imageSelectorDropdownTpl,
    		itemSelector: 'div.image-select'
		},{
			xtype: 'combo',
			fieldLabel: 'Toestemming beheerder nodig',
			store: toestemmingVereistStore,
			triggerAction: 'all',
			valueField: 'num',
			hiddenName : 'ToestemmingVereist',	//Zorg dat 'num' ipv 'desc' wordt gepost
			displayField: 'desc',
			emptyText:'Kies...',
			mode: 'local',
			name: 'ToestemmingVereist',
			editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
			forceSelection: true,
			triggerAction: 'all',
			selectOnFocus: true,
			lazyInit: false,		//Init items direct, niet op dropdown event
			anchor: '100%'
		}]
	}]
});

var autoDetailWindow = new Ext.Window({
	title: 'Wijzig autogegevens',
	width: 800,
	height: 430,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: autoDetailForm,
	bbar:	autoDetailWindowBottomBar
});

var showCarDetails = function(AutoId){
	autoDetailWindow.show(this);
	autoDetailWindow.setTitle("Details auto "+AutoId);
	autoImagesStore.load();
	AutoDetailId = AutoId;
	autoDetailForm.getForm().load({url:'./modules/api/getCar.php', params: {AutoId : AutoDetailId}, waitMsg:'Laden...'});
};
