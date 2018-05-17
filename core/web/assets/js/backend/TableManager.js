var TableManager = {
	init : function(){
		TableManager.handleEvent();
	},
	showDialog : function(content, modal, showClose){
		
	},
	handleEvent : function(){
		$(document).on("click", ".listWrapper.ajaxList .listCommand, .listCommand.ajaxCommand", function(e){
			e.preventDefault();
			TableManager.onAjaxAction($(this), false);
		});
		$(document).on("submit", ".listWrapper.ajaxList .formList", function(e){
			e.preventDefault();
			TableManager.onAjaxAction($(this), true);
		});
		$(document).on("change", ".ajaxActivator", function(e){
			var target = $(this);
			var checked = target.filter(':checked').val()=="1";
			if(checked){
				target.closest(".listWrapper").addClass("ajaxList");
			}else{
				target.closest(".listWrapper").removeClass("ajaxList");
			}
		});
	},
	
	onAjaxAction : function(target, isForm){
		if(TableManager.beforeAjaxAction(target)){
			Theme.showModalLoader();
			Tools.ajaxCall(target, {
				url : isForm ? target.attr("action") : target.attr("href"),
				success :function (jsonData) {
					Theme.hideModalLoader();
					console.log(jsonData);
				},
				error :function (jsonData) {
					Theme.hideModalLoader();
				},
			});
		}
	},
	
	beforeAjaxAction : function(target){return true;},
	afterAjaxAction : function(target){},
	checkCommand : function(target, callback){
		if(target.hasClass("ajaxCommand")||target.closest(".listWrapper").hasClass("ajaxList")){
			TableManager.onAjaxAction(target, false);
		}else{
			callback();
		}
	}
};
TableManager.init();