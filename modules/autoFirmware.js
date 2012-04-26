var autoFWStoreReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'AutoId'},
		{name: 'Bijnaam'},
		{name: 'Kenteken'},
		{name: 'Plaats'},
		{name: 'FW'},
		{name: 'TelNr'}
	]
})

//Ext.Ajax.timeout = 120000;		//Timeout in ms (2 minuten)
Ext.Ajax.timeout = 240000;		//Timeout in ms (4 minuten)

var autoFWStore = new Ext.data.GroupingStore({
	autoLoad: false,
	remoteSort : false,
	storeId: 'autoFW',
	url: './modules/api/getCarsFirmware.php',
	reader: autoFWStoreReader
});

autoFWStore.on('exception',handleProxyException);

var autoWindowSortBox = new Ext.form.Checkbox({
	boxLabel: 'Groepeer per plaats',
	checked : false
});

autoWindowSortBox.on('check',function(box, value){
	if(value == true){
		autoFWStore.groupBy('Plaats',true);
	}else{
		autoFWStore.clearGrouping();
	}
});

var autoFWWindowBottomBar = new Ext.Toolbar({
	items: [
		autoWindowSortBox,
		'->',
		{
		   iconCls : 'icon-excel',
			text: 'Exporteer excel bestand',
			handler: function(){
				var url='https://www.wheels4all.nl/backoffice/modules/api/getCarsFirmwareXLS.php'; 
				window.open(url);
			}
   	},'-',
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		})
	]
});

var autoFWGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: autoFWStore,
	loadMask: {msg:'Bezig met zoeken...<br>Dit duurt ongeveer 1 minuut'},
	anchor: '100%',
	columns: [
		{id:'AutoId',header: "AutoId", width: 50, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 70, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Plaats", width: 100, sortable: true, dataIndex: 'Plaats'},
		{header: "SW versie", width: 100, sortable: true, dataIndex: 'FW', groupable: true},
		{header: "06-nummer", width: 100, sortable: false, dataIndex: 'TelNr', groupable: false}
	],
	view: new Ext.grid.GroupingView({
		forceFit:true,
		startCollapsed: true,
		columnsText: 'Toon kolommen',
		groupByText: 'Groepeer op dit veld',
		showGroupsText: 'Groepeer',
		sortAscText: 'Sorteer oplopend',
		sortDescText: 'Sorteer aflopend',
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Autos" : "Auto"]})'
	}),
	rowCtxMenu: new Ext.menu.Menu({
		items: [
			{
				text: 'Wijzig autogegevens',
				iconCls: 'icon-wrench'
			},{
				text: 'Toon reserveringen',
				iconCls: 'icon-database_table'
			},{
				text: 'Maak reservering',
				iconCls: 'icon-date'
			}
		]
	})
});

autoFWGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var autoFWWindow = new Ext.Window({
	title: 'Auto software overzicht',
	width: 700,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: autoFWGrid,
	bbar: autoFWWindowBottomBar
});
