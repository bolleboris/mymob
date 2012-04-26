var createPersonForm = new Ext.FormPanel({
	    id: 'createPersonForm',
	    bodystyle: 'padding: 0px 0px 0px 0px',
		title: 'Creer nieuw persoon',
	    columnWidth: 0.7,
	    items: [{
		    xtype: 'textfield',
			fieldLabel: 'Voornaam',
			name: 'Name_FirstName_Public',
			anchor: '100%',
			allowBlank: false
		}, {
		    xtype: 'textfield',
		    fieldLabel: 'Achternaam',
		    name: 'Name_SurName_Public',
		    anchor: '100%',
		    allowBlank: false
		}, {
		    xtype: 'textfield',
		    fieldLabel: 'Email',
		    name: 'General_EmailAddress_Public',
		    anchor: '100%',
		    allowBlank: false
		},{
			xtype: 'textfield',
			fieldLabel: 'Loginnaam',
			name: 'entitykey',
			anchor: '100%',
			allowBlank: false
		},{
			xtype: 'textfield',
			fieldLabel: 'Wachtwoord',
			name: 'authentication',
			anchor: '100%',
			allowBlank: false
		}
	  ]
});

var createPersonWindowBottomBar = new Ext.Toolbar({			//personDetailWindowBottomBar voor main window
   items : [{
	   iconCls : 'icon-user_add',
	   text: 'Creeer Persoon',
	   handler: function(){
			console.log('Zend persoon');
			createPersonForm.getForm().submit({
				clientValidation : true,		//alleen submitten wanneer de form geen fouten bevat!
				url:'./modules/api/createPerson.php',
				waitMsg: 'Bezig met opslaan...',
				success: function(form, action){
				    console.log(action);					
				    createPersonWindow.hide();
					Ext.Msg.show({
					   title:'Opgeslagen',
					   msg: 'De persoon is succesvol gecreeerd!',
					   icon: Ext.MessageBox.INFO,
					   buttons: Ext.Msg.OK,
					   scope:this
					});
					showPersonDetails(action.result.id);
				},
				failure: function(form,action){
					if (action.failureType == 'client') {
						Ext.Msg.show({
						   title:'Fout',
						   msg: 'Je hebt het formulier niet correct ingevuld.<br>Verbeter de velden met rode onderstreping.',
						   icon: Ext.MessageBox.ERROR,
						   buttons: Ext.Msg.OK,
						   scope:this
						});
					}else{
						Ext.Msg.show({
						   title:'Fout',
						   msg: 'Er is iets fout gegaan.<br>De gegevens zijn niet opgeslagen!<br>Failure: '+action.failureType,
						   icon: Ext.MessageBox.ERROR,
						   buttons: Ext.Msg.OK,
						   scope:this
						});
					}
				}
			})

	   }
	},'->',{
	//	iconCls : 'icon-arrow_refresh',
		iconCls : 'icon-cross',
		text: 'Sluit',
		handler: function(){
		   console.log('Sluit scherm');
		   createPersonForm.getForm().reset();
		   createPersonWindow.hide();
		}
		
	}]
});

var createPersonWindow = new Ext.Window({
	title: '',
	width: 800,
	autoHeight: true,
	closable: true,
	closeAction: 'hide',
	layout: 'column',
	iconCls: 'icon-car',
	items: [createPersonForm],
	bbar:	createPersonWindowBottomBar
});

var showCreatePerson = function(){
   	createPersonForm.getForm().reset();	
	createPersonWindow.show(this);
};