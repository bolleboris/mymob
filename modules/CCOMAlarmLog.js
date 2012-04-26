var alarmStoreReader = new Ext.data.XmlReader({
	idProperty: 'id',
	record: 'row',
	totalProperty: 'results',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
		{name: 'autoid', type: 'int'},
		{name: 'StateTimeStamp', type: 'string'},
		{name: 'AlarmDat', type: 'string'},
		{name: 'AlarmNummer', type: 'int'},
		{name: 'AlarmSubNr', type: 'int'},
		{name: 'SWVersion', type: 'int'},
		{name: 'SNR', type: 'string'},
		{name: 'AlarmDesc', type: 'string'}
	]
})

var alarmStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : true,
	storeId: 'alarmStore',
	url: './modules/api/getAlarmsXML.php',
	reader: alarmStoreReader
});
 
var alarmWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
   items : [ 
   	{
			text: 'Bekijk autogegevens',
			iconCls: 'icon-car',
			handler: function(){
				showCarDetails(alarmGrid.getSelectionModel().getSelected().get("autoid"));
			}
		},'-',{
			text: 'Toon reserveringen van auto',
			iconCls: 'icon-database_table',
			handler: function(){
				showResPerAuto(alarmGrid.getSelectionModel().getSelected().get("autoid"));
			}
		},'->',{
         iconCls : 'icon-help',
			text: 'Help',
			handler: function(){ alarmWindowHelp.show(this); }
		}
	]
});

var alarmGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: alarmStore,
	anchor: '100%',
	loadMask: {msg:'Bezig met laden..'},
	columns: [
		{header: "AutoId", width: 45, sortable: false, dataIndex: 'autoid'},
		{header: "Melding tijd:", width: 100, sortable: false, dataIndex: 'AlarmDat'},
		{header: "Ontvangen op:", width: 120, sortable: true, dataIndex: 'StateTimeStamp'},
		{header: "AlarmNr", width: 40, sortable: false, dataIndex: 'AlarmNummer'},
		{header: "SubNr", width: 40, sortable: false, dataIndex: 'AlarmSubNr'},
		{header: "SW Versie", width: 40, sortable: false, dataIndex: 'SWVersion', hidden:false},
		{header: "Signaalsterkte", width: 40, sortable: false, dataIndex: 'SNR', hidden:true},
		{header: "Alarm Beschrijving", width: 295, sortable: false, dataIndex: 'AlarmDesc'}
	],
	rowCtxMenu: new Ext.menu.Menu({
		items: [
			{
				text: 'Bekijk autogegevens',
				iconCls: 'icon-car',
				handler: function(){
					showCarDetails(alarmGrid.getSelectionModel().getSelected().get("autoid"));
				}
			},{
				text: 'Bekijk beheerdersgegevens',
				iconCls: 'icon-user',
				disabled: true
			},{
				text: 'Toon reserveringen',
				iconCls: 'icon-database_table',
				handler: function(){
					showResPerAuto(alarmGrid.getSelectionModel().getSelected().get("autoid"));
				}
			}
		]
	})
});

var alarmWindowBottomBar = new Ext.Toolbar({
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
			store: alarmStore,       // grid and PagingToolbar using same store
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

alarmGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var alarmWindow = new Ext.Window({
	title: 'Auto Alarm Berichten',
	width: 700,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-error',
	items: alarmGrid,
	tbar: alarmWindowTopBar,
	bbar: alarmWindowBottomBar
});

var alarmWindowHelp = new Ext.Window({
	title: 'Auto Alarm Berichten Help',
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
			html: "<h1>CCOM Waarschuwingen</h1><br><p>Met deze module kan je waarschuwingen zien, die door een boordcomputer zijn verstuurd.</p><br>"+
			"<p>Je kunt sorteren op een kolom, door op de kolomnaam te klikken.<br>Je kunt ook extra kolommen weergeven door met je muis op de kolomnaam te gaan staan, en dan op het pijltje te klikken wat naast de kolomnaam verschijnt.</p><br>"+
			"<p>Onderaan het venster staat een checkbox waarmee je de waarschuwingen kunt groeperen per nummer.</p><br>"
		}
	]
});
