/*
Wijziging 2011-01-20: Leon
- Colom LaatsteRit is veranderd in LaatsteInstap
- Rowcontext menu toegevoegd
- topbar knoppen toegevoegd
*/
var stilstaandeAutosStoreReader = new Ext.data.JsonReader({
	idProperty: 'AutoId',
	totalProperty: 'results',
	messageProperty: 'msg',
	successProperty: 'success',
	root: 'rows',
	fields: [
		{name: 'AutoId'},
		{name: 'Bijnaam'},
		{name: 'Plaats'},
		{name: 'LaatsteInstap'},
		{name: 'Geparkeerd'}
	]
});

var stilstaandeAutosStore = new Ext.data.Store({
	storeId: 'stilstaandeAutosStore',
	url: './modules/api/getStilstaandeAutos.php',
	autoLoad: false,
	autoSave: false,
	remoteSort : false,
	reader: stilstaandeAutosStoreReader
});

stilstaandeAutosStore.on('exception',handleProxyException);

var stilstaandeAutosWindowBottomBar = new Ext.Toolbar({			//Topbar voor main window
	items : [
		"<b>*Let op:</b> De \"Geparkeerd\" tijd is vanaf het eind van de <b>reservering</b> (dus mogelijk staat de auto al langer geparkeerd)",
		'->',
		{
			iconCls : 'icon-arrow_refresh',
			text: 'Vernieuw',
			handler: function(){
				stilstaandeAutosStore.reload();
			}
		},
		{
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		}
	]
});

var stilstaandeAutosWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
		{
			id: 'stilstaandeAutosWindowTopBarWijzigButton',
			text: 'Wijzig autogegevens',
			iconCls: 'icon-wrench',
			handler: function(){
				showCarDetails(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		},{
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function(){
				showResPerAuto(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		},{
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		},{
			text: 'Toon auto waarschuwingen',
			iconCls: 'icon-error',
			handler: function(){
				showCarAlarms(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}
	]
});

var stilstaandeAutosGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			id: 'stilstaandeAutosGridCtxMenuWijzigButton',
			text: 'Wijzig autogegevens',
			iconCls: 'icon-wrench',
			handler: function(){
				showCarDetails(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		},{
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function(){
				showResPerAuto(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		},{
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		},{
			text: 'Toon auto waarschuwingen',
			iconCls: 'icon-error',
			handler: function(){
				showCarAlarms(stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		},{
			text: 'Open reserveersite',
			iconCls: 'icon-application_form',
			handler: function(){
				window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId='+stilstaandeAutosGrid.getSelectionModel().getSelected().get("AutoId"),'Reserveren');
			}
		}
	]
});

var stilstaandeAutosGrid = new Ext.grid.GridPanel({
	store: stilstaandeAutosStore,
	frame: false,
	header: false,
	anchor: '100%',
	loadMask: {msg:'Bezig met laden..'},
	columns: [
		{id:'AutoId', header: "AutoId", width: 50, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 150, sortable: true, dataIndex: 'Bijnaam'},
		{header: "Plaats", width: 150, sortable: true, dataIndex: 'Plaats'},
		{header: "Instap moment", width: 120, sortable: true, dataIndex: 'LaatsteInstap'},
		{header: "Einde reservering", width: 120, sortable: true, dataIndex: 'ReserveringEind'},
		{header: "Geparkeerd*", width: 170, sortable: true, dataIndex: 'Geparkeerd'}
	],
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	}),
	rowCtxMenu: stilstaandeAutosGridCtxMenu
});
	
stilstaandeAutosGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();									// Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	// Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		// Render ons eigen menu
});

var stilstaandeAutosWindow = new Ext.Window({
	title: 'Lang geparkeerde autos (met boordcomputer)',
	width: 800,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car_stop',
	items: stilstaandeAutosGrid,
	tbar: stilstaandeAutosWindowTopBar,
	bbar: stilstaandeAutosWindowBottomBar
});
