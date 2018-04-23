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
}