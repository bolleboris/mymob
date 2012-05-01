var createResourceForm = new Ext.FormPanel({
	 id: 'createResourceForm',
	 bodystyle: 'padding: 0px 0px 0px 0px',
	 title: 'Creer nieuwe auto',
	 columnWidth: 0.7,
	 items: [
	 {
		 xtype: 'numberfield',
		 fieldLabel: 'Supplier',
		 name: 'supplierId',
		 dataIndex: 'supplierId',
		 allowBlank: false

	 },{
		 xtype:'textfield',
		 fieldLabel: 'Kenteken',
		 name: 'kenteken',
		 dataIndex: 'kenteken',
		 allowBlank: false
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
					if(e == 'yes') createResourceForm.getForm().load({url:'./modules/api/createResource.php'});
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
