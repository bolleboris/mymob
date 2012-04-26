Ext.namespace("Ext.ux");
Ext.ux.comboBoxRenderer = function(combo) {
  return function(value) {
    //var idx = aanvraagRittenStore.find(combo.valueField, value);
    //var rec = aanvraagRittenStore.getById(combo.hiddenName);
    //var rec = aanvraagRittenStore.getById("InAanvraagBeheerder");
    //return rec.get(combo.displayField);
    return value;
  };
}

var aanvraagRittenStore = new Ext.data.GroupingStore({
	url: './modules/api/getRequests.php',
	reader : new Ext.data.JsonReader(),			//Reader configureerd zichzelf met metaData in JSON string
	writer : new Ext.data.JsonWriter()
});
 
aanvraagRittenStore.on('exception',handleProxyException);

var InAanvraagMywheelsStore = new Ext.data.SimpleStore({
	fields: ['num', 'desc'],
	data: [
		[1, 'In aanvraag'],
		[0, 'Goedgekeurd']
	]
});

var InAanvraagBeheerderStore = new Ext.data.SimpleStore({
	fields: ['num', 'desc'],
	data: [
		[1, 'In aanvraag'],
		[0, 'Goedgekeurd']
	]
});

var InAanvraagMywheelsCombo = new Ext.form.ComboBox({
	store: InAanvraagMywheelsStore,
	triggerAction: 'all',
	valueField: "desc",
	displayField: "desc",
	hiddenName : 'InAanvraagMywheels',	//Zorg dat 'num' ipv 'desc' wordt gepost
	mode: 'local',
	editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
	forceSelection: true,
	triggerAction: 'all',
	selectOnFocus: true,
	lazyInit: false		//Init items direct, niet op dropdown event
});


var InAanvraagBeheerderCombo = new Ext.form.ComboBox({
	store: InAanvraagBeheerderStore,
	triggerAction: 'all',
	valueField: "desc",
	displayField: "desc",
	hiddenName : 'InAanvraagBeheerder',	//Zorg dat 'num' ipv 'desc' wordt gepost
	mode: 'local',
	editable: false,		//User mag niet zelf typen, alleen kiezen uit lijst.
	forceSelection: true,
	triggerAction: 'all',
	selectOnFocus: true,
	lazyInit: false		//Init items direct, niet op dropdown event
});


var aanvraagRittenWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
  items : [
			{
				text: 'Toon details van rit',
				iconCls: 'icon-database_table',
				handler: function(){
					showRitDetails(aanvraagRittenGrid.getSelectionModel().getSelected().get("RitNr"));
				}
			},{
				text: 'Toon autogegevens',
				iconCls: 'icon-wrench',
				handler: function(){
					showCarDetails(aanvraagRittenGrid.getSelectionModel().getSelected().get("AutoId"));
				}
			},'->',{
				iconCls : 'icon-arrow_refresh',		
				text: 'Vernieuw',
				handler: function(){
					aanvraagRittenStore.load();
				}
			}
		]
});

var aanvraagRittenGrid = new Ext.grid.EditorGridPanel({
	store: aanvraagRittenStore,
	frame: false,
	stripeRows: true,
	header: false,
	anchor: '100%',
	clicksToEdit: 1,
	//loadMask: {msg:'Bezig met laden..'},
	maskDisabled: false,
	columns: [
		{header: "RitNr", width: 60, sortable: true, dataIndex: 'RitNr'},
		{header: "AutoId", width: 40, sortable: true, dataIndex: 'AutoId'},
		{header: "Auto bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam'},
		{header: "Reserveerder", width: 100, sortable: true, dataIndex: 'ReserveerderNaam'},
		{header: "Beheerder", width: 100, sortable: true, dataIndex: 'BeheerderNaam'},
		{header: "Aanvr. Mywheels?", width: 100, sortable: true, dataIndex: 'InAanvraagMywheels', editor: InAanvraagBeheerderCombo, renderer: Ext.ux.comboBoxRenderer(InAanvraagBeheerderCombo)},
		{header: "Aanvr. Beheerder?", width: 100, sortable: true, dataIndex: 'InAanvraagBeheerder', editor: InAanvraagMywheelsCombo, renderer: Ext.ux.comboBoxRenderer(InAanvraagMywheelsCombo)}
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
					showRitDetails(aanvraagRittenGrid.getSelectionModel().getSelected().get("RitNr"));
				}
			},{
				text: 'Toon autogegevens',
				iconCls: 'icon-wrench',
				handler: function(){
					showCarDetails(aanvraagRittenGrid.getSelectionModel().getSelected().get("AutoId"));
				}
			}
	   	]
	})
});

aanvraagRittenGrid.on('rowcontextmenu',function(grid, rowIdx, evtObj){
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

var aanvraagRittenWindow = new Ext.Window({
	title: 'Status ritten met aanvraag',
	width: 650,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-accept',
	items: aanvraagRittenGrid,
	tbar: aanvraagRittenWindowTopBar
});

