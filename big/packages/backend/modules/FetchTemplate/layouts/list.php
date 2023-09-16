<script type="text/javascript" language="javascript">
Ext.onReady(function(){
    Ext.QuickTips.init();
    var tabs = new Ext.FormPanel({
		xtype:"form",
        layout: 'form',
		items:[{
			store: new Ext.data.SimpleStore({
				 fields: ['value','display']
				,data: [[[|data|]]]
			}),
			editable: true,
			mode: 'local',
			displayField: 'display',
			valueField : 'value',
			allowBlank :false,
			triggerAction: 'all',
			xtype:"combo",
			fieldLabel:"[[.template.]]",
			name:"template"
		  },{
			xtype:"textfield",
			width:400,
			allowBlank :false,
			fieldLabel:"[[.Url.]]",
			name:"link"
		  },{
			xtype:"hidden",
			width:400,
			allowBlank :false,
			fieldLabel:"[[.Url.]]",
			name:"link_1"
		  }
		  ],
	     bodyStyle:'padding:15px 15px 15px 5px 10px',
		 style:'padding:10px',
		 buttons: [
		 {
            text: '[[.fetch.]]',
			handler:function(){
				tabs.form.submit(
				{
					waitMsg: '[[.fetching.]]',
					url:'?page=<?php echo  DataFilter::removeXSSinHtml(Url::get('page'));?>',
					params:{
						cmd:'declaration'
					}
				});
			}
         },
		 {
            text: '[[.Cancel.]]',
			handler:function(){
			}
         }
		 ]
	  });
 tabs.render($('content'));
});
</script>
<div id="content"></div>
