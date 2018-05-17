var Tools = {
	ajaxCall : function(target, options){
		console.log(options);
		defaultOptions = {cache: false, dataType: 'json',type: 'post'};
		options = ArrayTools.merge(defaultOptions, options);
		if(typeof(options.data)==="undefined"){
			options.data={};
		}
		options.data.ajax = "1";
		console.log(options);
		$.ajax(options);
	},
	/*ajaxCallQuick : function(target, showModalLoader, successCallback, errorCallback){
		defaultOptions = {cache: false, dataType: 'json',type: 'post'};
		options = ArrayTools.merge(defaultOptions, options);
	},*/
};