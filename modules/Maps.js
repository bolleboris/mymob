var LCKaart = new Ext.ux.GMapPanel({
	zoomLevel: 8,
	gmapType: 'map',
	setCenter: {
		lat: 52.089543,
		lng: 5.109744
	}
});

var geoXMLcars = new google.maps.KmlLayer('https://www.wheels4all.nl/reserveren/mymob_maps/BOgenKML.php?cars',{preserveViewport: true});
var geoXMLpersons = new google.maps.KmlLayer('https://www.wheels4all.nl/reserveren/mymob_maps/BOgenKML.php?persons',{preserveViewport: true});
var geoXMLcontractors = new google.maps.KmlLayer('https://www.wheels4all.nl/reserveren/mymob_maps/BOgenKML.php?contractors',{preserveViewport: true});

var getVars = [];

var showCarsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon autos',
	checked : false
});

showCarsBox.on('check', function (box, value) {
	if (value === true) {
		geoXMLcars.setMap(LCKaart.getMap());
		getVars[0] = "cars";
	} else {
		geoXMLcars.setMap(null);
		getVars[0] = "";
	}
});

var showPersonsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon personen',
	checked : false
});

showPersonsBox.on('check', function (box, value) {
	if (value === true) {
		geoXMLpersons.setMap(LCKaart.getMap());
		getVars[1] = "persons";
	} else {
		geoXMLpersons.setMap(null);
		getVars[1] = "";
	}
});

var showContractorsBox = new Ext.form.Checkbox({
	boxLabel: 'Toon contractanten',
	checked : false
});

showContractorsBox.on('check', function (box, value) {
	if (value === true) {
		geoXMLcontractors.setMap(LCKaart.getMap());
		getVars[2] = "contractors";
	} else {
		geoXMLcontractors.setMap(null);
		getVars[2] = "";
	}
});

var downloadKMLbutton = new Ext.Button({
	text : 'Download KML',
	iconCls : 'icon-disk_download',
	handler : function () {
		var getVarsStr = getVars.join('&'), url = 'https://www.wheels4all.nl/reserveren/maps/BOgenKML.php?' + getVarsStr;
		window.open(url);
	}
});

var mapsBottomBar = new Ext.Toolbar({
	items: [
		showCarsBox, ' ',
		showPersonsBox, ' ',
		//18-04-2011, op verzoek van Ronald de contractantenbox weggehaald.
		//showContractorsBox, ' ',
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
	LCMapWindow.show();
	LCKaart.getMap().setOptions({streetViewControl: false});
};

