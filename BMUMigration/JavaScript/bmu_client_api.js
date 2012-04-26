var BMUCore = {
	bmu_rmi : '0.0',
	namespace : 'Core',
	invocation : '',
	invocationStack : [],
	pushCall : function (callString) {
		"use strict";
		this.invocationStack.push(callString);
	},
	buildRequest:	function () {
		"use strict";
		var invocationString = this.buildInvocation();
		return '{"bmu_rmi":"' + this.bmu_rmi + '","namespace":"' + this.namespace + '","invocation":"' + invocationString + ((invocationString.charAt(invocationString.length - 1) === ']')? '' : '"') + '}';
	},
	buildInvocation : function () {
		"use strict";
		var tGlue = '',
			glue = '.',
			output = '',
			i;
		for (i = 0; i < this.invocationStack.length; i += 1) {
			output = output + tGlue + this.invocationStack[i];
			tGlue = glue;
		}
		this.flushRequest();
		return output;

	},
	flushRequest : function () {
		"use strict";
		this.invocationStack = [];
	},	
	Application : function () {
		"use strict";
		BMUCore.pushCall('Application');
		return this.ApplicationC;
	},	
	ApplicationC : {
		Connect : function (key) {
			"use strict";	
			BMUCore.pushCall("Connect('" + key + "')");
		},
		Disconnect : function() {
			BMUCore.pushCall("Disconnect()");
		}
	},	
	ProviderUI : function (id) {
		"use strict";
		BMUCore.pushCall('ProviderUI(' + id + ')');
		return this.ProviderUIC;
	},	
	ProviderUIC : {
		SubscribePerson : function (email) {
			"use strict";
			BMUCore.pushCall("SubscribePerson('" + email + "')'");
		},		
		SubscriptionConfirm : function (key) {
			BMUCore.pushCall("SubscriptionConfirm('" + key + "')");
		},
		SubscriptionComplete : function (key, password) {
			BMUCore.pushCall("SubscriptionComplete('" + key + "','" + password + "')");
		},
		LoginUser : function (email, authentication) {
			BMUCore.pushCall("LoginUser('" + email + "','" + authentication + "')");
		},
		Person : function (id) {
			BMUCore.pushCall("Person(" + id + ")");
			return BMUCore.PersonC;
		},
		Consumer : function (id) {
			BMUCore.pushCall("Consumer(" + id + ")");
			return BMUCore.ConsumerC;
		},
		Customer : function (id) {
			BMUCore.pushCall("Customer(" + id + ")");
			return BMUCore.CustomerC;
		},
		
		Blaat : function () {
			console.log('Blaaaaaaat');
		}
	},	
	AttributesC : {
		List : function () {
			BMUCore.pushCall("List()");
		},
		UpdateList : function(itemArray) {
			var i,
				output = 'UpdateList(attributes),"attributes":"[';
				tGlue = '',
				glue = ',';			
			for(i = 0; i < itemArray.length; i++) {
				output += tGlue + '{"group":"' + itemArray[i].group + '",' + 
							'"key":"' + itemArray[i].key + '",' +
							'"value":"' + itemArray[i].value + '",' + 
							'"access":"' + itemArray[i].access + '" }';
			}			
			output += ']';
			
			BMUCore.pushCall(output);			
		},
		
		CreateList : function(itemArray) {
			var i,
				output = 'CreateList(attributes),"attributes":"[';
				tGlue = '',
				glue = ',';			
			for(i = 0; i < itemArray.length; i++) {
				output += tGlue + '{"group":"' + itemArray[i].group + '",' + 
							'"key":"' + itemArray[i].key + '",' +
							'"value":"' + itemArray[i].value + '",' + 
							'"access":"' + itemArray[i].access + '" }';
			}			
			output += ']';
			
			BMUCore.pushCall(output);			
		},
		
	},	
	PersonC : {
		Attributes : function() {
			BMUCore.pushCall("Attributes");
			return BMUCore.AttributesC;
		},
		List : function () {
			BMUCore.pushCall("List()");
		},		
		Info : function (options) {
			BMUCore.pushCall("Info('" + options + "')");
		},		
		RoleList : function () {
			BMUCore.pushCall("RoleList()");
		}
	},

	ConsumerC : {
		Attributes : function() {
			BMUCore.pushCall("Attributes");
			return BMUCore.AttributesC;
		},
		List : function () {
			BMUCore.pushCall("List()");
		},		
		Info : function (options) {
			BMUCore.pushCall("Info('" + options + "')");
		},		
		RoleList : function () {
			BMUCore.pushCall("RoleList()");
		}
	},

	
	CustomerC : {
		Attributes : function() {
			BMUCore.pushCall("Attributes");
			return BMUCore.AttributesC;
		},
		List : function () {
			BMUCore.pushCall("List()");
		},		
		Info : function (options) {
			BMUCore.pushCall("Info('" + options + "')");
		},		
		RoleList : function () {
			BMUCore.pushCall("RoleList()");
		}
	}	
}
