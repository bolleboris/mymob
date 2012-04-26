var nieuwePassenStoreReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'PersoonNr'},
		{name: 'Persoon'},
		{name: 'Pincode'},
		{name: 'Verloopdatum'}
	]
})

var nieuwePassenStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : true,
	storeId: 'nieuwePassen',
	url: 'modules/api/getUserGrants.php',
	reader: nieuwePassenStoreReader
});

nieuwePassenStore.on('exception',handleProxyException);

var nieuwePassenWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
   items : [ 
   		{
				text: 'Bekijk persoongegevens',
				iconCls: 'icon-user',
				handler: function(){
					var PersoonNr = nieuwePassenGrid.getSelectionModel().getSelected().get("PersoonNr");
					showUserDetails(PersoonNr);
				}
			},'-',	{
				text: 'Trek machtiging in',
				iconCls: 'icon-cross',
				handler: function(){
					var userId = nieuwePassenGrid.getSelectionModel().getSelected().get('PersoonNr');
					var naam = nieuwePassenGrid.getSelectionModel().getSelected().get('Persoon');		//Volledige naam
					machtigNieuwePasIntrekken(userId, naam);
				}
			}
	]
});

var nieuwePassenGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: nieuwePassenStore,
	anchor: '100%',
	sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
	columns: [
		{header: "Persoon", width: 150, sortable: true, dataIndex: 'Persoon'},
		{header: "PersoonNr", width: 75, sortable: true, dataIndex: 'PersoonNr'},
		{header: "Pincode", width: 50, sortable: true, dataIndex: 'Pincode'},
		{header: "Verloopdatum", width: 100, sortable: true, dataIndex: 'Verloopdatum'}
	],
	rowCtxMenu: new Ext.menu.Menu({
		items: [
			{
				text: 'Bekijk persoongegevens',
				iconCls: 'icon-user',
				handler: function(){
					var PersoonNr = nieuwePassenGrid.getSelectionModel().getSelected().get("PersoonNr");
					showUserDetails(PersoonNr);
				}
			},	{
				text: 'Trek machtiging in',
				iconCls: 'icon-cross',
				handler: function(){
					var userId = nieuwePassenGrid.getSelectionModel().getSelected().get('PersoonNr');
					var naam = nieuwePassenGrid.getSelectionModel().getSelected().get('Persoon');		//Volledige naam
					machtigNieuwePasIntrekken(userId, naam);
				}
			}
		]
	})
});

nieuwePassenGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);		//Selecteer de row als we er op klikken met rechter muisknop
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var nieuwePassenWindowBottomBar = new Ext.Toolbar({
	items: [
	{
			xtype: 'paging',
			afterPageText: 'van {0}',
			beforePageText: 'Pagina',
			firstText: 'Eerste pagina',
			lastText: 'Laatste pagina',
			nextText: 'Volgende pagina',
			prevText: 'Vorige pagina',
			refreshText: 'Vernieuw',
			store: nieuwePassenStore,       // grid and PagingToolbar using same store
			displayInfo: false,
			pageSize: 13,
			prependButtons: true
		},
		'->',
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		})
	]
});

var nieuwePassenWindow = new Ext.Window({
	title: 'Nieuwe passen machtigingen',
	width: 400,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-creditcards',
	items: nieuwePassenGrid,
	tbar: nieuwePassenWindowTopBar,
	bbar: nieuwePassenWindowBottomBar
});
