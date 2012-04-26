
var	offerSoortStore = {},
	offerDetailWindowNumber = null,
	offerDetailWindow = {};


// @todo
var showOfferDetails = function(OfferNr) {

	Ext.Msg.alert('TODO!');
	return false;

	offerDetailWindow.show(this);
	offerDetailWindow.setTitle("Offer "+OfferNr);
	offerDetailWindowNumber = OfferNr;
	offerSoortStore.load();

	contractLedenStore.load({params: {OfferNr : OfferNr}, waitMsg:'Laden...'});
	contractDetailForm.getForm().load({url:'./modules/api/getOffer.php', params: {OfferNr : OfferNr}, waitMsg:'Laden...'});
};