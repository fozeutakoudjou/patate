var modalLoader;
$(document).on("shown.bs.modal", ".modal", function(e){
	$(this).show();
    Theme.setModalMaxHeight(this);
});

$(window).resize(function() {
    if ($(".modal.in").length != 0) {
        Theme.setModalMaxHeight($(".modal.in"));
    }
});
Theme.setModalMaxHeight=function(element) {
    this.$element     = $(element);  
    this.$content     = this.$element.find('.modal-content');
    var borderWidth   = this.$content.outerHeight() - this.$content.innerHeight();
    var dialogMargin  = $(window).width() < 768 ? 20 : 60;
    var contentHeight = $(window).height() - (dialogMargin + borderWidth);
    var headerHeight  = this.$element.find('.modal-header').outerHeight() || 0;
    var footerHeight  = this.$element.find('.modal-footer').outerHeight() || 0;
    var maxHeight     = contentHeight - (headerHeight + footerHeight);
	this.$content.css({'overflow': 'hidden'});
	this.$element.find('.modal-body').css({'max-height': maxHeight,'overflow-y': 'auto'});
};
Theme.showDialog=function(content, modal, showClose){
	/*var dialog = bootbox.dialog({
		message: content,
		backdrop: 'static',
		className: "my-modal",
		closeButton: showClose
	});*/
	var dialog = $(Theme.getModalContent(content, "modal_max_size"));
	options={};
	if(modal){
		options.backdrop = "static";
	}
	dialog.modal(options);
	return dialog;
};
Theme.showModalLoader=function(message){
	modalLoader = bootbox.dialog({
		message: '<p class="loader_modal"><i class="fa fa-refresh fa-spin loader_modal_icon"></i> Loading...</p>',
		closeButton: false
	});
};
Theme.hideModalLoader=function(){
	Theme.closeDialog(modalLoader);
};
Theme.closeDialog=function(dialog){
	if((typeof(dialog)!=="undefined") && (dialog!=null)){
		dialog.modal("hide");
	}
};
Theme.getSuccessHtml=function(message){
	return '<div class="bootstrap"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div></div>';
};
Theme.getErrorsHtml=function(errors){
	var list = ArrayTools.isArrayOrObject(errors) ? errors : [errors];
	var htmlContent = '<div class="bootstrap"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><ol>';
	for (i in errors) {
        htmlContent += "<li>" + errors[i] + "</li>";
    }
    htmlContent += "</ol></div></div>";
    return htmlContent;
};

Theme.getModalContent=function(content, dialogClass){
	var html = ''+
	'<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">'+
		'<div class="modal-dialog '+dialogClass+'" role="document">'+
			'<div class="modal-content">'+
			'<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'+
			'<div class="modal-body">'+content+'</div>'+
			'</div>'+
		'</div>'+
	'</div>';
	return html;
};