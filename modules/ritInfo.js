var ritInfoReader = new Ext.data.JsonReader({
	totalProperty: 'results',
	messageProperty: 'msg',
	successProperty: 'success'
});
	
var ritInfoStore = new Ext.data.JsonStore({
	storeId: 'rittenLijst',
	url: './modules/api/getReservationDetails.php',
	remoteSort: false,
	autoLoad: false,
    root: 'rows',
    reader: ritInfoReader,
    //totalProperty: 'results',
    fields: [
		{name: 'RitNr'},
		{name: 'AutoId'},
		{name: 'Kenteken'},
		{name: 'AutoPlaats'},
		{name: 'AutoStraat'},		
		{name: 'Geannuleerd'},
		{name: 'Bijnaam'},
		{name: 'PersoonNr'},
		{name: 'Voornaam'},
		{name: 'Achternaam'},
		{name: 'Initialen'},
		{name: 'Verantwoordelijke'},
		{name: 'verantwoordelijkeVoornaam'},
		{name: 'verantwoordelijkeAchternaam'},
		{name: 'verantwoordelijkeInitialen'},
		{name: 'Optie'},
		{name: 'OpmerkingReservering'},
		{name: 'OpmerkingValidatie'},
		{name: 'ReserveringBegin'},
		{name: 'ReserveringEind'},
		{name: 'GebruikBegin'},
		{name: 'GebruikEind'},
		{name: 'KilometerstandBegin'},
		{name: 'KilometerstandEind'},
		{name: 'GebruiktMifareUID'},
		{name: 'Boordcomputer'}
    ]
});


var ritAuditStore = new Ext.data.Store({
	url: './modules/api/getReservationAudit.php',
	reader: new Ext.data.JsonReader({
		root: 'rows',
		totalProperty: 'results',
		id: 'post_id',
		fields: [
			{name: 'Tijdstip'},
			{name: 'Status'}
		]
	})
});


ritAuditStore.on('exception',handleProxyException);

// Custom rendering Template
var ritTpl = new Ext.XTemplate(
    '<tpl for="."><div class="rit-info">',
    		'<p><pre><b>Rit: {RitNr} 	<FONT color="red">{Geannuleerd}</FONT></b></pre></p>',
    		'<p>Auto: {AutoId} ({Bijnaam}) {Kenteken}, {AutoStraat}, {AutoPlaats} {Boordcomputer}</p>',
        '<p>Reserveerder: {PersoonNr}: {Achternaam}, {Initialen} ({Voornaam})<br>',
        'Contractant: {Verantwoordelijke}: {verantwoordelijkeAchternaam}, {verantwoordelijkeInitialen} ({verantwoordelijkeVoornaam})</p>',        
        '<p><b>Begin reservering:</b> {ReserveringBegin} <b>Eind:</b> {ReserveringEind} {Optie} {OpmerkingReservering}</p>',
        '<p><b>Gereden:</b> Begin: {GebruikBegin} Eind: {GebruikEind} KMbegin: {KilometerstandBegin} KMeind: {KilometerstandEind} {OpmerkingValidatie} Pasnummer: {GebruiktMifareUID}</p>',
    '</div></tpl>'
);

var ritTplLoading = new Ext.XTemplate(
    '<tpl for="."><div class="rit-info">',
    		'<p>Bezig met laden...</p>',
    '</div></tpl>'
);


var ritAudit = new Ext.list.ListView({
    store: ritAuditStore,
    multiSelect: false,
    emptyText: 'Geen reserveringen gevonden',
    loadingText: 'Gegevens laden',
    reserveScrollOffset: true,
    height: 195,
    autoScroll: true,
    columns: [{
    	width: .2,
        header: 'Tijdstip',
        dataIndex: 'Tijdstip'
    },{
        header: 'Status',
        dataIndex: 'Status'
    }]
});

var ritInfoWindowTopBar = new Ext.Toolbar({
	items: [
		{
	   		text: 'Auto waarschuwingen',
	   		iconCls: 'icon-error',
	   		handler: function(){
	   			showCarAlarms(ritInfoStore.getAt(0).get("AutoId"));
	   		}
   		},{
			text: 'Reserveringen van auto',
			iconCls: 'icon-database_table',
			handler: function () {
				showResPerAuto(ritInfoStore.getAt(0).get("AutoId"));
			}
		},{
			text: 'Persoongegevens',
			iconCls: 'icon-user',
			handler: function(){
				var PersoonNr = ritInfoStore.getAt(0).get("PersoonNr");
				showUserDetails(PersoonNr);
			}
		},{
			text: 'Opnieuw sturen',
			iconCls: 'icon-transmit',
			handler: function(){
				var ritNr = resPerAutoGrid.getSelectionModel().getSelected().get("RitNr");
				resendRes(ritNr);
			}
		},{
			text: 'Reserveersite',
			iconCls: 'icon-application_form',
			handler: function(){
				window.open('https://www.wheels4all.nl/reserveren/index.php?AutoWisselen=TRUE&AutoId='+ritInfoStore.getAt(0).get("AutoId"),'Reserveren') 
			}
		}
	]
});

var ritInfoWindowBottomBar = new Ext.Toolbar({
	items: [
   	'->',
		new Ext.Button({
		   iconCls : 'icon-arrow_refresh',
			text: 'Vernieuw',
			handler: function(){
				ritInfoStore.reload();
				ritAuditStore.reload();
			}
		}),
		new Ext.Button({
			text: 'Sluit venster',
			iconCls: 'icon-cross',
			handler: function(){
				this.findParentByType('window').hide();
			}
		})
	]
});

var ritInfoWindow = new Ext.Window({
//	title: 'Rit Details',
//	layout: 'fit',
	width: 650,
	height: 400,
	closable: true,
	//resizable: false, 
	closeAction: 'hide',
	iconCls: 'icon-car',
	layout:'border',
	layoutConfig: {
		align : 'stretch',
		pack  : 'start'
	},
	items: [
		{
			title: 'Rit details',
			region: 'north',
			height: 120,
			//collapsible: true,
			id: 'ritDetailPanel',
			bodyStyle: {
				background: '#ffffff',
				padding: '7px'
			},
			html: 'Kan geen gegevens vinden van deze rit!'
		}, {
			title: 'Historie',
			region: 'center',
			items: ritAudit
		}
	],
	bbar: ritInfoWindowBottomBar,
	tbar: ritInfoWindowTopBar
});

//Vul user info blok adv template en gegevens uit store.
ritInfoStore.on('load',function(st, recs, options){
	var ritInfoPanel = Ext.getCmp('ritDetailPanel');
	ritTpl.overwrite(ritInfoPanel.body, st.getAt(0).data);
});

ritInfoStore.on('beforeload',function(st, options){
	var ritInfoPanel = Ext.getCmp('ritDetailPanel');
	ritTplLoading.overwrite(ritInfoPanel.body, '');
});

	
var showRitDetails = function(RitNr){
	ritInfoWindow.setTitle("Details van rit "+RitNr);
	ritInfoWindow.show();
	ritInfoStore.load({params: {RitNr : RitNr}});
	ritAuditStore.load({params: {RitNr : RitNr}});	
};
