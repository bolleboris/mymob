var OfferSoortStore = new Ext.data.GroupingStore({
	url: './modules/api/getOfferSoorten.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

OfferSoortStore.on('exception', handleProxyException);

var OfferLedenStore = new Ext.data.GroupingStore({
	url: './modules/api/getOfferMembers.php',
	reader : new Ext.data.JsonReader(),			//Reader configureerd zichzelf met metaData in JSON string
	writer : new Ext.data.JsonWriter({
	   writeAllFields: true,
	   listful: true
	}),
    proxy: new Ext.data.HttpProxy({
	   method: 'POST',
	   prettyUrls: false,
	   url: './modules/api/getOfferMembers.php',
	   api: {
		  create: './modules/api/putOfferMembers.php', // @todo
		  update: './modules/api/putOfferMembers.php', // @todo
		  destroy: './modules/api/deleteOfferMember.php'
	   }
	})
});

OfferLedenStore.on('exception', handleProxyException);

OfferLedenStore.on('add', function(store, records, index) {
   console.log(store);
   OfferLedenStore.save();
   OfferLedenStore.reload();
});

OfferLedenStore.on('beforewrite', function(store, action, rs, options, arg) {
   console.log(action);
   if(action === 'destroy') {
	  Ext.each(rs, function(record, index) {
		 record.id = record.json;
	  });
   }
});

var OfferStatusStore = new Ext.data.ArrayStore({
	fields: ['num', 'desc'],
	idIndex: 0, // id for each record will be the first element
	data : [
		['Requested', 'Aangevraagd'],
		['Accepted', 'Geaccepteerd'],
		['Released', 'Vrijgegeven'],
		['Block', 'Geblokkeerd'],
		['Canceled', 'Geannuleerd'],
		['Rejected', 'Geweigerd'],
		['Ended', 'Opgezegd'],
	]
});

var OfferResourceRecord = Ext.data.Record.create([
   {name: 'AutoId'},
   {name: 'Merk'},
   {name: 'Bijnaam'},
   {name: 'Kenteken'},
   {name: 'Plaats'},
   {name: 'Straat'},
   {name: 'OfferNr'}
]);

var OfferLedenTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [
		{
			text: 'Bekijk persoongegevens',
			iconCls: 'icon-user',
			handler: function(){
				showPersonDetails(OfferLedenGrid.getSelectionModel().getSelected().get("PersoonNr"));
			}
		}, {
		   text: 'Voeg Medelid toe aan Offer',
		   iconCls: 'icon-user-add',
		   handler: function() {
			  showUserSearchBox(function(BMUNr, W4ANr) {
				  console.log('Callback succeeded');
				  OfferLedenStore.add(new OfferResourceRecord({
					PersoonNr: BMUNr,
					Naam: 'Wordt opgehaald',
					Email: 'Wordt opgehaald',
					Telefoon1: 'Wordt opgehaald',
					Type: 'Medelid',
					OfferNr: OfferDetailWindowNumber
				  }));

			  });
		   }
		}, {
		   text: 'Verwijder Medelid',
		   iconCls: 'icon-user',
		   handler: function() {
			  console.log(OfferLedenGrid.getSelectionModel().getSelected());
			  OfferLedenStore.remove(OfferLedenGrid.getSelectionModel().getSelected());
		   }
		}
	]
});

var OfferDetailWindowBottomBar = new Ext.Toolbar({
items : [
	'->',
	/*{
	//	iconCls : 'icon-arrow_refresh',
		iconCls : 'icon-arrow_undo',
		text: 'Herstel',
		handler: function(){
			Ext.Msg.show({
			   title:'Ongedaan maken',
			   msg: 'Weet je zeker dat je je wijzigingen ongedaan wilt maken?',
			   buttons: Ext.Msg.YESNO,
			   fn: function(e){
//					if(e == 'yes') 	OfferDetailForm.getForm().reload();
					var OfferNr = OfferDetailForm.getForm().getValues().OfferNr;
					if(e == 'yes') OfferDetailForm.getForm().load({
						url:'./modules/api/getOffer.php',
						params: {
							OfferNr: OfferDetailForm.getForm().getValues().OfferNr},
							waitMsg:'Laden...'
						}
					);
				},
			   animEl: 'header',
			   icon: Ext.MessageBox.QUESTION,
			   scope:this
			})
		}
	},'-',*/{
     	iconCls : 'icon-disk',
		text: 'Opslaan',
		handler: function(){
			OfferDetailForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/putOffer.php',
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
	}
	]
});

var OfferDetailForm = new Ext.FormPanel({
	width: 240,
	id: 'OfferDetailForm',
	reader: new Ext.data.JsonReader(),			//Reader configureerd zichzelf met metaData in JSON string,
    fieldDefaults: {
        msgTarget: 'side',
        labelWidth: 100
    },
    defaultType: 'textfield',
    defaults: {
        anchor: '100%'
    },
	items: [{
        fieldLabel: 'OfferNr',
        name: 'OfferNr',
	dataIndex: 'OfferNr',
        readOnly: true
    },{
        fieldLabel: 'Verantwoordelijke',
        name: 'Verantwoordelijke',
        readOnly: true
    },{
		xtype: 'combo',
		fieldLabel: 'Status',
		store: OfferStatusStore,
		displayField: 'desc',
		valueField: 'num',
		hiddenName: 'Status',		//Create hiddenname so that extjs sends the valuefield
		name: 'Status',
		emptyText:'Kies status...',
		mode: 'local',
		editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
		forceSelection: true,
		triggerAction: 'all',
		selectOnFocus: true,
		lazyInit: false,		//Init items direct, niet op dropdown event
		anchor: '100%'
	}, {
		xtype: 'combo',
		fieldLabel: 'Soort',
		store: OfferSoortStore,
		displayField: 'Omschrijving',
		valueField: 'OfferSoort',
		hiddenName: 'OfferSoort',		//Create hiddenname so that extjs sends the valuefield
		name: 'OfferSoort',
		emptyText:'Kies soort...',
		mode: 'local',
		editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
		forceSelection: true,
		triggerAction: 'all',
		selectOnFocus: true,
		lazyInit: false,		//Init items direct, niet op dropdown event
		anchor: '100%',
        readOnly: true
    }]
});

var OfferLedenGrid = new Ext.grid.GridPanel({
	frame:false,
//	width: 350,
	height: 390,
	title: 'Resources',
	store: OfferLedenStore,
	loadMask: {msg:'Bezig met laden..'},
	anchor: '100%',
	tbar: OfferLedenTopBar,
	columns: [
		{header: "AutoId", width: 60, sortable: true, dataIndex: 'AutoId'},
		{header: "Merk", width: 100, sortable: true, dataIndex: 'Merk'},
		{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 70, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Plaats", width: 80, sortable: true, dataIndex: 'Plaats'},
		{header: "Straat", width: 100, sortable: true, dataIndex: 'Straat', groupable: false},
		{header: "Supplier", width: 100, sortable: true, dataIndex: 'Supplier'}
	],
	view: new Ext.grid.GroupingView({
		forceFit: true,
		startCollapsed: true,
		columnsText: 'Toon kolommen',
		groupByText: 'Groepeer op dit veld',
		showGroupsText: 'Groepeer',
		sortAscText: 'Sorteer oplopend',
		sortDescText: 'Sorteer aflopend',
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Autos" : "Auto"]})'
	}),
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	})
});

var OfferDetailWindow = new Ext.Window({
	title: 'Offer',
	width: 800,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'column',
	iconCls: 'icon-page_white_text',
	items: [
		{
			title: 'Offer details',
			width: 250,
			height: 390,
			collapsible: false,
			id: 'OfferDetailPanel',
			items: OfferDetailForm
		},
		OfferLedenGrid
	],
	//items: OfferDetailForm,
	bbar: OfferDetailWindowBottomBar
});

var showOfferDetails = function (OfferNr, VerschafferNr) {
	OfferDetailWindow.show(this);
	OfferDetailWindow.setTitle("Offer " + OfferNr + " details");
	OfferDetailWindowNumber = OfferNr;
	OfferSoortStore.load();

	OfferLedenStore.load({params: {offer_id : OfferNr, supplier_id: VerschafferNr}, waitMsg:'Laden...'});
	OfferDetailForm.getForm().load({url:'./modules/api/getOffer.php', params: {OfferNr : OfferNr}, waitMsg:'Laden...'});
};
