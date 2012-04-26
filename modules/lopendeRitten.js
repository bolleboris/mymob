var lopendeRittenStoreReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'RitNr'},
		{name: 'AutoId'},
		{name: 'Kenteken'},
		{name: 'Bijnaam'},
		{name: 'Plaats'},
		{name: 'PersoonNr'},
		{name: 'UserName'},
		{name: 'ReserveringBegin'},
		{name: 'ReserveringEind'},
		{name: 'Status'}
	]
})

var lopendeRittenStore = new Ext.data.GroupingStore({
	autoLoad: false,
	remoteSort : false,
	storeId: 'bezetting',
	url: './modules/api/getLopendeRitten.php',
	reader: lopendeRittenStoreReader
});

lopendeRittenStore.on('exception',handleProxyException);

var lopendeRittenWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
  items : [ 
  	{
			text: 'Toon reservering details',
			iconCls: 'icon-database_table',
			handler: function(){
				var ritNr = lopendeRittenGrid.getSelectionModel().getSelected().get("RitNr");
				showRitDetails(ritNr);
			}
	},{
			text: 'Wijzig reservering',
			iconCls: 'icon-wrench',
			disabled: true
  	},{
  			text: 'Stuur reservering opnieuw',
			iconCls: 'icon-transmit',
			handler: function(){
				var ritNr = lopendeRittenGrid.getSelectionModel().getSelected().get("RitNr");
				resendRes(ritNr);
			}
	},{
			text: 'Toon waarschuwingen',
   		iconCls: 'icon-error',
   		handler: function(){
				showCarAlarms(lopendeRittenGrid.getSelectionModel().getSelected().get("AutoId"));
			}
   },'->',{
        iconCls : 'icon-help',
			text: 'Help',
			handler: function(){ helpWindowbezetting.show(this); }
		}
	]
});

var lopendeRittenWindowSortBox = new Ext.form.Checkbox({
	boxLabel: 'Groepeer per plaats',
	checked : false
});

lopendeRittenWindowSortBox.on('check',function(box, value){
	if(value == true){
		lopendeRittenStore.groupBy('Plaats',true);
	}else{
		lopendeRittenStore.clearGrouping();
	}
});

var lopendeRittenWindowBottomBar = new Ext.Toolbar({
	items: [
		lopendeRittenWindowSortBox,
		'->',
		new Ext.Button({
		   iconCls : 'icon-arrow_refresh',
			text: 'Vernieuw',
			handler: function(){
				lopendeRittenStore.reload();
			}
  		}),
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		})
	]
});

var lopendeRittenGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: lopendeRittenStore,
	loadMask: {msg:'Bezig met laden..'},
	anchor: '100%',
	columns: [
		{id:'RitNr',header: "RitNr", width: 35, sortable: true, dataIndex: 'RitNr'},
		{header: "Auto", width: 25, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 100, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 40, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Plaats", width: 55, sortable: true, dataIndex: 'Plaats'},
		{header: "PersoonNr", width: 30, sortable: true, dataIndex: 'PersoonNr'},
		{header: "Persoon", width: 75, sortable: true, dataIndex: 'UserName'},
		{header: "Begin", width: 75, sortable: true, dataIndex: 'ReserveringBegin', groupable: false},
		{header: "Eind", width: 75, sortable: true, dataIndex: 'ReserveringEind', groupable: false},
		{header: "Status", width: 75, sortable: true, dataIndex: 'Status', groupable: false}
	],
	view: new Ext.grid.GroupingView({
		forceFit:true,
		startCollapsed: true,
		columnsText: 'Toon kolommen',
		groupByText: 'Groepeer op dit veld',
		showGroupsText: 'Groepeer',
		sortAscText: 'Sorteer oplopend',
		sortDescText: 'Sorteer aflopend',
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Lopende ritten" : "Lopende rit"]})'
	}),
	rowCtxMenu: new Ext.menu.Menu({
		items: [
			{
				text: 'Toon reservering details',
				iconCls: 'icon-database_table',
				handler: function(){
					var ritNr = lopendeRittenGrid.getSelectionModel().getSelected().get("RitNr");
					showRitDetails(ritNr);
				}
			},{
				text: 'Toon gebruiker details',
				iconCls: 'icon-user',
				handler: function(){
					var userId = lopendeRittenGrid.getSelectionModel().getSelected().get("PersoonNr");
					showUserDetails(userId);
				}
			},{
				text: 'Wijzig reservering',
				iconCls: 'icon-wrench',
				disabled: true
			},{
				text: 'Stuur reservering opnieuw',
				iconCls: 'icon-transmit',
				handler: function(){
					var ritNr = lopendeRittenGrid.getSelectionModel().getSelected().get("RitNr");
					resendRes(ritNr);
				}
			},{
				text: 'Toon waarschuwingen',
				iconCls: 'icon-error',
				handler: function(){
					showCarAlarms(lopendeRittenGrid.getSelectionModel().getSelected().get("AutoId"));
				}
   		}
		]
	})
});

lopendeRittenGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();									//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick	
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var lopendeRittenWindow = new Ext.Window({
	title: 'Overzicht lopende reserveringen',
	width: 1000,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: lopendeRittenGrid,
	tbar: lopendeRittenWindowTopBar,
	bbar: lopendeRittenWindowBottomBar
});

var helpWindowbezetting = new Ext.Window({
	title: 'Lopende reserveringen Help',
	width: 400,
	height: 250,
	autoScroll: true,
	closeAction: 'hide',
	layout: 'border',
	items: [
		{
			region: 'center',
			bodyStyle: {
				background: '#ffffff',
				padding: '7px'
			},
			html: "<h1>Lopende reserveringen</h1><br><p>Met deze module ze je alle lopende reserveringen. (Begintijd eerder dan nu en eindtijd later dan nu).</p><br>"+
			"<p>Je kunt op elke kolom sorteren door op de naam te klikken.</p><br>"+
			"<p>Je kunt kolommen toevoegen en verbergen door op het pijltje te klikken, welke naast de kolomnaam verschijnt als je je muis erop houd.</p><br>"
		}
	]
});	
