var KMStandenWindow = new Ext.Window({
	id: 'KMStandenWindow',
	title: 'Kilometerstanden check',
	closable: true,
	closeAction: 'hide',
	autoScroll: true,
	iconCls : 'icon-database_gear',
	width: 400,
	height: 450,
	layout: 'fit',
	autoLoad: {
		url: './modules/api/KMStanden.php',
		scripts: false
	},
	bbar: new Ext.Toolbar({
		items: [
			'->',
			new Ext.Button({
				text: 'Vernieuw',
				iconCls: 'icon-arrow_refresh',
				handler: function(){
					this.findParentByType('window').load({url: './modules/api/KMStanden.php'});
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
	})
})
