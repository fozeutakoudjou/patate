var Tools = {
	ajaxCall : function(target, options){
		defaultOptions = {cache: false, dataType: 'json',type: 'post'};
		options = ArrayTools.merge(defaultOptions, options);
		if(typeof(options.data)==="undefined"){
			options.data={};
		}
		if(typeof(options.data)==="object"){
			options.data.ajax = "1";
		}else{
			options.data +=((options.data=="") ? "" :"&")+"ajax=1";
		}
		$.ajax(options);
	},
	/*ajaxCallQuick : function(target, showModalLoader, successCallback, errorCallback){
		defaultOptions = {cache: false, dataType: 'json',type: 'post'};
		options = ArrayTools.merge(defaultOptions, options);
	},*/
};