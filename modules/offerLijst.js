var offerLijstStore = new Ext.data.GroupingStore({
	url: './modules/api/getOffers.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

offerLijstStore.on('exception', handleProxyException);

var offerLijstWindowTopBar = new Ext.Toolbar({			//Topbar voor main window
	items : [
		/*{
			text: 'Offer toevoegen',
			iconCls: 'icon-bullet_plus',
			handler: function () {
				//showOfferDetails(offerLijstGrid.getSelectionModel().getSelected().get("OfferNr"));
				addOffer();
			}
		}, */{
			text: 'Wijzig offergegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				var selectedRow = offerLijstGrid.getSelectionModel().getSelected();
				if (selectedRow) {
					showOfferDetails(selectedRow.get("OfferNr"), selectedRow.get("VerschafferNr"));
				}
			}
		}, {
			text: 'Toon leveranciersdetails',
			iconCls: 'icon-user',
			handler: function () {
				// @todo Welke scripts zijn verantwoordelijk voor het ophalen van Suppliers/LegalEntities?
				showSupplierDetails(offerLijstGrid.getSelectionModel().getSelected().get("Verschaffer"));
			}
		}
	]
});

var offerLijstGridCtxMenu = new Ext.menu.Menu({
	items: [
		{
			text: 'Wijzig offergegevens',
			iconCls: 'icon-wrench',
			handler: function () {
				var selectedRow = offerLijstGrid.getSelectionModel().getSelected();
				if (selectedRow) {
					showOfferDetails(selectedRow.get("OfferNr"), selectedRow.get("VerschafferNr"));
				}
			}
		}, {
			text: 'Toon leveranciersdetails',
			iconCls: 'icon-user',
			handler: function () {
				showSupplierDetails(offerLijstGrid.getSelectionModel().getSelected().get("Verschaffer"));
			}
		}
	]
});

var offerLijstWindowBottomBar = new Ext.Toolbar({
	items: [
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

var offerLijstGrid = new Ext.grid.GridPanel({
	frame: false,
	header: false,
	collapsible: true,
	animCollapse: true,
	loadMask: true,
	store: offerLijstStore,
	loadMask: {msg:'Bezig met laden..<br/>Dit kan best even duren'},
	anchor: '100%',
	columns: [
		{header: "Nr", width: 50, sortable: true, dataIndex: 'OfferNr'},
		{header: "Verschaffer", width: 75, sortable: true, dataIndex: 'Verschaffer'},
		{header: "Leverancier", width: 100, sortable: true, dataIndex: 'Leverancier'},
		{header: "Offer Soort", width: 75, sortable: true, dataIndex: 'OfferSoort'},
		{header: "Offer Status", width: 75, sortable: true, dataIndex: 'Status'},
		{header: "Datum aangemaakt", width: 175, sortable: true, dataIndex: 'DatumAangemaakt'},
		{header: "Datum aangevraagd", width: 100, sortable: true, dataIndex: 'DatumAangevraagd'},
		{header: "Geldig van", width: 75, sortable: true, dataIndex: 'GeldigVan'},
		{header: "Geldig tot", width: 175, sortable: true, dataIndex: 'GeldigTot'}
	],
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	}),
	rowCtxMenu: offerLijstGridCtxMenu
});

offerLijstGrid.on('rowcontextmenu', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	grid.getSelectionModel().selectRow(rowIdx);	//Select de row (doet ie niet standaard bij rightclick
	grid.rowCtxMenu.showAt(evtObj.getXY());		//Render ons eigen menu
});

offerLijstGrid.on('rowdblclick', function (grid, rowIdx, evtObj) {
	evtObj.stopEvent();			//Voorkom dat browser zijn eigen rightclick menu renderd.
	var selectedRow = offerLijstGrid.getSelectionModel().getSelected();

	//showContractDetails(grid.getSelectionModel().getSelected().get("ContractNr"));
	//showUserDetails(grid.getSelectionModel().getSelected().get("OfferNr"));
	showOfferDetails(selectedRow.get("OfferNr"), selectedRow.get("VerschafferNr"));
});

var offerLijstWindow = new Ext.Window({
	title: 'Offers overzicht',
	width: 525,
	height: 400,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-page_white_text',
	items: offerLijstGrid,
	tbar: offerLijstWindowTopBar,
	bbar: offerLijstWindowBottomBar
});

// @todo
var addOffer = function () {
	Ext.Msg.alert('TODO!');
	return false;
}

// move this to a separate file?
var showSupplierDetails = function (supplier_id) {
	Ext.Msg.alert('TODO!');
	return false;
}