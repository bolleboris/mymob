var resPerAutoStoreReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'RitNr'},
		{name: 'PersoonNr'},
		{name: 'Persoon'},
		{name: 'ReserveringBegin'},
		{name: 'ReserveringEind'},
		{name: 'Status'}
	]
});

var resPerAutoStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	sortInfo: {
		field: 'ReserveringBegin',
		direction: 'DESC'
	},
	storeId: 'bezetting',
	url: './modules/api/getResPerAuto.php',
	reader: resPerAutoStoreReader
});

resPerAutoStore.on('exception',handleProxyException);
 
var resPerAutoWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
   items : [ 
		{
				text: 'Annuleren',
				iconCls: 'icon-cross',
				handler: function(){
					var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
					annuleerRit(ritNr,userId);
					resPerAutoStore.reload();
				}
			},{
				text: 'Opnieuw sturen',
				iconCls: 'icon-transmit',
				handler: function(){
					var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
					resendRes(ritNr);
				}
			},{
				text: 'Details',
				iconCls: 'icon-database_table',
				handler: function(){
					var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
					showRitDetails(ritNr);
				}
			},{
				text: 'Bekijk persoongegevens',
				iconCls: 'icon-user',
				handler: function(){
					var PersoonNr = resPerAutoGrid.getSelectionModel().getSelected().get("PersoonNr");
					showUserDetails(PersoonNr);
				}
			},{
				text: 'Open reserveersite',
				iconCls: 'icon-application_form',
				handler: function(){
					window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId='+resPerAutoStore.baseParams.AutoId,'Reserveren');
				}
			}
	]
});

var resPerAutoGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			text: 'Annuleren',
			iconCls: 'icon-cross',
			handler: function(){
				var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
				annuleerRit(ritNr,userId);
				resPerAutoStore.reload();
			}
		},{
			text: 'Opnieuw sturen',
			iconCls: 'icon-transmit',
			handler: function(){
				var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
				resendRes(ritNr);
			}
		},{
			text: 'Details',
			iconCls: 'icon-database_table',
			handler: function(){
				var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
				showRitDetails(ritNr);
			}
		},{
			text: 'Bekijk persoongegevens',
			iconCls: 'icon-user',
			handler: function(){
				var PersoonNr = resPerAutoGrid.getSelectionModel().getSelected().get("PersoonNr");
				showUserDetails(PersoonNr);
			}
		},{
			text: 'Open reserveersite',
			iconCls: 'icon-application_form',
			handler: function(){
				window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId='+resPerAutoStore.baseParams.AutoId,'Reserveren');
			}
		},{
			text: 'Wijzig reservering',
			iconCls: 'icon-wrench',
			disabled: true
		}
	]
});

/*var resPerAutoWindowBottomBar = new Ext.Toolbar({
	items: [
		'->',
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		})
	]
});*/

var resPerAutoWindowBottomBar = new Ext.PagingToolbar({
	afterPageText: 'van {0}',
	beforePageText: 'Pagina',
	firstText: 'Eerste pagina',
	lastText: 'Laatste pagina',
	nextText: 'Volgende pagina',
	prevText: 'Vorige pagina',
	refreshText: 'Vernieuw',
	store: resPerAutoStore,       // grid and PagingToolbar using same store
	displayInfo: true,
	pageSize: 15,
	prependButtons: true
});


var resPerAutoGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: resPerAutoStore,
	loadMask: {msg:'Bezig met laden..'},
	anchor: '100%',
	columns: [
		{id:'RitNr',header: "RitNr", width: 50, sortable: true, dataIndex: 'RitNr'},
		{header: "PersoonNr", width: 60, sortable: true, dataIndex: 'PersoonNr'},
		{header: "Persoon", width: 150, sortable: true, dataIndex: 'Persoon'},
		{header: "Begin", width: 75, sortable: true, dataIndex: 'ReserveringBegin'},
		{header: "Eind", width: 75, sortable: true, dataIndex: 'ReserveringEind'},
		{header: "Status", width: 160, sortable: true, dataIndex: 'Status'}
	],
	rowCtxMenu: resPerAutoGridCtxMenu
});

resPerAutoGrid.on('rowdblclick',function(grid, rowIdx, e){
	e.stopEvent();
	var ritNr = grid.getSelectionModel().getSelected().get("RitNr");
	showRitDetails(ritNr);
});

resPerAutoGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();									//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var resPerAutoWindow = new Ext.Window({
	title: 'Reserveringen',
	width: 600,
	height: 430,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: resPerAutoGrid,
	tbar: resPerAutoWindowTopBar,
	bbar: resPerAutoWindowBottomBar
});

var showResPerAuto = function(AutoId){
resPerAutoWindow.setTitle("Reserveringen van auto "+AutoId);
resPerAutoWindow.show();
resPerAutoStore.setBaseParam('AutoId',AutoId);
resPerAutoStore.load();	
};

