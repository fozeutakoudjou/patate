$(document).ready(function() {
	handleGlobalEvent();
});

function handleGlobalEvent(){
	$(document).on("click", ".show_hide", function(e){
		e.preventDefault();
		$($(this).attr("target_to_hide")).hide();
		$($(this).attr("target_to_show")).show();
	});
	
	$(document).on("click", ".switcher_field_lang", function(e){
		e.preventDefault();
		$(".lang_form_label").text($(this).attr("data-label"));
		$(".li_switcher_field_lang").removeClass("active");
		$(".li_switcher_field_lang.li_"+$(this).attr("data-lang")).addClass("active");
	});
	
	$(document).on("click", ".all_checker", function(e){
		e.preventDefault();
		$($(this).attr("target_item")).prop("checked", true);
	});
	$(document).on("click", ".all_unchecker", function(e){
		e.preventDefault();
		$($(this).attr("target_item")).prop("checked", false);
	});
	$(document).on("change", ".check_all_switcher", function(e){
		$($(this).attr("target_item")).prop("checked", $(this).is(":checked"));
	});
	$(document).on("click", ".bulk_action", function(e){
		checkConfirm(e, function(target){
			var form = target.closest("form");
			form.find("input[name='action']:first").val(target.attr("data-action"));
			form.submit();
		});
	});
	$(document).on("click", ".auto_confirm", function(e){
		if(!$(this).hasClass("alreadyConfirmed")){
			checkConfirm(e, function(target){
				target.addClass("alreadyConfirmed");
				if(target.is("a")){
					location.href = target.attr("href");
				}else{
					target.click();
				}
			});
		}
	});
	
	$(document).on("click", ".table_search_btn", function(e){
		var form = $(this).closest("form");
		form.find("input[name='action']:first").val("");
	});
}

function showConfirmDialog(target, callback){
	if(confirm(target.attr("confirm_text"))){
		callback(target);
	}
}

function checkConfirm(event, callback){
	event.preventDefault();
	var target = $(event.target);
	if(target.hasClass("confirm_command")){
		showConfirmDialog(target, callback);
	}else{
		callback(target);
	}
}