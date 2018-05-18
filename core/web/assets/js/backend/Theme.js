var Theme = {
	showDialog : function(content, modal, showClose){
		return null;
	},
	showModalLoader : function(message){
		
	},
	hideModalLoader : function(){
		
	},
	closeDialog : function(dialog){
		
	},
	getPageContent : function(content){
		return '<div class="pageContent"><div class="pageSuccess"></div><div class="pageErrors"></div>'+content+'</div>';
	},
	getSuccessHtml : function(message){
		return message;
	},
	getErrorsHtml : function(errors){
		return ArrayTools.isArrayOrObject(errors) ? errors.join() : errors;
	},
	showErrorFromTarget : function(target, errors){
		target.closest(".pageContent").find(".pageErrors:first").html(Theme.getErrorsHtml(errors));
	},
	showSuccessFromTarget : function(target, message){
		target.closest(".pageContent").find(".pageSuccess:first").html(Theme.getSuccessHtml(message));
	},
	showToast : function(message){
		toastr.success(message);
	},
	clearSuccessFromTarget : function(target){
		target.closest(".pageContent").find(".pageSuccess:first").html("");
	},
	clearErrorsFromTarget : function(target){
		target.closest(".pageContent").find(".pageErrors:first").html("");
	},
};