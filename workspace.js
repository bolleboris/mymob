Ext.ns("W4ABackOffice");

//Vertaling van de knoppen van Ext.Messagebox
Ext.MessageBox.buttonText.ok = "OK";
Ext.MessageBox.buttonText.yes = "Ja";
Ext.MessageBox.buttonText.no = "Nee";
Ext.MessageBox.buttonText.cancel = "Annuleren";

var handleProxyException = function (proxy, type, action, response, args) {
	if (type === 'remote') {			//Server gaf een correcte JSON string terug
		Ext.MessageBox.alert('Fout', "De server gaf de volgende fout:<br><i>" + response.reader.jsonData.msg + "</i>");
	} else {								//Server gaf geen correct antwoord (404, 500, ongeldige JSON string, of geen metaData)
		Ext.MessageBox.alert('Fout', "Ongeldig antwoord van server ontvangen.");
	}
};

var autoMenu = {
	text : 'Auto\'s',
	iconCls : 'icon-car',
	menu : [
		{
			text : 'Auto overzicht',
			iconCls : 'icon-car_red',
			handler: function () {
				autoLijstWindow.show();
				autoStore.load();
			}
		}, {
			text : 'Stilstaande autos',
			iconCls : 'icon-car_stop',
			handler: function () {
				stilstaandeAutosWindow.show();
				stilstaandeAutosStore.load();
			}
		},	{
			text : 'Bezettingsgraad',
			iconCls : 'icon-chart_curve',
			handler: function () {
				autoBezettingWindow.show();
				autoBezettingStore.load();
			}
		}
	]
};

var personenMenu = {
	text : 'Personen',
	iconCls : 'icon-user',
	menu : [
		{
			text : 'Persoon zoeken',
			iconCls : 'icon-user_magnify',
			handler: function () {
			    showUserSearchBox(showPersonDetails);
			}
		},	{
			text : 'Persoon toevoegen',
			iconCls : 'icon-user_add',
			handler: function() {
			   showCreatePerson();
			}
		},	{
			text : 'Beheerders',
			iconCls : 'icon-user_key',
			disabled: true
		},	{
			text : 'Superusers',
			iconCls : 'icon-user_gray_cool',
			disabled: true
		},	{
			text : 'Contracten',
			iconCls : 'icon-page_white_text',
			handler: function () {
				contractLijstWindow.show();
				contractLijstStore.load();
			}
		},	{
			text : 'Passen',
			iconCls : 'icon-creditcards',
			disabled: true
		},	{
			text : 'Nieuwe passen',
			iconCls : 'icon-creditcards',
			handler: function () {
				nieuwePassenWindow.show();
				nieuwePassenStore.load();
			}
		}
	]
};

var CCOMMenu = {
	text : 'CCOM',
	iconCls : 'icon-transmit',
	menu : [
		{
			text : 'Auto alarm berichten',
			iconCls: 'icon-error',
			handler: function () {
				alarmWindow.show();
				alarmStore.load();
			}			
		},
		{
			text : 'Boordcomputer versies',
			iconCls : 'icon-car_red',
			handler: function () {
				autoFWWindow.show();
				autoFWStore.load();
			}
		},
		{
			text : 'Status SMS berichten',
			iconCls : 'icon-mail',
			handler: function () {
				smsStatusStore.load();
				smsStatusWindow.show();
			}
		},
		{
			text : 'Realtime log',
			iconCls : 'icon-application_xp_terminal',
			disabled: true
		},
		{
			text : 'Status daemon',
			iconCls : 'icon-server_connect',
			disabled: true
		},
		{
			text : 'Stuur pasdetectie',
			iconCls : 'icon-creditcards',
			disabled: true
		}
	]
};

var reserveringenMenu = {
	text : 'Reserveringen',
	iconCls : 'icon-calendar',
	menu : [
		{
			text : 'Reservering zoeken',
			iconCls : 'icon-calendar_select_day',
			disabled: true
		},
		{
			text : 'Reservering maken',
			iconCls : 'icon-calendar_add',
			handler: function() {
			   creeerBoekingWindow.show();
			}
		},
		{
			text : 'Lange reserveringen',
			iconCls : 'icon-calendar_select_week',
			disabled: true
		}
	]
};

var rittenMenu = {
	text : 'Ritten',
	iconCls : 'icon-car_start',
	menu : [
		{
			text : 'Lopende ritten',
			iconCls : 'icon-car_start',
			handler: function () {
				lopendeRittenWindow.show();
				lopendeRittenStore.load();
			}	
		},
		{
			text : 'Lange ritten',
			iconCls : 'icon-car_error',
			disabled: true
		},
		{
			text : 'Noodopeningen',
			iconCls : 'icon-key',
			disabled: true
		},
		{
			text : 'Ritstaten invullen',
			iconCls : 'icon-book_open',
			disabled: true
		},
		{
			text : 'Ritten in aanvraag',
			iconCls : 'icon-accept',
			handler: function () {
				aanvraagRittenWindow.show();
				aanvraagRittenStore.load();
			}	
		}
	]
};

var databaseMenu = {
	text : 'Database',
	iconCls : 'icon-database',
	menu : [
		{
			text : 'Kilometer check',
			iconCls : 'icon-database_gear',
			handler: function () {
				KMStandenWindow.show();
			}
		},
		{
			text : 'Backup status',
			iconCls : 'icon-database_copy',
			disabled: true
		}
	]
};

var mapsMenu = {
	text : 'Kaart',
	iconCls : 'icon-map',
	handler: function () {
		showMapWindow();
	}
};


W4ABackOffice.workspace = function () {
	var userLevel, userName, viewport, EmployeeCardPanel, CoordinatorCardPanel, loginWindow, cookieUtil = Ext.util.Cookies;

	return {
		init : function () {
			/*if (! cookieUtil.get('loginCookie')) {
				if (! loginWindow) {
					loginWindow = this.buildLoginWindow();
				}
				loginWindow.show();
			}else{
				this.buildViewport();
			}*/

			if (!loginWindow) {
				loginWindow = this.buildLoginWindow();
			}
			loginWindow.show();
			//this.onLoginSuccess();
		},

		buildLoginWindow : function () {
			return new TKE.window.UserLoginWindow({
				scope   : this,
				handler : this.onLogin
			});
		},
	
		buildViewport : function () {
			employeeTopbar = new Ext.Toolbar({
				items: [
					autoMenu,
					'-',
					personenMenu,
					'-',
					CCOMMenu,
					'-',
					reserveringenMenu,
					'-',
					rittenMenu,
					'-',
					databaseMenu,
					'-',
					mapsMenu,
					'->',
					{
						text     : 'Afmelden',
						iconCls  : 'icon-door_out',
						scope    : this,
						//handler  : this.onLogOut			//Are you sure venster
						handler	: this.doLogOut		//Direct uitloggen
					}
				]
			});

			coordinatorTopbar = new Ext.Toolbar({
				items: [
					{
						text : 'Ledenlijst',
						iconCls : 'icon-user',
						handler: function () {
							ledenLijstWindow.show();
							ledenLijstStore.load();
						}
					}, '-', {
						text : 'Autolijst',
						iconCls : 'icon-car',
						handler: function () {
							autoLijstWindow.show();
							autoStore.load();
						}
					}, '-', {
						text : 'Bezettingsgraad',
						iconCls : 'icon-chart_curve',
						handler: function () {
							autoBezettingWindow.show();
							autoBezettingStore.load();
						}
					}, '-', {
						text : 'Stilstaande autos',
						iconCls : 'icon-car_stop',
						handler: function () {
							stilstaandeAutosWindow.show();
							stilstaandeAutosStore.load();
						}
					}, '-', {
						text : 'Kaart',
						iconCls : 'icon-map',
						handler: function () {
							showMapWindow();
						}
					},	'->',	{
						text     : 'Afmelden',
						iconCls  : 'icon-door_out',
						scope    : this,
						//handler  : this.onLogOut			//Are you sure venster
						handler	: this.doLogOut		//Direct uitloggen
					}
				]
			});
		
			bottomBar = new Ext.Toolbar({
				items: [
					'Ingelogd als: ' + userName,
					'->',
					"Wheels4All backoffice V0.2"
				]
			});

			EmployeeCardPanel = new Ext.Panel({
				layout     : 'card',
				activeItem : 0,
				border     : false,
				defaults   :  { workspace : this },
				tbar : employeeTopbar,
				bbar : bottomBar
			});


			CoordinatorCardPanel = new Ext.Panel({
				layout     : 'card',
				activeItem : 0,
				border     : false,
				defaults   :  { workspace : this },
				tbar : coordinatorTopbar,
				bbar : bottomBar
			});

			viewport = new Ext.Viewport({
				layout : 'fit'
				//items  : cardPanel			//Items worden verderop ingevoegd, afhankelijk van userlevel
			});
			
			Ext.getBody().unmask();						
		},

		onLogin :  function () {
			var form = loginWindow.get(0);
			if (form.getForm().isValid()) {
				loginWindow.el.mask('Bezig met inloggen...', 'x-mask-loading');
				form.getForm().submit({
					success : this.onLoginSuccess,
					failure : this.onLoginFailure,
					scope   : this
				});
			}
		},

		onLoginSuccess : function (form, action) {	
			if (! loginWindow) {
				loginWindow = this.buildLoginWindow();
			}
			if (loginWindow.el){
				loginWindow.el.unmask();
			}
			var jsonData = Ext.decode(action.response.responseText);
			var cookie = cookieUtil.get('loginCookie');
			if (cookie) {
				userName = jsonData.username;
				this.buildViewport();
				loginWindow.destroy();
				loginWindow = null;
				userLevel = jsonData.userlevel;
				if (userLevel === 2) {
					autoLijstWindowTopBar.get('autoLijstWindowTopBarWijzigButton').disable();
					autoLijstGridCtxMenu.get('autoLijstGridCtxMenuWijzigButton').disable();
					stilstaandeAutosWindowTopBar.get('stilstaandeAutosWindowTopBarWijzigButton').disable();
					stilstaandeAutosGridCtxMenu.get('stilstaandeAutosGridCtxMenuWijzigButton').disable();
					resPerAutoWindowTopBar.disable();
					resPerAutoGridCtxMenu.destroy();			//Een disable maakt het menu doorzichtig, maar wel klikbaar??
					downloadKMLbutton.disable();
				//	showContractorsBox.disable();
					autoBezettingWindowTopBar.get('autoBezettingWindowTopBarWijzigButton').disable();
					autoBezezttingGridCtxMenu.get('autoBezezttingGridCtxMenuWijzigButton').disable();
					autoBezettingExportButton.disable();

					viewport.add(CoordinatorCardPanel);
					viewport.doLayout();				
				} else {
					viewport.add(EmployeeCardPanel);

					viewport.doLayout();				
				}
			} else {
				this.onLoginFailure();
			}
		},

		onLoginFailure : function (form, action) {
			var jsonData = Ext.decode(action.response.responseText);
			loginWindow.el.unmask();
			Ext.MessageBox.alert('Fout', "Fout: " + jsonData.msg);
		},

		onLogOut : function () {
			Ext.MessageBox.confirm('Afmelden', 'Weet je zeker dat je jezelf wilt afmelden?',
				function (btn) {
					if (btn === 'yes') {
						this.doLogOut();
					}
				},
					this
				);
		},
      
		doLogOut : function () {
			Ext.getBody().mask('Bezig met afmelden...', 'x-mask-loading');
			Ext.Ajax.request({
				url          : 'userlogout.php',
				params       : { user : cookieUtil.get('loginCookie') },
				scope        : this,
				callback     : this.onAfterAjaxReq,
				succCallback : this.onAfterLogout
			});
		},
		
		onAfterLogout : function () {
			this.destroy();
		},

		onSwitchPanel : function (btn) {
			var xtype  = btn.itemType,
				panels = cardPanel.findByType(xtype),
				newPanel = panels[0];

			var newCardIndex = cardPanel.items.indexOf(newPanel);
			this.switchToCard(newCardIndex);
		},

		switchToCard : function (newCardIndex) {
			var layout         = cardPanel.getLayout(),
				activePanel    = layout.activeItem,
				activePanelIdx = cardPanel.items.indexOf(activePanel);

			if (activePanelIdx !== newCardIndex) {
				var  newPanel = cardPanel.items.itemAt(newCardIndex);

				layout.setActiveItem(newCardIndex);

				if (newPanel.cleanSlate) {
					newPanel.cleanSlate();
				}
			}
		},

		onAfterAjaxReq : function (options, success, result) {
			Ext.getBody().unmask();
			if (success === true) {
				var jsonData;
				try {
					jsonData = Ext.decode(result.responseText);
				} catch (e) {
					Ext.MessageBox.alert('Error!', 'Data returned is not valid!');
				}
				options.succCallback.call(options.scope, jsonData, options);
			} else {
				Ext.MessageBox.alert('Error!', 'The web transaction failed!');
			}
		},

		destroy : function () {
			viewport.destroy();
			viewport  = null;
			cardPanel = null;
			this.init();
		}
	};
}();

Ext.onReady(W4ABackOffice.workspace.init, W4ABackOffice.workspace);
