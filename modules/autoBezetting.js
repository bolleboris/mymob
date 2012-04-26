var autoBezettingStoreReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'AutoId'},
		{name: 'Kenteken'},
		{name: 'Bijnaam'},
		{name: 'Plaats'},
		{name: 'Uren'},
		{name: 'Bezetting'}
	]
});

var autoBezettingHelp = new Ext.Window({
	title: 'Autobezetting Help',
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
			html: "<h1>Auto bezetting overzicht</h1><br><p>Met deze module kan je de bezetting per auto zien.</p><br>" +
				"<p>Dit betreft het aantal <b>gereserveerde</b> uren tussen 07:00 en 23:00 uur van de afgelopen 30 dagen.</p><br>" +
				"<p>Het gemiddelde is genomen over 10 uur per dag. Omdat er meer dan 10 factureerbare uren per dag zijn, kan het percentage daardoor oplopen tot meer dan 100%.</p><br>"
		}
	]
});	

var autoBezettingStore = new Ext.data.GroupingStore({
	autoLoad: false,
	remoteSort : false,
	storeId: 'bezetting',
	url: './modules/api/getBezetting.php',		//Let op, path is relatief vanaf plaats waar deze file wordt geinclude.
	reader: autoBezettingStoreReader
});

autoBezettingStore.on('exception', handleProxyException);


var autoBezettingWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [ 
		{
			id: 'autoBezettingWindowTopBarWijzigButton',
			text: 'Wijzig autogegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				showCarDetails(autoBezettingGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function () {
				showResPerAuto(autoBezettingGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		}, {
			iconCls : 'icon-arrow_refresh',
			text: 'Vernieuw',
			handler: function () {
				autoBezettingStore.reload();
			}
		}, '->', {
			iconCls : 'icon-help',
			text: 'Help',
			handler: function () {
				autoBezettingHelp.show(this);
			}
		}
	]
});

var autoBezettingSortBox = new Ext.form.Checkbox({
	boxLabel: 'Groepeer per plaats',
	checked : false
});

autoBezettingSortBox.on('check', function (box, value) {
	if (value === true) {
		autoBezettingStore.groupBy('Plaats', true);
	} else {
		autoBezettingStore.clearGrouping();
	}
});

autoBezettingExportButton = new Ext.Button({
	iconCls : 'icon-excel',
	text: 'Exporteer excel bestand',
	handler: function () {
		var url = 'https://www.wheels4all.nl/backoffice/modules/api/getBezettingXLS.php'; 
		window.open(url);
	}
});

var autoBezettingWindowBottomBar = new Ext.Toolbar({
	items: [
		autoBezettingSortBox,
		'->',
		autoBezettingExportButton,
		'-',
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function () {
				this.findParentByType('window').hide();
			}
		})
	]
});



var autoBezezttingGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			id: 'autoBezezttingGridCtxMenuWijzigButton',
			text: 'Wijzig autogegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				showCarDetails(autoBezettingGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Toon reserveringen',
			iconCls: 'icon-database_table',
			handler: function () {
				showResPerAuto(autoBezettingGrid.getSelectionModel().getSelected().get("AutoId"));
			}
		}, {
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		}
	]
});

var autoBezettingGrid = new Ext.grid.GridPanel({
	frame: false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: autoBezettingStore,
	loadMask: {msg: 'Bezig met laden..'},
	anchor: '100%',
	columns: [
		{id: 'AutoId', header: "AutoId", width: 30, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 100, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 60, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Plaats", width: 100, sortable: true, dataIndex: 'Plaats'},
		{header: "Uren", width: 30, sortable: true, dataIndex: 'Uren', groupable: false},
		{header: "Bezetting (%)", width: 50, sortable: true, dataIndex: 'Bezetting', groupable: false}
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
	rowCtxMenu: autoBezezttingGridCtxMenu
});

autoBezettingGrid.on('rowcontextmenu', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();									//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var autoBezettingWindow = new Ext.Window({
	title: 'Overzicht autobezetting (adv reserveringen)',
	width: 700,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: autoBezettingGrid,
	tbar: autoBezettingWindowTopBar,
	bbar: autoBezettingWindowBottomBar
});
