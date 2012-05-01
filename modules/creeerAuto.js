var createResourceForm = new Ext.FormPanel({
	 id: 'createResourceForm',
	 bodystyle: 'padding: 0px 0px 0px 0px',
	 title: 'Creer nieuwe auto',
	 columnWidth: 0.7,
	 items: [
	 {
		 xtype: 'numberfield',
		 fieldLabel: 'Supplier \n (100 = MyWheels Supplier)',
		 name: 'supplierId',
		 dataIndex: 'supplierId',
		 value: 100,
		 allowBlank: false

	 },{
		 xtype:'textfield',
		 fieldLabel: 'Kenteken',
		 name: 'kenteken',
		 dataIndex: 'kenteken',
		 allowBlank: false
	 },{
		 xtype:'numberfield',
		 fieldLabel: 'Vloot',
		 name: 'vloot',
		 dataIndex: 'vloot',
		 allowBlank: false
	 },{
		 xtype: 'textfield',
		 fieldLabel: 'Basale Informatie over Auto',
		 dataIndex: 'info',
		 name: 'info',
		 allowBlank: true
	 }
	 ]
});

var createResourceWindowBottomBar = new Ext.Toolbar({			//autoDetailWindowBottomBar voor main window
   items : [
	'->',
	{
		iconCls : 'icon-car_add',
		text: 'Maak!',
		handler: function(){
			Ext.Msg.show({
			   title:'Ongedaan maken',
			   msg: 'Weet je zeker dat je een nieuwe auto aan wil maken?',
			   buttons: Ext.Msg.YESNO,
			   fn: function(e){
					if(e == 'yes') createResourceForm.getForm().submit(
					{
					   url:'./modules/api/createResource.php',
					   success: function(form, action) {
						  Ext.Msg.show({
							 title: 'Auto is aangemaakt!',
							 msg: 'De Auto is succesvol aangemaakt! Klik op OK om al de gegevens in te vullen',
							 buttons: Ext.Msg.OK,
							 fn: function(e) {
								showCarDetails(action.result.id);
								createResourceWindow.hide();
							 }
						  })
						  
					}});
				},
			   animEl: 'header',
			   icon: Ext.MessageBox.QUESTION,
			   scope:this
			})
		}
	}]
});

var createResourceWindow = new Ext.Window({
	title: 'Creeer auto',
	width: 800,
	height: 430,
	closable: true,
	closeAction: 'hide',
	layout: 'fit',
	iconCls: 'icon-car',
	items: createResourceForm,
	bbar: createResourceWindowBottomBar
});

var showCreateResourceWindow = function() {
   createResourceWindow.show();
   //createResourceForm.getForm().empty() // empty form
}
