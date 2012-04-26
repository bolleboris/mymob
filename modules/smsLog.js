var smsStatusStoreReader = new Ext.data.JsonReader({
	totalProperty: 'results',
	messageProperty: 'msg',
	successProperty: 'success',
	root: 'rows',
	fields: [
		{name: 'Type'},			
		{name: 'RitNr'},
		{name: 'AutoId'},
		{name: 'Bijnaam'},
		{name: 'ReserveringBegin'},
		{name: 'verstuurd'},
		{name: 'difference'}
	]
})

var smsStatusStore = new Ext.data.Store({
	storeId: 'smsStatusStore',
	url: './modules/api/getSMSDelays.php',
	autoLoad: false,
	autoSave: false,
	remoteSort : false,
	autoDestroy : true,
	pruneModifiedRecords: true,		//Cache de records niet, we willen dat een melding verdwijnd als deze niet meer in de resultset van het PHP script voorkomt
	restful: true,
	reader: smsStatusStoreReader
});
 
smsStatusStore.on('exception',handleProxyException);

var smsStatusWindowBBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
			'Dit venster vernieuwd automatisch elke 10 seconden'
	]
});

var smsStatusWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
  items : [
			{
				text: 'Toon details van rit',
				iconCls: 'icon-database_table',
				handler: function(){
					showRitDetails(smsStatusGrid.getSelectionModel().getSelected().get("RitNr"));
				}
			},{
				text: 'Toon autogegevens',
				iconCls: 'icon-wrench',
				handler: function(){
					showCarDetails(smsStatusGrid.getSelectionModel().getSelected().get("AutoId"));
				}
			},{
   			text: 'Toon auto waarschuwingen',
	   		iconCls: 'icon-error',
	   		handler: function(){
   				showCarAlarms(smsStatusGrid.getSelectionModel().getSelected().get("AutoId"));
   			}
   		}
	]
});

var smsStatusGrid = new Ext.grid.GridPanel({
	store: smsStatusStore,
	frame: false,
	stripeRows: true,
	header: false,
	anchor: '100%',
	//loadMask: {msg:'Bezig met laden..'},
	maskDisabled: false,
	columns: [
		{header: "RitNr", width: 60, sortable: true, dataIndex: 'RitNr'},
		{header: "AutoId", width: 40, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam'},
		{header: "Type", width: 75, sortable: true, dataIndex: 'Type'},
		{header: "Begin reservering", width: 100, sortable: true, dataIndex: 'ReserveringBegin'},
		{header: "Verstuurd", width: 100, sortable: true, dataIndex: 'verstuurd'},
		{header: "Vertraging", width: 100, sortable: true, dataIndex: 'difference'}
	],
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	}),
	rowCtxMenu: new Ext.menu.Menu({
		items: [
			{
				text: 'Toon details van rit',
				iconCls: 'icon-database_table',
				handler: function(){
					showRitDetails(smsStatusGrid.getSelectionModel().getSelected().get("RitNr"));
				}
			},{
				text: 'Toon autogegevens',
				iconCls: 'icon-wrench',
				handler: function(){
					showCarDetails(smsStatusGrid.getSelectionModel().getSelected().get("AutoId"));
				}
			},{
   			text: 'Toon auto waarschuwingen',
	   		iconCls: 'icon-error',
	   		handler: function(){
   				showCarAlarms(smsStatusGrid.getSelectionModel().getSelected().get("AutoId"));
   			}
   		}
   	]
	})
});

smsStatusGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var smsStatusWindow = new Ext.Window({
	title: 'Status SMS berichten',
	width: 630,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-mail',
	items: smsStatusGrid,
	bbar: smsStatusWindowBBar,
	tbar: smsStatusWindowTopBar
});

var AutoRefreshSMSStatusWindow = {
	run: function(){
		if(smsStatusWindow.isVisible()){
			smsStatusStore.removeAll(true);	//Leeg de cache van de store, en geeft geen event
			smsStatusStore.reload();
			//smsStatusStore.load({add:false});
		}
	},
	interval: 10000 //10 second
}
Ext.TaskMgr.start(AutoRefreshSMSStatusWindow);
