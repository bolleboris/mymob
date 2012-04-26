//TODO: stores ombouwen met metadata

var userReader = new Ext.data.JsonReader({
	totalProperty: 'results',
	messageProperty: 'msg',
	successProperty: 'success'
});
	
var userInfoStore = new Ext.data.JsonStore({
	storeId: 'rittenLijst',
	url: './modules/api/getUserInfo.php',
	remoteSort: false,
	autoLoad: false,
	root: 'rows',
	reader: userReader,
	//totalProperty: 'results',
	fields: [
		{name: 'PersoonNr'},
		{name: 'W4APersoonNr'},
		{name: 'Voornaam'},
		{name: 'Achternaam'},
		{name: 'Initialen'},
		{name: 'Woonplaats'},
		{name: 'Adres'},		
		{name: 'Postcode'},
		{name: 'FavorieteAuto'},
		{name: 'FavorieteAutoBijnaam'},
		{name: 'Telefoon1'},
		{name: 'Telefoon2'},
		{name: 'Email'},
		{name: 'AbonnementSoort'},
		{name: 'AbonnementGraad'},
		{name: 'OpgezegdPer'},
		{name: 'ContractNr'},
		{name: 'ContractStatus'}
	]
});

userInfoStore.on('exception',handleProxyException);

var userRittenReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
        {name: 'RitNr'},
        {name: 'AutoId'},
        {name: 'Bijnaam'},
        {name: 'ReserveringBegin'},
        {name: 'ReserveringEind'},
        {name: 'Status'}
	]
});

var userContractReader = new Ext.data.JsonReader({
	idProperty: 'id',
	root: 'rows',
	totalProperty: 'results',
	successProperty: 'success',
	messageProperty: 'msg',  // The element within the response that provides a user-feedback message (optional)
	fields: [
        {name: 'ContractNr'},
        {name: 'Soort'}
	]
});

var userInfoRittenStore = new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	sortInfo: {
		field: 'ReserveringBegin',
	    direction: 'DESC'
	},
	storeId: 'userInfoRittenStore',
	url: './modules/api/getReservationsPerUser.php',
	reader: userRittenReader
});

var userInfoContractStore =  new Ext.data.Store({
	autoLoad: false,
	remoteSort : false,
	storeId: 'userInfoContractStore',
	url: './modules/api/getContractsPerUser.php',
	reader: userContractReader
});


userInfoRittenStore.on('exception',handleProxyException);

// Custom rendering Template
var userTpl = new Ext.XTemplate(
    '<tpl for="."><div class="user-info">',
        '<p><b>{Achternaam}, {Initialen} ({Voornaam})</b> W4A PersoonNr: {W4APersoonNr} BMU persoonNr {PersoonNr} <b>{OpgezegdPer}</b>',
        '<br>{Postcode}, {Woonplaats}, {Adres}</p>',
        '<p>Tel1: {Telefoon1} Tel2: {Telefoon2}<br>Email: {Email}</p>',
        '<p>Favoriete auto: {FavorieteAuto} ({FavorieteAutoBijnaam})</p>',
    '</div></tpl>'
);

var userTplLoading = new Ext.XTemplate(
    '<tpl for="."><div class="user-info">',
        '<p>Gegevens zoeken...</p>',
    '</div></tpl>'
);

var laatsteRitten = new Ext.list.ListView({
    store: userInfoRittenStore,
    multiSelect: false,
    singleSelect: true,
    height: 275,
    emptyText: 'Geen reserveringen gevonden',
    reserveScrollOffset: true,
    loadingText: 'Gegevens laden',
    autoScroll: true,
    columns: [{
    	width: .075,
        header: 'RitNr',
        dataIndex: 'RitNr'
    },{
    	width: .05,
        header: 'Auto',
        dataIndex: 'AutoId'
    },{
    	width: .3,
        header: 'Auto bijnaam',
        dataIndex: 'Bijnaam'
    },{
    		width: .15,
        header: 'Begin',
        dataIndex: 'ReserveringBegin'
    },{
        width: .15,
        header: 'Eind',
        dataIndex: 'ReserveringEind'
    },{
        header: 'Status',
        dataIndex: 'Status'
    }]
});

var contracten = new Ext.list.ListView({
    store: userInfoContractStore,
    multiSelect: false,
    singleSelect: true,
    emptyText: 'Geen contracten gevonden',
    reserveScrollOffset: true,
    loadingText: 'Gegevens laden',
    autoScroll: true,
    columns: [{
    	width: .35,
        header: 'Contract',
        dataIndex: 'ContractNr'
    },{
        header: 'Soort lid',
        dataIndex: 'Soort'
    }]
});


var userInfoTopBar = new Ext.Toolbar({
	items: [
		{
			text: 'Machtig nieuwe pas',
			iconCls: 'icon-creditcards',
			handler: function(){
				var userId = userInfoStore.lastOptions.params.userId;
				var naam = userInfoStore.getAt(0).get('Voornaam')+' '+userInfoStore.getAt(0).get('Achternaam');		//Volledige naam
				machtigNieuwePas(userId, naam);
			}
		},'-',{
			text: 'Wijzig gebruiker gegevens',
			iconCls: 'icon-user_edit',
			disabled: true
		}
	]
});

var laatsteRittenTopBar = new Ext.Toolbar({
	items: [
		{
			text: 'Annuleer reservering',
			iconCls: 'icon-cross',
			//disabled: true
			handler: function(){
				var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
				var userId = userInfoStore.lastOptions.params.userId;
				annuleerRit(ritNr,userId);
				userInfoRittenStore.reload();
			}
		},'-',{
			text: 'Stuur reservering opnieuw',
			iconCls: 'icon-transmit',
			handler: function(){
				var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
				resendRes(ritNr);
			}
		},'-',{
			text: 'Toon reservering details',
			iconCls: 'icon-database_table',
			handler: function(){
				var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
				showRitDetails(ritNr);
			}
		},'-',{
			text: 'Wijzig reservering',
			iconCls: 'icon-wrench',
			disabled: true
		},'-',{
			text: 'Maak reservering',
			iconCls: 'icon-date',
			disabled: true
		}
	]
});

var userInfoWindowBottomBar = new Ext.Toolbar({
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
});

laatsteRitten.on('contextmenu',function(dataview, index, node ,e){
	e.stopEvent();
	this.select(index);	//Selecteer regel (puur optisch)
	if(!this.menu){
		this.menu = new Ext.menu.Menu({
			items: [
				{
					text: 'Annuleer reservering',
					iconCls: 'icon-cross',
					handler: function(){
						var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
						var userId = userInfoStore.lastOptions.params.userId;
						annuleerRit(ritNr,userId);
						userInfoRittenStore.reload();
					}
				},{
					text: 'Stuur reservering opnieuw',
					iconCls: 'icon-transmit',
					handler: function(){
						var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
						resendRes(ritNr);
					}
				},{
					text: 'Toon reservering details',
					iconCls: 'icon-database_table',
					handler: function(){
						var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
						showRitDetails(ritNr);
					}
				},{
					text: 'Wijzig reservering',
					iconCls: 'icon-wrench',
					disabled: true
				},{
					text: 'Auto details',
					iconCls: 'icon-car',
					handler: function(){
						showResPerAuto(laatsteRitten.getSelectedRecords()[0].get("AutoId"));
					}
				},{
					text: 'Open reserveersite',
					iconCls: 'icon-application_form',
					handler: function(){
						window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId='+laatsteRitten.getSelectedRecords()[0].get("AutoId"),'Reserveren') 
					}
				}
			]
		})
	}
	this.menu.showAt(e.xy);
});

laatsteRitten.on('dblclick',function(dataview, index, node ,e){
	e.stopEvent();
	this.select(index);	//Selecteer regel (puur optisch)
	var ritNr = laatsteRitten.getSelectedRecords()[0].get("RitNr");
	showRitDetails(ritNr);
});

contracten.on('dblclick',function(dataview, index, node ,e){
	e.stopEvent();
	this.select(index);	//Selecteer regel (puur optisch)
	var ritNr = contracten.getSelectedRecords()[0].get("ContractNr");
	showContractDetails(ritNr);
});


var userInfoWindow = new Ext.Window({
	title: 'Persoon',
//	layout: 'fit',
	width: 900,
	height: 510,
	closable: true,
	resizable: false, 
	closeAction: 'hide',
	iconCls: 'icon-user',
	layout:'border',
	/*layoutConfig: {
		align : 'stretch',
		pack  : 'start'
	},*/
	items: [
		{
//			title: 'Gebruiker details',
			region: 'north',
			height: 125,
			collapsible: false,
			id: 'userDetailPanel',
			bodyStyle: {
				background: '#ffffff',
				padding: '7px'
			},
			html: 'Kan geen gegevens vinden van deze gebruiker!',
			tbar: userInfoTopBar
		}, {
			title: 'Reserveringen:',
			region: 'center',
			items: laatsteRitten,
			tbar: laatsteRittenTopBar
		},{
			title: 'Contracten',
			width: 200,
			region: 'east',
			items: contracten
		}
	],
	bbar: userInfoWindowBottomBar
});

//Vul user info blok adv template en gegevens uit store.
userInfoStore.on('load',function(st, recs, options){
	var userInfoPanel = Ext.getCmp('userDetailPanel');
	userTpl.overwrite(userInfoPanel.body, st.getAt(0).data);
});

userInfoStore.on('beforeload',function(st, options){
	var userInfoPanel = Ext.getCmp('userDetailPanel');
	userTplLoading.overwrite(userInfoPanel.body, '');
});
	
var showUserDetails = function(BMUuserId, W4APersonNr){
	userInfoWindow.show();
	userInfoWindow.setTitle("Details van persoon "+W4APersonNr);
	userInfoStore.load({params: {userId : BMUuserId}});
	userInfoContractStore.setBaseParam('userId',BMUuserId);
	userInfoContractStore.load();
	userInfoRittenStore.setBaseParam('userId',BMUuserId);
	userInfoRittenStore.load();
};
