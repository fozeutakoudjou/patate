var ArrayTools = {
	merge : function(array1, array2){
		return $.extend({}, array1, array2);
	},
	isArrayOrObject : function(value){
		return (typeof(value)==="object");
	},
};