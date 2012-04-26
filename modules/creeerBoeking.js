/*var resourceBoekingGridPanel = new Ext.grid.GridPanel({
   store: new Ext.data.Store({
        autoDestroy: true,
        reader: reader,
        data: xg.dummyData
    }),
    colModel: new Ext.grid.ColumnModel({
        defaults: {
            width: 120,
            sortable: true
        },
        columns: [
            {id: 'company', header: 'Company', width: 400, sortable: true, dataIndex: 'company'},
            {header: 'Price', renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
            {header: 'Change', dataIndex: 'change'},
            {header: '% Change', dataIndex: 'pctChange'},
            // instead of specifying renderer: Ext.util.Format.dateRenderer('m/d/Y') use xtype
            {
                header: 'Last Updated', width: 135, dataIndex: 'lastChange',
                xtype: 'datecolumn', format: 'M d, Y'
            }
        ]
    })
})*/

var miscellaneousBoekingFormPanel = new Ext.FormPanel({
   title: 'Misc',

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
	 name: 'eindDate',
	 dataIndex: 'eindDate'
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
					if(e == 'yes') miscellaneousBoekingFormPanel.getForm().submit({url:'./modules/api/createBooking.php'});
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
	height: 900,
	autoHeight: true,
	closable: true,
	closeAction: 'hide',
	layout: 'column',
	iconCls: 'icon-user',
	items: [miscellaneousBoekingFormPanel],
	bbar: creeerBoekingWindowBottomBar
});

var showCreeerBoekingWindow = function(){
	creeerBoekingWindow.show();
};
