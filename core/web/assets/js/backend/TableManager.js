var TableManager = {
	instances:{},
	init : function(){
		TableManager.handleEvent();
	},
	handleEvent : function(){
		$(document).on("click", ".listWrapper.ajaxList .listCommand:not(.auto_confirm), .listCommand.ajaxCommand:not(.auto_confirm)", function(e){
			e.preventDefault();
			TableManager.onAjaxAction($(this), false, false);
		});
		$(document).on("submit", ".listWrapperTopParent.ajaxParent .formEdit", function(e){
			e.preventDefault();
			TableManager.onAjaxAction($(this), true, true);
		});
		$(document).on("click", ".listWrapperTopParent.ajaxParent .formEdit .btnCancel", function(e){
			e.preventDefault();
			TableManager.onCancelEdit($(this));
		});
		$(document).on("submit", ".listWrapper.ajaxList .formList", function(e){
			e.preventDefault();
			TableManager.onAjaxAction($(this), true, false);
		});
		$(document).on("change", ".ajaxActivator", function(e){
			var target = $(this);
			var checked = target.filter(':checked').val()=="1";
			if(checked){
				target.closest(".listWrapper").addClass("ajaxList").closest(".listWrapperTopParent").addClass("ajaxParent");
			}else{
				target.closest(".listWrapper").removeClass("ajaxList").closest(".listWrapperTopParent").removeClass("ajaxParent");
			}
		});
	},
	onCancelEdit : function(target){
		var listEditionFormBlock = target.closest(".listEditionFormBlock"); 
		listEditionFormBlock.html("");
		listEditionFormBlock.hide();
		var listWrapperTopParent = listEditionFormBlock.closest(".listWrapperTopParent");
		if(listWrapperTopParent.hasClass("formInDialog")){
			TableManager.closeEditionDialog(listWrapperTopParent.attr("data-id"));
		}else{
			var listWrapper = listWrapperTopParent.find(".listWrapper:first");
			if(listWrapper.attr("data-form_open_mode")=="side"){
				listWrapper.removeClass(listWrapper.attr("data-list_width"));
			}
		}
	},
	onAjaxDefaultAction : function(target, isFormTarget, isEditForm){
		Theme.showModalLoader();
		Tools.ajaxCall(target, {
			url : isFormTarget ? target.attr("action") : target.attr("href"),
			data : isFormTarget ? target.serialize() :{},
			success :function (jsonData) {
				TableManager.handleAjaxResponse(jsonData, target, isFormTarget, isEditForm);
			},
			error :function (jsonData) {
				Theme.hideModalLoader();
			},
		});
	},
	beforeAjaxResponse : function(jsonData, target, isFormTarget, isEditForm){return true;},
	afterAjaxResponse : function(jsonData, target, isFormTarget, isEditForm){},
	handleAjaxResponse : function(jsonData, target, isFormTarget , isEditForm){
		if(TableManager.beforeAjaxResponse(jsonData, target, isFormTarget, isEditForm)){
			TableManager.handleAjaxDefaultResponse(jsonData, target, isFormTarget, isEditForm);
		}
		TableManager.afterAjaxResponse(jsonData, target, isFormTarget, isEditForm);
	},
	closeEditionDialog : function(id){
		if((typeof(TableManager.instances[id])!=="undefined") && (typeof(TableManager.instances[id].editionDialog)!=="undefined") && (TableManager.instances[id].editionDialog!=null)){
			Theme.closeDialog(TableManager.instances[id].editionDialog);
		}
	},
	showResponseNotification : function(jsonData, target, isFormTarget , isEditForm){
		if(jsonData.hasErrors){
			Theme.showErrorFromTarget(target, jsonData.errors);
		}else{
			Theme.clearErrorsFromTarget(target);
		}
		if(typeof(jsonData.success)!=="undefined"){
			if((typeof(jsonData.successAsToast)!=="undefined") && jsonData.successAsToast){
				Theme.showToast(sonData.success);
			}else{
				Theme.showSuccessFromTarget(target, jsonData.success);
			}
		}else{
			Theme.clearSuccessFromTarget(target);
		}
	},
	handleAjaxDefaultResponse : function(jsonData, target, isFormTarget , isEditForm){
		Theme.hideModalLoader();
		var hasContent = typeof(jsonData.content)!=="undefined";
		var isListContentType = hasContent && (typeof(jsonData.listContentType)!=="undefined") && jsonData.listContentType;
		var isFormContentType = hasContent && (typeof(jsonData.formContentType)!=="undefined") && jsonData.formContentType;
		var listWrapperTopParent = null;
		var notificationTarget = target;
		var isFormOpenInDialog = false;
		if(hasContent){
			listWrapperTopParent = target.closest(".listWrapperTopParent");
			if(listWrapperTopParent.hasClass("formInDialog")){
				if(isListContentType){
					var parentId = listWrapperTopParent.attr("data-id");
					listWrapperTopParent = $("#"+parentId);
					TableManager.closeEditionDialog(parentId);
				}
				isFormOpenInDialog = true;
			}
			notificationTarget = listWrapperTopParent;
		}
		TableManager.showResponseNotification(jsonData, notificationTarget, isFormTarget , isEditForm);
		if(hasContent){
			if(isListContentType && target.hasClass("openInDialog")){
				Theme.showDialog(Theme.getPageContent(jsonData.content), false, true);
			}else if(isListContentType){
				listWrapperTopParent.replaceWith(jsonData.content);
			}else if(isFormContentType){
				if(isFormOpenInDialog){
					listWrapperTopParent.find(".listEditionFormBlock:first").html(jsonData.content);
				}else{
					var listWrapper = listWrapperTopParent.find(".listWrapper:first");
					var mode = listWrapper.attr("data-form_open_mode");
					if(mode=="dialog"){
						var parentId = listWrapperTopParent.attr("id");
						TableManager.instances[parentId] = {};
						TableManager.instances[parentId].editionDialog = Theme.showDialog(TableManager.createModalFormWrapper(parentId, jsonData.content), false, true);
					}else{
						var listEditionFormBlock = listWrapperTopParent.find(".listEditionFormBlock:first");
						listEditionFormBlock.html(jsonData.content);
						if(mode=="side"){
							var widthClass = listWrapper.attr("data-list_width");
							if(!listWrapper.hasClass(widthClass)){
								listWrapper.addClass(widthClass);
							}
						}
						listEditionFormBlock.show();
					}
				}
			}
		}
	},
	onAjaxAction : function(target, isFormTarget, isEditForm){
		if(TableManager.beforeAjaxAction(target, isFormTarget, isEditForm)){
			TableManager.onAjaxDefaultAction(target, isFormTarget, isEditForm);
		}
		TableManager.afterAjaxAction(target, isFormTarget, isEditForm);
	},
	
	beforeAjaxAction : function(target, isFormTarget, isEditForm){return true;},
	afterAjaxAction : function(target, isFormTarget, isEditForm){},
	checkCommand : function(target, callback){
		if(target.hasClass("ajaxCommand")||target.closest(".listWrapper").hasClass("ajaxList")){
			TableManager.onAjaxAction(target, false, false);
		}else{
			callback();
		}
	},
	createModalFormWrapper : function(id, content){
		return Theme.getPageContent('<div class="listWrapperTopParent ajaxParent formInDialog" data-id="'+id+'"><div class="listEditionFormBlock">'+content+'</div></div>');
	}
};
TableManager.init();