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

$('.answer-group-checkbox .form-answer').on('change', function(){
	var $questionsGroup = $(this).parents('.question-group').first();
	var $answerGroup = $(this).parents('.answer-group');


	$answerGroup.find('.form-answer').each( function( index, input ){
		var $input = $(input);
		var value = $input.val();
		var answer = $input.data('answer');
		var answerUppercase = answer.toUpperCase();
		var $groupCorrectAnswers = $answerGroup.find('.form-group-correct-answers');

		var answerText = 'Odpověď '+answerUppercase;
		
		if (value.length === 0) {
			$groupCorrectAnswers.find('.checkbox-'+answer).addClass('disabled');
		}else{
			var answerText = answerText +' - ' + value;
			$groupCorrectAnswers.find('.checkbox-'+answer).removeClass('disabled');
		}
		
		$groupCorrectAnswers.find('.checkbox-'+answer+' span').text(answerText);
	});

});
