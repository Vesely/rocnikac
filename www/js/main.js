//Fill selectbox 
$('.form-answer').on('change', function(){
	var $questionsGroup = $(this).parents('.question-group').first();
	var value = $(this).val();
	var answer = $(this).data('answer');
	var answerUppercase = answer.toUpperCase();

	var $thisAnswer = $questionsGroup.find('.form-correct-answer').find('[value='+answer+']');
	// if (value != '') {
		$thisAnswer.text('Odpověď '+answerUppercase+' - ' + value);
	// }else{
		// $thisAnswer.text('Odpověď '+answerUppercase);
	// }
});
