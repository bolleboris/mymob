Ext.ux.userSearchBox = Ext.extend(Ext.form.ComboBox, {
   initComponent: function() {
	  Ext.ux.userSearchBox.superclass.initComponent.call(this);

	  this.store =  new Ext.data.Store({
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
	  });//this.store.on('exception',handleProxyException);

	  this.on('render', function() { console.log('expanding!')});
   },
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
	// applyTo: Ext.get('searchInHTML'),
	tpl: new Ext.XTemplate(
    '<tpl for="."><div class="search-item">',
   //     '<table border=0 style=\'font-size:12;\'><tr><td colspan=2><b>{Achternaam}, {Initialen} ({Voornaam})</b></td><td width=125>{Woonplaats}</td></tr>',
	     '<table border=0 style=\'font-size:12;\'><tr><td colspan=2><b>{Achternaam}, {Voornaam} ({userId})</b></td><td width=125>{Woonplaats}</td></tr>',
         '<tr><td width=100>{Telefoon1}</td><td width=100>{Telefoon2}</td><td width=125>{Email}</td></tr></table>',
    '</div></tpl>'
   ),
	//applyTo: Ext.get(this),// Ext.get('searchInWindow'),
	itemSelector: 'div.search-item',
	setCallBack: function(func) {
	   this.searchCallBack = func;
	},
	searchCallBack: function(BMUNr, W4ANr) {
   console.log('Default searchBox callback called', BMUNr, W4ANr);
   },
	onSelect: function(record){ // override default onSelect to do redirect
		if(record.data.userId){		//Alleen klikbaar als er werkelijk result is
		    this.searchCallback(record.data.userId,record.data.W4APersonNr);
			//this.collapse();
		}
	}
});