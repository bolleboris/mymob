var personDetailId = '';

var personDetailStoreReader = new Ext.data.JsonReader({
	idProperty: 'PersonId',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'PersonId'},
		{name: 'Settings_W4APersoonNr_Private'},
		{name: 'Name_FirstName_Public'},
		{name: 'Name_Preposition_Public'},
		{name: 'Name_SurName_Public'},
		{name: 'Name_Initials_Public'},
		{name: 'General_Gender_Public'},
		{name: 'General_BirthDate_Public'},
		{name: 'General_EmailAddress_Public'},
		{name: 'General_Telephone_Public'},
		{name: 'General_Telephone2_Protected'},
		{name: 'General_Telephone3_Protected'},
		{name: 'General_DriverLicenceNr_Protected'},
		{name: 'HomePosition_Latitude_Public'},
		{name: 'HomePosition_Longitude_Public'},
		{name: 'Settings_EmailResConf_Public'},
		{name: 'Settings_AmountOfEmail_Protected'},
		{name: 'Settings_FavouriteCar_Protected'},
		{name: 'Address_Zipcode_Public'},
		{name: 'Address_HouseNr_Public'},
		{name: 'Address_Affix_Public'},
		{name: 'Address_City_Public'},
		{name: 'Address_StreetName_Public'},
		{name: 'Address_Country_Public'},
	]
});

var personDetailStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	storeId: 'personDetails',
	reader: personDetailStoreReader
});

personDetailStore.on('exception',handleProxyException);
	
/* Hou erin als voorbeeld!
   var toestemmingVereistStore = new Ext.data.SimpleStore({
	fields: ['num', 'desc'],
	data: [
		[1, 'Ja'],
		[0, 'Nee']
	]
});*/

/*var autoImagesStore = new Ext.data.JsonStore({
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
});*/

var personDetailWindowBottomBar = new Ext.Toolbar({			//personDetailWindowBottomBar voor main window
   items : ['->',{
	   iconCls : 'icon-user_add',
	   text: 'Nieuwe gebruiker',
	   handler: function() {
		 showCreatePerson();
	   }
	},{
	//	iconCls : 'icon-arrow_refresh',
		iconCls : 'icon-arrow_undo',		
		text: 'Herstel',
		handler: function(){
			Ext.Msg.show({
			   title:'Ongedaan maken',
			   msg: 'Weet je zeker dat je je wijzigingen ongedaan wilt maken?',
			   buttons: Ext.Msg.YESNO,
			   fn: function(e){
					if(e == 'yes') personDetailForm.getForm().load({url:'./modules/api/getMyMobilityPerson.php', params: {PersonId : personDetailId}});
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
			personDetailForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/putMyMobilityPerson.php',
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
					if (action.failureType == 'client') {
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


var genderType = new Ext.data.SimpleStore({
	  fields: ['num','desc'],
	  data: [
		 [1,'Man'],
		 [0,'Vrouw']
	  ]
});

var blockedType = new Ext.data.SimpleStore({
  fields: ['num','desc'],
  data: [
	 [1, 'Ja'],
	 [0, 'Nee']
  ]
})

var personDetailForm = new Ext.FormPanel({
  id: 'personDetailForm',
  bodystyle: 'padding: 0px 0px 0px 0px',
  reader: personDetailStoreReader,
  columnWidth: 0.7,
  items: [{
	columnWidth:.5,
    title: 'Personalia',
    layout:'column',
    items:[{
      columnWidth:.5,
      layout: 'form',
      items: [{
			xtype:'numberfield',
			fieldLabel: 'PersonId',
			name: 'PersonId',
			dataIndex: 'PersonId',
			anchor: '100%',
			readOnly: true
		}, {
			xtype:'textfield',
			fieldLabel: 'Naam',
			name: 'Name_FirstName_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Initialen',
			name: 'Name_Initials_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Tussenvoegsels',
			name: 'Name_Preposition_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Achternaam',
			name: 'Name_SurName_Public',
			anchor: '100%'
		}]
	},{
      columnWidth:.5,
      layout: 'form',
      items: [{
			xtype: 'combo',
			fieldLabel: 'Geslacht',
			store: genderType,
			displayField: 'desc',
			valueField: 'num',
			name: 'General_Gender_Public',
			mode: 'local',
			editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
			forceSelection: true,
			triggerAction: 'all',
			selectOnFocus: true,
			lazyInit: false,		//Init items direct, niet op dropdown event
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Geboortedatum',
			name: 'General_BirthDate_Public',
			anchor: '100%'
		}]
	}]
  },{
	columnWidth:.5,
    title: 'Contactinformatie',
    layout:'column',
    items:[{
      columnWidth:.5,
      layout: 'form',
      items: [ {
			xtype:'textfield',
			fieldLabel: 'Straatnaam',
			name: 'Address_StreetName_Public',
			anchor: '100%'
		}, {
			xtype:'numberfield',
			fieldLabel: 'Huisnummer',
			name: 'Address_HouseNr_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Toevoeging',
			name: 'Address_Affix_Public',
			anchor: '100%'
		},{
			xtype:'textfield',
			fieldLabel: 'Postcode',
			name: 'Address_Zipcode_Public',
			anchor: '100%',
			readOnly: true
		}, {
			xtype:'textfield',
			fieldLabel: 'Woonplaats',
			name: 'Address_City_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Land',
			name: 'Address_Country_Public',
			anchor: '100%'
		}]
	},{
      columnWidth:.5,
      layout: 'form',
      items: [{
			xtype:'textfield',
			fieldLabel: 'Telefoon',
			name: 'General_Telephone_Public',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Email',
			name: 'General_EmailAddress_Public',
			anchor: '100%'
		}, {
			xtype:'numberfield',
			fieldLabel: 'Longitude',
			name: 'HomePosition_Longitude_Public',
			anchor: '100%',
			decimalPrecision: 9
		}, {
			xtype:'numberfield',
			fieldLabel: 'Latitude',
			name: 'HomePosition_Latitude_Public',
			anchor: '100%',
			decimalPrecision: 9
		}]
	}]
  },{
	columnWidth:.5,
    title: 'MyWheels Details',
    layout:'column',
    items:[{
      columnWidth:.5,
      layout: 'form',
      items: [ {
			xtype:'textfield',
			fieldLabel: 'Favoriete Auto',
			name: 'Settings_FavouriteCar_Protected',
			anchor: '100%'
		}, {
			xtype:'numberfield',
			fieldLabel: 'MyWheels PersoonNummer',
			name: 'Settings_W4APersoonNr_Private',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'RijbewijsNummer',
			name: 'General_DriverLicenceNr_Protected',
			anchor: '100%'
		}]
	},{
      columnWidth:.5,
      layout: 'form',
      items: [{
			xtype:'textfield',
			fieldLabel: 'Telefoon2',
			name: 'General_Telephone2_Protected',
			anchor: '100%'
		}, {
			xtype:'textfield',
			fieldLabel: 'Telefoon3',
			name: 'General_Telephone3_Protected',
			anchor: '100%'
		}]
	}]
  }]
		
});

var personContractReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
        {name: 'ContractNr'},
        {name: 'Soort'},
	]
});
var personContractStore =  new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	storeId: 'personContractStore',
	url: './modules/api/getContractsPerUser.php',
	reader: personContractReader
});

var personContractListView = new Ext.list.ListView({
    store: personContractStore,
    title: 'Contracten',
    multiSelect: false,
    singleSelect: true,
    emptyText: 'Geen contracten gevonden',
    reserveScrollOffset: true,
    loadingText: 'Gegevens laden',
    autoScroll: true,
    columns: [{
    	width: .35,
        header: 'Contract',
        dataIndex: 'ContractNr'
    },{
        header: 'Soort lid',
        dataIndex: 'Soort'
    }]
});

personContractListView.on('dblclick',function(dataview, index, node ,e){
	e.stopEvent();
	this.select(index);	//Selecteer regel (puur optisch)
	var contractNr = personContractListView.getSelectedRecords()[0].get("ContractNr");
	showContractDetails(contractNr);
});


var personChipReader = new Ext.data.JsonReader({
	idProperty: 'uid',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
        {name: 'uid'},
        {name: 'blocked'},
		{name: 'id', hiddenValue: true}
	]
});

var personChipWriter = new Ext.data.JsonWriter({
    encode: true,
    writeAllFields: true, // write all fields, not just those that changed
	listful: true,
	url: './modules/api/putMyMobilityChips.php'
});

var personChipStore =  new Ext.data.Store({
	autoLoad: false,
	autoSave: true,
	remoteSort : false,
	storeId: 'personContractStore',
	url: './modules/api/getMyMobilityChips.php',
	proxy: new Ext.data.HttpProxy({
	   method: 'POST',
	   prettyUrls: false,
	   url: './modules/api/getMyMobilityChips.php',
	   api: {
		  create: './modules/api/putMyMobilityChips.php',
		  update: './modules/api/putMyMobilityChips.php',
		  destroy: './modules/api/putMyMobilityChips.php'
	   }
	}),
	reader: personChipReader,
	writer: personChipWriter
});

var personChipListViewColumnModel = new Ext.grid.ColumnModel({
   defaults: {
	  sortable: true,
	  editable: true
   },
   columns: [{
		 id: 'uid',
		 header: 'Chip UID',
		 dataIndex: 'uid',
		 editor: new Ext.form.NumberField({
			allowBlank: false
		 })
	  },{
		 header: 'Geblokkeerd',
		 dataIndex: 'blocked',
		 editor: new Ext.form.NumberField({
			allowBlank: false
		 })
	  }]
});

var chipRecord = Ext.data.Record.create([
   {name: 'uid'},
   {name: 'blocked'}
]);

var personChipListView = new Ext.grid.EditorGridPanel({
    store: personChipStore,
	cm: personChipListViewColumnModel,
	bbar: [
	   {
		   iconCls : 'icon-disk',
		   text: 'Voeg Chip toe',
		   handler: function() {
			  personChipStore.add(new chipRecord({
				 uid: 0,
				 blocked: 1
			  }));
		   }
	   }
	],
	clicksToEdit: 1,
	title: 'Passen',
    multiSelect: false,
    singleSelect: true,
	autoHeight: true,
    emptyText: 'Geen passen gevonden',
    reserveScrollOffset: true,
    loadingText: 'Gegevens laden',
    autoScroll: true
});

personChipListView.on('rowclick', function(grid, rowIndex, e) {
   personChipStore.write();
})


var personChipsWindowBottomBar = new Ext.Toolbar({			//personDetailWindowBottomBar voor main window
   items : [
	'->', {
	    iconCls : 'icon-plus',
		text: 'Voeg Chip toe',
		handler: function() {
		   personChipStore.add();
		}

	},{
     	iconCls : 'icon-disk',
		text: 'Opslaan',
		handler: function(){
			personDetailForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/putMyMobilityPerson.php',
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
	}]
});

var personChipContractContainerPanel = new Ext.Panel({
   columnWidth: .3,
   id: 'personChipContractContainerPanel',
   //autoHeight: true,
   //layout: 'border',
   items: [{
			title: 'Contracten:',
			height: 220,
			region: 'north',
			items: personContractListView
		},personChipListView]
});

var personDetailWindow = new Ext.Window({
	title: 'Details persoon ',
	width: 800,
	autoHeight: true,
	closable: true,
	closeAction: 'hide',
	layout: 'column',
	iconCls: 'icon-user',
	items: [personDetailForm,personChipContractContainerPanel],
	bbar:	personDetailWindowBottomBar
});

var showPersonDetails = function(PersonId){
	personDetailWindow.show(this);
	personDetailId = PersonId;
	personDetailWindow.setTitle("Details persoon "+PersonId);
	personDetailForm.getForm().load({url:'./modules/api/getMyMobilityPerson.php', params: {PersonId : personDetailId}, waitMsg:'Laden...'});
	personChipStore.setBaseParam('userId',PersonId);
	personChipStore.load();
	personContractStore.setBaseParam('userId',PersonId);
	personContractStore.load();
};
