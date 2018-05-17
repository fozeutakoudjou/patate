var modalLoader;
Theme.showDialog=function(content, modal, showClose){
	var dialog = bootbox.dialog({
		message: content,
		closeButton: showClose
	});
}
Theme.showModalLoader=function(message){
	modalLoader = bootbox.dialog({
		message: '<p class="loader_modal"><i class="fa fa-refresh fa-spin loader_modal_icon"></i> Loading...</p>',
		closeButton: false
	});
}
Theme.hideModalLoader=function(message){
	if(typeof(modalLoader)!=="undefined"){
		modalLoader.modal("hide");
	}
}