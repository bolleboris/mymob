var contractStore = new Ext.data.GroupingStore({
	url: './modules/api/getCars.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

contractStore.on('exception', handleProxyException);

var contractSoortStore = new Ext.data.GroupingStore({
	url: './modules/api/getContractSoorten.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

contractSoortStore.on('exception', handleProxyException);

var contractLedenStore = new Ext.data.GroupingStore({
	url: './modules/api/getContractMembers.php',
	reader : new Ext.data.JsonReader(),			//Reader configureerd zichzelf met metaData in JSON string
	writer : new Ext.data.JsonWriter({
	   writeAllFields: true,
	   listful: true
	}),
    proxy: new Ext.data.HttpProxy({
	   method: 'POST',
	   prettyUrls: false,
	   url: './modules/api/getContractMembers.php',
	   api: {
		  create: './modules/api/putContractMembers.php',
		  update: './modules/api/putContractMembers.php',
		  destroy: './modules/api/deleteContractMember.php'
	   }
	})
});

contractLedenStore.on('exception', handleProxyException);

contractLedenStore.on('add', function(store, records, index) {
   console.log(store);
   contractLedenStore.save();
   contractLedenStore.reload();
});

contractLedenStore.on('beforewrite', function(store, action, rs, options, arg) {
   console.log(action);
   if(action === 'destroy') {
	  Ext.each(rs, function(record, index) {
		 record.id = record.json;
	  });
   }
});

var contractStatusStore = new Ext.data.ArrayStore({
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

var contractGraadStore = new Ext.data.ArrayStore({
	fields: ['desc'],
	//idIndex: 0, // id for each record will be the first element
	data : [
		['Vol'],
		['Half'],
		['Gratis'],
		['Stop']		
	]
});

var ContractPersonRecord = Ext.data.Record.create([
   {name: 'PersoonNr'},
   {name: 'Naam'},
   {name: 'Email'},
   {name: 'Telefoon1'},
   {name: 'Type'},
   {name: 'ContractNr'}
]);

var contractLedenTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
		{
			text: 'Bekijk persoongegevens',
			iconCls: 'icon-user',
			handler: function(){
				showPersonDetails(contractLedenGrid.getSelectionModel().getSelected().get("PersoonNr"));
			}
		}, {
		   text: 'Voeg Medelid toe aan contract',
		   iconCls: 'icon-user-add',
		   handler: function() {
			  showUserSearchBox(function(BMUNr, W4ANr) {
				  console.log('Callback succeeded');
				  contractLedenStore.add(new ContractPersonRecord({
					PersoonNr: BMUNr,
					Naam: 'Wordt opgehaald',
					Email: 'Wordt opgehaald',
					Telefoon1: 'Wordt opgehaald',
					Type: 'Medelid',
					ContractNr: contractDetailWindowNumber
				  }));
				  
			  });
		   }
		}, {
		   text: 'Verwijder Medelid',
		   iconCls: 'icon-user',
		   handler: function() {
			  console.log(contractLedenGrid.getSelectionModel().getSelected());
			  contractLedenStore.remove(contractLedenGrid.getSelectionModel().getSelected());
		   }
		}
	]
});

var contractDetailWindowBottomBar = new Ext.Toolbar({
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
//					if(e == 'yes') 	contractDetailForm.getForm().reload();
					var ContractNr = contractDetailForm.getForm().getValues().ContractNr;
					if(e == 'yes') contractDetailForm.getForm().load({
						url:'./modules/api/getContract.php', 
						params: {
							ContractNr: contractDetailForm.getForm().getValues().ContractNr}, 
							waitMsg:'Laden...'
						}
					);
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
			contractDetailForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/putContract.php',
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

var contractDetailForm = new Ext.FormPanel({
	width: 240,	
	id: 'contractDetailForm',
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
        fieldLabel: 'ContractNr',
        name: 'ContractNr',
		dataIndex: 'ContractNr',
        readOnly: true
    },{
        fieldLabel: 'Verantwoordelijke',
        name: 'Verantwoordelijke',
        readOnly: true
    },{
		xtype: 'combo',
		fieldLabel: 'Status',
		store: contractStatusStore,
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
		store: contractSoortStore,
		displayField: 'Omschrijving',
		valueField: 'AbonnementSoort',
		hiddenName: 'AbonnementSoort',		//Create hiddenname so that extjs sends the valuefield 
		name: 'AbonnementSoort',
		emptyText:'Kies soort...',
		mode: 'local',
		editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
		forceSelection: true,
		triggerAction: 'all',
		selectOnFocus: true,
		lazyInit: false,		//Init items direct, niet op dropdown event
		anchor: '100%',
        readOnly: true
    }/*, {
		xtype: 'combo',
		fieldLabel: 'Graad',
		name: 'AbonnementGraad',	
		store: contractGraadStore,
		displayField: 'desc',
		valueField: 'desc',
		mode: 'local',
		emptyText:'Kies graad...',
		editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
		forceSelection: true,
		triggerAction: 'all',
		selectOnFocus: true,
		lazyInit: false,		//Init items direct, niet op dropdown event
		anchor: '100%',
        readOnly: true
    }*/]
});

var contractLedenGrid = new Ext.grid.GridPanel({
	frame:false,
//	width: 350,
	height: 390,
	title: 'leden',
	store: contractLedenStore,
	loadMask: {msg:'Bezig met laden..'},
	anchor: '100%',
	tbar: contractLedenTopBar,
	columns: [
		{header: "Persoon", width: 50, sortable: true, dataIndex: 'PersoonNr'},
		{header: "Naam", width: 125, sortable: true, dataIndex: 'Naam'},
		{header: "Email", width: 150, sortable: true, dataIndex: 'Email'},
		{header: "Telefoon", width: 100, sortable: true, dataIndex: 'Telefoon1'},
		{header: "Soort", width: 100, sortable: true, dataIndex: 'Type'}
	]
});

var contractDetailWindow = new Ext.Window({
	title: 'Contract',
	width: 800,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'column',
	iconCls: 'icon-page_white_text',
	items: [
		{
			title: 'Contract details',
			width: 250,
			height: 390,
			collapsible: false,
			id: 'contractDetailPanel',
			items: contractDetailForm
		},
		contractLedenGrid
	],
	//items: contractDetailForm,
	bbar: contractDetailWindowBottomBar
});
var contractDetailWindowNumber;
var showContractDetails = function(ContractNr){
	contractDetailWindow.show(this);
	contractDetailWindow.setTitle("Contract "+ContractNr);
	contractDetailWindowNumber = ContractNr;
	contractSoortStore.load();

	contractLedenStore.load({params: {ContractNr : ContractNr}, waitMsg:'Laden...'});
	contractDetailForm.getForm().load({url:'./modules/api/getContract.php', params: {ContractNr : ContractNr}, waitMsg:'Laden...'});
};
