var autoStore = new Ext.data.GroupingStore({
	url: './modules/api/getCars.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

autoStore.on('exception', handleProxyException);

var autoLijstWindowHelp = new Ext.Window({
	title: 'Autogegevens Help',
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
			html: "<h1>Auto overzicht</h1><br><p>Met deze module kan je alle auto's zien.</p><br>" +
				"<p>Selecteerd een auto en kies een van de opties bovenaan in het menu, of gebruik de rechter muisknop.</p><br>" +
				"<p>Onderaan het venster staat een checkbox waarmee je auto's kunt groeperen per plaats.</p><br>"
		}
	]
});

var autoLijstWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
		{
			text: 'Wijzig autogegevens',
			id: 'autoLijstWindowTopBarWijzigButton',
			iconCls: 'icon-wrench',
			handler: function () {
				showCarDetails(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function () {
				showResPerAuto(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		}, {
			text: 'Toon auto waarschuwingen',
			iconCls: 'icon-error',
			handler: function () {
				showCarAlarms(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, 
		'->',
		{
			iconCls : 'icon-help',
			text: 'Help',
			handler: function () {
				autoLijstWindowHelp.show(this);
			}
		}
	]
});

var autoLijstGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			id: 'autoLijstGridCtxMenuWijzigButton',
			text: 'Wijzig autogegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				showCarDetails(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function () {
				showResPerAuto(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		}, {
			text: 'Toon auto waarschuwingen',
			iconCls: 'icon-error',
			handler: function () {
				showCarAlarms(autoLijstGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Open reserveersite',
			iconCls: 'icon-application_form',
			handler: function () {
				window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId=' + autoLijstGrid.getSelectionModel().getSelected().get("AutoId"), 'Reserveren');
			}
		}
	]
});

var autoLijstWindowSortBox = new Ext.form.Checkbox({
	boxLabel: 'Groepeer per plaats',
	checked : false
});

autoLijstWindowSortBox.on('check', function (box, value) {
	if (value === true) {
		autoStore.groupBy('Plaats', true);
	} else {
		autoStore.clearGrouping();
	}
});

var autoLijstWindowBottomBar = new Ext.Toolbar({
	items: [
		autoLijstWindowSortBox,
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

var autoLijstGrid = new Ext.grid.GridPanel({
	frame: false,
	header: false,
	collapsible: true,
	animCollapse: true,
	loadMask: true,
	store: autoStore,
	anchor: '100%',
	columns: [
		{header: "AutoId", width: 60, sortable: true, dataIndex: 'AutoId'},
		{header: "Merk", width: 100, sortable: true, dataIndex: 'Merk'},
		{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 70, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Plaats", width: 80, sortable: true, dataIndex: 'Plaats'},
		{header: "Straat", width: 100, sortable: true, dataIndex: 'Straat', groupable: false},
		{header: "Supplier", width: 100, sortable: true, dataIndex: 'SupplierId'}
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
	}),
	rowCtxMenu: autoLijstGridCtxMenu
});

autoLijstGrid.on('rowcontextmenu', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var autoLijstWindow = new Ext.Window({
	title: 'Auto overzicht',
	width: 700,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: autoLijstGrid,
	tbar: autoLijstWindowTopBar,
	bbar: autoLijstWindowBottomBar
});
