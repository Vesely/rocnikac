
//Alerts
$('.alert .close').on('click', function(){
	$(this).parents('.alert').first().fadeOut(300);
	return false;
});


//Tooltips
$('.tooltip').tooltipster({
	contentAsHTML: true
});