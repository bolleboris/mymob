var userSearchStore = new Ext.data.Store({
	url: './modules/api/searchUsers.php',
	reader: new Ext.data.JsonReader({
		root: 'rows',
		totalProperty: 'results',
		id: 'post_id'
	}, [
		{name: 'userId'},
		{name: 'Voornaam'},
		{name: 'Achternaam'},
		{name: 'Initialen'},
		{name: 'Woonplaats'},
		{name: 'Telefoon1'},
		{name: 'Telefoon2'},
		{name: 'Email'},
		{name: 'W4APersonNr'}
	])
});

userSearchStore.on('exception',handleProxyException);

// Custom rendering Template
var userSearchResultTpl = new Ext.XTemplate(
    '<tpl for="."><div class="search-item">',
   //     '<table border=0 style=\'font-size:12;\'><tr><td colspan=2><b>{Achternaam}, {Initialen} ({Voornaam})</b></td><td width=125>{Woonplaats}</td></tr>',
	     '<table border=0 style=\'font-size:12;\'><tr><td colspan=2><b>{Achternaam}, {Voornaam} ({userId})</b></td><td width=125>{Woonplaats}</td></tr>',
         '<tr><td width=100>{Telefoon1}</td><td width=100>{Telefoon2}</td><td width=125>{Email}</td></tr></table>',
    '</div></tpl>'
);

var userSearchBox = new Ext.form.ComboBox({
	store: userSearchStore,
	//displayField:'Achternaam',
	typeAhead: false,
	loadingText: 'Zoeken...',
	emptyText: 'Geef tenminste 3 letters van de achternaam',
	minChars : 2,
	width: 425,
	height: 400,
	listEmptyText: 'Geen personen gevonden...',
	//pageSize:10,		//Zet dit aan, en je krijgt een paging bar.
	hideTrigger:true,
	tpl: userSearchResultTpl,
	// applyTo: Ext.get('searchInHTML'),
	//applyTo: Ext.get('searchInWindow'),
	itemSelector: 'div.search-item',

	onSelect: function(record){ // override default onSelect to do redirect
		if(record.data.userId){		//Alleen klikbaar als er werkelijk result is
		    userSearchBoxCallback(record.data.userId,record.data.W4APersonNr);
			this.collapse();
			userSearchWindow.hide();
		}
	}
});

var userSearchBoxCallback = function(BMUNr, W4ANr) {
   console.log('Default searchBox callback called', BMUNr, W4ANr);
}

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
	items: userSearchBox
});

var showUserSearchBox = function (callbackFunc) {
				userSearchBox.clearValue();	//Zorg dat de oude naam er niet in staat.
				userSearchWindow.show();
				userSearchBox.focus(true, true);		//Focus op de box, en selecteer eventuele text, zodat je meteen kan invoeren
															//Er wordt hier 10msec delay gegenereerd (2e var op true) zodat de box tijd heeft om te renderen (met name voor FF en IE)
			    userSearchBoxCallback = callbackFunc;
}