var userSearchWindow = new Ext.Window({
	title: 'Zoek een persoon',
	layout: 'fit',
	width: 425,	//Ondanks 'fit' layout, moet hier wel een width staan, anders verprutst IE het...
	x: 40,		//We zetten hier een X en Y, omdat het vervelend is dat dit venster bij hoge resoluties zover van het menu wordt gerenderd
	y: 60,
	closable: true,
	resizable: false,
	closeAction: 'hide',
	iconCls: 'icon-magnifier',
	items: new Ext.ux.userSearchBox()
});
userSearchWindow.show();
