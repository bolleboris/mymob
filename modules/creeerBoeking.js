var resourceBoekingStore = new Ext.data.GroupingStore({
	url: './modules/api/searchResources.php',
	reader : new Ext.data.JsonReader()			//Reader configureerd zichzelf met metaData in JSON string
});

var resourceBoekingGridPanel = new Ext.grid.GridPanel({
	frame: false,
	header: false,
	collapsible: true,
	animCollapse: true,
	loadMask:  {msg: "Mogelijk te reserveren auto's worden geladen.."},
	region: 'east',
	store: resourceBoekingStore,
	columns: [
		{header: "AutoId", width: 60, sortable: true, dataIndex: 'AutoId'},
		{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam', groupable: false},
		{header: "Kenteken", width: 70, sortable: true, dataIndex: 'Kenteken', groupable: false},
		{header: "Supplier", width: 100, sortable: true, dataIndex: 'SupplierCode'},
		{header: "Contractant", width: 100, sortable: true, dataIndex: 'CustomerCode'},
		{header: "Service", width: 100, sortable: true, dataIndex: 'service'},
		{header: "Afstand (in meters)", width: 100, sortable: true, dataIndex: 'distance'},
		{header: "ContractId",hidden: true, width: 100, sortable: true, dataIndex: 'contractid'},
		{header: "OfferId",hidden: true, width: 100, sortable: true, dataIndex: 'offerid'},
		{header: "ServiceId",hidden: true, width: 100, sortable: true, dataIndex: 'serviceid'},

	],
	view: new Ext.grid.GroupingView({
		forceFit: true,
		startCollapsed: true,
		columnsText: 'Toon kolommen',
		groupByText: 'Groepeer op dit veld',
		showGroupsText: 'Groepeer',
		sortAscText: 'Sorteer oplopend',
		sortDescText: 'Sorteer aflopend',
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Autos" : "Auto"]})'
	}),
	sm: new Ext.grid.RowSelectionModel({
		singleSelect: true
	}),
	height: 500
});

var miscellaneousBoekingFormPanel = new Ext.FormPanel({
   title: 'Misc',
   region: 'west',
   items: [{
	 xtype: 'numberfield',
	 fieldLabel: 'Persoon ID',
	 name: 'PersonId',
	 dataIndex: 'PersonId'
   },{
	 xtype: 'numberfield',
	 fieldLabel: 'Auto ID',
	 name: 'ResourceId',
	 dataIndex: 'ResourceId'
   },{
	 xtype: 'datefield',
	 fieldLabel: 'Start Datum',
	 name: 'startDate',
	 dataIndex: 'startDate'
   },{
	 xtype: 'timefield',
	 fieldLabel: 'Start Tijd',
	 name: 'startTime',
	 dataIndex: 'startTime'
   },{
	 xtype: 'datefield',
	 fieldLabel: 'Eind Datum',
	 name: 'endDate',
	 dataIndex: 'endDate'
   },{
	 xtype: 'timefield',
	 fieldLabel: 'Eind Tijd',
	 name: 'endTime',
	 dataIndex: 'endTime'
   },{
	 xtype: 'numberfield',
	 fieldLabel: 'Offer ID',
	 name: 'offerId',
	 dataIndex: 'offerId'
   },{
	 xtype: 'numberfield',
	 fieldLabel: 'Contract ID',
	 name: 'contractId',
	 dataIndex: 'contractId'
   },{
	 xtype: 'numberfield',
	 fieldLabel: 'Service ID',
	 name: 'serviceId',
	 dataIndex: 'serviceId'
   }]
});
miscellaneousBoekingFormPanel.getForm().findField('PersonId').on('change',function( field, newValue, oldValue) {
   resourceBoekingStore.load({params:{personId: newValue}});
});

resourceBoekingGridPanel.on('rowclick',function(grid, rowIndex, e) {
   var record = grid.getSelectionModel().getSelected();
   console.log(miscellaneousBoekingFormPanel.getForm().findField('contractid'));
   miscellaneousBoekingFormPanel.getForm().findField('ResourceId').setValue(record.json.AutoId);
   miscellaneousBoekingFormPanel.getForm().findField('contractId').setValue(record.json.contractid);
   miscellaneousBoekingFormPanel.getForm().findField('offerId').setValue(record.json.offerid);
   miscellaneousBoekingFormPanel.getForm().findField('serviceId').setValue(record.json.serviceid);
});

var creeerBoekingWindowBottomBar = new Ext.Toolbar({
   items: [
	  {
		 text: 'Creeer Boeking',
		 iconCls: 'icon-creditcards',
		 handler: function() {
			Ext.Msg.show({
			   title:'Boeking Creeeren',
			   msg: 'Weet je zeker dat je deze boeking wil aanmaken?',
			   buttons: Ext.Msg.YESNO,
			   fn: function(e){
					if(e == 'yes') miscellaneousBoekingFormPanel.getForm().submit({
					   url:'./modules/api/createBooking.php',
					   success: function() {
						  Ext.Msg.show({
						  title:'Succesvol aangemaakt',
						  msg: 'De Reservering is succesvol aangemaakt',
						  icon: Ext.MessageBox.INFO,
						  buttons: Ext.Msg.OK,
						  scope:this
						  });
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
								 msg: 'Er is iets fout gegaan.<br>De reservering is niet aangemaakt!<br>Failure: '+action.failureType,
								 icon: Ext.MessageBox.ERROR,
								 buttons: Ext.Msg.OK,
								 scope:this
							  });
						  }
						}
					  });
				},
			   animEl: 'header',
			   icon: Ext.MessageBox.QUESTION,
			   scope:this
			})
		 }
	  }
   ]
});

var creeerBoekingWindow = new Ext.Window({
	title: 'Creeer Boeking',
	width: 800,
	height: 700,
	closable: true,
	closeAction: 'hide',
	iconCls: 'icon-user',
	items: [miscellaneousBoekingFormPanel,resourceBoekingGridPanel],
	bbar: creeerBoekingWindowBottomBar
});

var showCreeerBoekingWindow = function(){
	creeerBoekingWindow.show();
	
};
