var contractLijstStore = new Ext.data.GroupingStore({
	url: './modules/api/getContracts.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

contractLijstStore.on('exception', handleProxyException);

var contractLijstWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
		{
			text: 'Wijzig contractgegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				showContractDetails(contractLijstGrid.getSelectionModel().getSelected().get("ContractNr"));
			}
		}, {
			text: 'Toon contracthouder',
			iconCls: 'icon-user',
			handler: function () {
				showUserDetails(contractLijstGrid.getSelectionModel().getSelected().get("Contractant"));
			}
		}/*, {
			text: 'Toon leden',
			iconCls: 'icon-group',
			disabled: true
		}*/
	]
});

var contractLijstGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			text: 'Wijzig contractgegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				showContractDetails(contractLijstGrid.getSelectionModel().getSelected().get("ContractNr"));
			}
		}, {
			text: 'Toon contracthouder',
			iconCls: 'icon-user',
			handler: function () {
				showUserDetails(contractLijstGrid.getSelectionModel().getSelected().get("Contractant"));
			}
		}/*, {
			text: 'Toon leden',
			iconCls: 'icon-group',
			disabled: true
		}*/
	]
});


var contractLijstWindowBottomBar = new Ext.Toolbar({
	items: [
		'->',
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function () {
				this.findParentByType('window').hide();
			}
		})
	]
});

var contractLijstGrid = new Ext.grid.GridPanel({
	frame: false,
	header: false,
	collapsible: true,
	animCollapse: true,
	loadMask: true,
	store: contractLijstStore,
	loadMask: {msg:'Bezig met laden..<br/>Dit kan best even duren'},
	anchor: '100%',
	columns: [
		{header: "Nr", width: 50, sortable: true, dataIndex: 'ContractNr'},
		{header: "Status", width: 75, sortable: true, dataIndex: 'Status'},
		{header: "Soort", width: 100, sortable: true, dataIndex: 'AbonnementSoort'},
		{header: "Graad", width: 75, sortable: true, dataIndex: 'AbonnementGraad'},
		{header: "Contractant", width: 175, sortable: true, dataIndex: 'ContractantNaam'}
	],
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	}),
	rowCtxMenu: contractLijstGridCtxMenu
});

contractLijstGrid.on('rowcontextmenu', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

contractLijstGrid.on('rowdblclick', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	showContractDetails(grid.getSelectionModel().getSelected().get("ContractNr"));
});


var contractLijstWindow = new Ext.Window({
	title: 'Contracten overzicht',
	width: 525,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-page_white_text',
	items: contractLijstGrid,
	tbar: contractLijstWindowTopBar,
	bbar: contractLijstWindowBottomBar
});
