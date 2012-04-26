Ext.ns("TKE.window");

TKE.window.UserLoginWindow = Ext.extend(Ext.Window, {
	initComponent : function() {
		// Force defaults
		Ext.apply(this, {
			width     : 450,
			height    : 200,
			modal     : true,
			draggable : false,
			title     : 'Inloggen op de Wheels4All backoffice',
			layout    : 'fit',
			center    : true,
			closable  : false,
			resizable : false,
			border    : false,
			items     : this.LoginForm(),
			buttons   : [
				{
					text    : 'Inloggen',
					handler : this.handler || Ext.emptyFn,
					scope   : this.scope || this
				}
			]
		});
		TKE.window.UserLoginWindow.superclass.initComponent.call(this);
	},
	//private builds the form.
	LoginForm : function() {
		var formItemDefaults = {
			allowBlank : false,
			anchor     : '-5',
			listeners  : {
				scope      : this,
				specialkey : function(field, e) {
					if (e.getKey() === e.ENTER && this.handler) {
						this.handler.call(this.scope);
					}
				}
			}
		};

		return {
			xtype       : 'form',
			defaultType : 'textfield',
			labelWidth  : 150,
			frame       : true,
			url         : 'userlogin.php',
			labelAlign  : 'right',
			defaults    : formItemDefaults,
			items       : [
				{
					xtype: 'box',
					autoEl: { 
						tag: 'div',
						html: '<div class="app-msg"><img src="images/key-icon.png" class="app-img" />Inloggen op de Wheels4All backoffice</div>'
					}
				},{
					fieldLabel : 'Naam',
					name       : 'user'
				},{
					inputType  : 'password',
					fieldLabel : 'Wachtwoord',
					name       : 'password'
				}
			]
		};
	}
});
