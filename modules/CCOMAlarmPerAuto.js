var alarmStorePerCarReader = new Ext.data.XmlReader({
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

var alarmStorePerCar = new Ext.data.Store({
	autoLoad: false,
	remoteSort : true,
	storeId: 'alarmStorePerCar',
	url: './modules/api/getAlarmsPerAutoXML.php',
	reader: alarmStorePerCarReader
});
 
var alarmPerCarWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
   items : [ 
   	{
      	iconCls : 'icon-arrow_refresh',
			text: 'Vernieuw',
			handler: function(){alarmStorePerCar.reload();}
   	}
	]
});

var alarmPerCarGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	store: alarmStorePerCar,
	anchor: '100%',
	loadMask: {msg:'Bezig met laden..'},
	columns: [
		{header: "AutoId", width: 50, sortable: false, dataIndex: 'autoid', hidden:true},
		{header: "Ontvangen", width: 120, sortable: true, dataIndex: 'StateTimeStamp'},
		{header: "Melding tijd", width: 110, sortable: false, dataIndex: 'AlarmDat'},
		{header: "AlarmNr", width: 40, sortable: false, dataIndex: 'AlarmNummer'},
		{header: "SubNr", width: 40, sortable: false, dataIndex: 'AlarmSubNr'},
		{header: "SW Versie", width: 40, sortable: false, dataIndex: 'SWVersion', hidden:false},
		{header: "Signaalsterkte", width: 40, sortable: false, dataIndex: 'SNR', hidden:true},
		{header: "Alarm Beschrijving", width: 330, sortable: false, dataIndex: 'AlarmDesc'}
	]
});

var alarmPerCarWindowBottomBar = new Ext.Toolbar({
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
			store: alarmStorePerCar,       // grid and PagingToolbar using same store
			displayInfo: true,
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


var alarmPerCarWindow = new Ext.Window({
	title: 'Alarm berichten per auto',
	width: 700,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-error',
	items: alarmPerCarGrid,
	tbar: alarmPerCarWindowTopBar,
	bbar: alarmPerCarWindowBottomBar
});


var showCarAlarms = function(AutoId){
	alarmPerCarWindow.setTitle("Alarm berichten van auto "+AutoId);
	alarmPerCarWindow.show();
	alarmStorePerCar.setBaseParam('AutoId',AutoId);
	alarmStorePerCar.load();
};
