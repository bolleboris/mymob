var ledenLijstStore = new Ext.data.GroupingStore({
		url: './modules/api/getLeden.php',
		reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

ledenLijstStore.on('exception',handleProxyException);
 
var ledenLijstWindowSortBox = new Ext.form.Checkbox({
	boxLabel: 'Groepeer per plaats',
	checked : false
});

ledenLijstWindowSortBox.on('check',function(box, value){
	if(value == true){
		ledenLijstStore.groupBy('Woonplaats',true);
	}else{
		ledenLijstStore.clearGrouping();
	}
});

var ledenLijstWindowBottomBar = new Ext.Toolbar({
	items: [
		ledenLijstWindowSortBox,
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

var autoGrid = new Ext.grid.GridPanel({
	frame:false,
	header: false,
	collapsible: true,
	animCollapse: true,
	loadMask: {msg:'Bezig met zoeken...'},
	store: ledenLijstStore,
	//anchor: '100%',
	columns: [
		{header: "Nr", width: 50, sortable: true, dataIndex: 'PersoonNr', groupable: false},
		{header: "Achternaam", width: 125, sortable: true, dataIndex: 'Achternaam', groupable: false},
		{header: "Voornaam", width: 100, sortable: true, dataIndex: 'Voornaam', groupable: false},
		{header: "Adres", width: 150, sortable: true, dataIndex: 'Adres', groupable: false},
		{header: "Plaats", width: 125, sortable: true, dataIndex: 'Woonplaats'},
		{header: "Email", width: 150, sortable: true, dataIndex: 'Email', groupable: false},
		{header: "Telefoon1", width: 80, sortable: true, dataIndex: 'Telefoon1', groupable: false},
		{header: "Telefoon2", width: 80, sortable: true, dataIndex: 'Telefoon2', groupable: false}
	],
	view: new Ext.grid.GroupingView({
		//forceFit:true,
		startCollapsed: true,
		columnsText: 'Toon kolommen',
		groupByText: 'Groepeer op dit veld',
		showGroupsText: 'Groepeer',
		sortAscText: 'Sorteer oplopend',
		sortDescText: 'Sorteer aflopend',
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Leden" : "Lid"]})'
	}),
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	})
});

var ledenLijstWindow = new Ext.Window({
	title: 'Leden overzicht',
	width: 900,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-user',
	items: autoGrid,
	bbar: ledenLijstWindowBottomBar
});


