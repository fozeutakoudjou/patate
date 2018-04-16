$(document).ready(function() {
	handleGlobalEvent();
});

function handleGlobalEvent(){
	$(document).on("click", ".show_hide", function(e){
		e.preventDefault();
		$($(this).attr("target_to_hide")).hide();
		$($(this).attr("target_to_show")).show();
	});
}