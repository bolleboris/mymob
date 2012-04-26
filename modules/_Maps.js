var LCKaart = new Ext.ux.GMapPanel({
	zoomLevel: 8,
	gmapType: 'map',
	setCenter: {
		lat: 52.089543,
		lng: 5.109744
	}
});

var geoXMLcars = new GGeoXml('http://reserveren.wheels4all.nl/leontest/KML/genKML.php?cars');
var geoXMLpersons = new GGeoXml('http://reserveren.wheels4all.nl/leontest/KML/genKML.php?persons');
var geoXMLcontractors = new GGeoXml('http://reserveren.wheels4all.nl/leontest/KML/genKML.php?contractors');

var getVars = new Array();

var showCarsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon autos',
	checked : false
});

showCarsBox.on('check', function (box, value) {
	if (value === true) {
		LCKaart.getMap().addOverlay(geoXMLcars);
		getVars[0] = "cars";
	} else {
		LCKaart.getMap().removeOverlay(geoXMLcars);
		getVars[0] = "";
	}
});

var showPersonsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon personen',
	checked : false
});

showPersonsBox.on('check', function (box, value) {
	if (value === true) {
		LCKaart.getMap().addOverlay(geoXMLpersons);
		getVars[1] = "persons";
	} else {
		LCKaart.getMap().removeOverlay(geoXMLpersons);
		getVars[1] = "";
	}
});

var showContractorsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon contractanten',
	checked : false
});

showContractorsBox.on('check', function (box, value) {
	if (value === true) {
		LCKaart.getMap().addOverlay(geoXMLcontractors);
		getVars[2] = "contractors";
	} else {
		LCKaart.getMap().removeOverlay(geoXMLcontractors);
		getVars[2] = "";
	}
});

var downloadKMLbutton = new Ext.Button({
	text : 'Download KML',
	iconCls : 'icon-disk_download',
	handler : function () {
		var getVarsStr = getVars.join('&'), url = 'http://reserveren.wheels4all.nl/leontest/KML/genKML.php?' + getVarsStr;
		window.open(url);
	}
});

var mapsBottomBar = new Ext.Toolbar({
	items: [
		showCarsBox, ' ',
		showPersonsBox, ' ',
		showContractorsBox, ' ',
		'->',
		downloadKMLbutton
	]
});

LCMapWindow = new Ext.Window({
	layout: 'fit',
	title: 'Kaart',
	closeAction: 'hide',
	iconCls : 'icon-map',
	width: 1000,
	height: 700,
	x: 40,
	y: 60,
	tbar: mapsBottomBar,
	items: LCKaart
});

var showMapWindow = function () {
	var mapControl = new GMapTypeControl(), zoomControl = new GSmallMapControl();
	LCMapWindow.show();
	//LCKaart.getMap().disableDragging();
	LCKaart.getMap().enableScrollWheelZoom();
	LCKaart.getMap().addControl(mapControl);
	LCKaart.getMap().addControl(zoomControl);
};

