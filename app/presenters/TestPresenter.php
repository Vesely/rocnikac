<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Application\UI\Form,
	Nette\Forms\Container,
	Nette\Forms\Controls\SubmitButton;


/**
 * Test presenter.
 */
class TestPresenter extends BasePresenter
{
	
	public $currentQuestions;
	public $testId;


	public function beforeRender()
	{
		$this->template->tests = $this->tests->getTests();

	}

	public function renderTest($id)
	{
		if(!empty($id)) {
			$test = $this->tests->getTest($id);
			$this->template->test = $test;
			$this->template->questions = $this->questions->getQuestionsInTest($id);
		} else {
			$stop();
		}
	}


	public function renderResult()
	{
		$json = '{"1":{"isCorrect":false,"questionId":1,"myAnswer":"c","correctAnswer":"b"},"2":{"isCorrect":true,"questionId":2,"myAnswer":"a","correctAnswer":"a"},"3":{"isCorrect":false,"questionId":3,"myAnswer":"b","correctAnswer":"c"}}';
		$this->template->answers = json_decode($json);
	}


	/**
	 * TakeTest form form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentTakeTestForm()
	{
		$form = new Nette\Application\UI\Form;
		
		$testId = $this->params['id'];
		$questions = $this->questions->getQuestionsInTest($testId);


		$form->addGroup('questions');
		foreach ($questions as $key => $question) {
			$questionsGroup = $form->addContainer('group'.$key);
			$type = $question->type;

			//Výběr jedné správné odpověďi
			if($type == 'radio') {
				//Answers
				$answers = array(
					'a' => $question->answer_a,
					'b' => $question->answer_b
					);
				if (!empty($question->answer_c)) { $answers['c'] = $question->answer_c; }
				if (!empty($question->answer_d)) { $answers['d'] = $question->answer_d; }
				
				//Answer radio list
				$questionsGroup->addRadioList('answer'.$key, $question->question, $answers)
					->setRequired('Vyber odpověď k otázce: '.$question->question);
			}

			//Výběr více odpověďí
			if($type == 'checkbox') {
				//Answers
				$answers = array(
					'a' => $question->answer_a,
					'b' => $question->answer_b
					);
				if (!empty($question->answer_c)) { $answers['c'] = $question->answer_c; }
				if (!empty($question->answer_d)) { $answers['d'] = $question->answer_d; }
				
				//Answer radio list
				$questionsGroup->addCheckboxList('answer'.$key, $question->question, $answers)
					->setRequired('Vyber odpověďi k otázce: '.$question->question);
			}

			//Textová odpověď
			if($type == 'text') {
				$questionsGroup->addText('answer'.$key, $question->question)
					->setRequired('Vyplň odpověď k otázce: '.$question->question);
			}
			$questionsGroup->addHidden('answer_type'.$key)->setDefaultValue($type);
		}

		// $form->addHidden('test_id')->setDefaultValue($testId);
		
		$form->addGroup('submit');
		$form->addSubmit('submit', 'Odeslat test');

		$form->onSuccess[] = array($this, 'takeTestFormFormSucceeded');
		return $form;
	}

	public function takeTestFormFormSucceeded($form)
	{
		$values = $form->values;
		
		//ID aktuální testu
		$testId = (int) $this->params['id'];
		//Získání otázek aktuálního testu z databáze
		$questions = $this->questions->getQuestionsInTest($testId);
		//Počet otázek v aktuálním testu
		$questionsCount = $questions->count();


		$answers = array();
		$correctAnswersCount = 0;
		foreach ($questions as $key => $question) {
			//Vyplňená odpověď
			$answer = $form->values['group'.$key]['answer'.$key];
			$answerType = $form->values['group'.$key]['answer_type'.$key];

			//Je odpověď správně? (true X false)
			$correctAnswer = NULL;
			if ($answerType == 'radio') {
				$correctAnswer = $question->correct_answer;
			}elseif($answerType == 'checkbox') {
				$correctAnswer = json_decode($question->correct_answers);
			}elseif($answerType == 'text') {
				$correctAnswer = $question->answer_text;
				//similar_text($question->answer_text, $answer, $percentageTextSimillar);
			}
			$isAnswerCorrect = ($correctAnswer == $answer);

			//Soupis data z odpověďi
			$answers[$key] = array(
				'isCorrect' => $isAnswerCorrect,
				// 'question' => $question->question,
				'questionId' => $question->id,
				'myAnswer' => $answer,
				'correctAnswer' => $correctAnswer,
				'answer_type' => $answerType
				);

			//Pokud je odpověď správná, inkrementuj počet správných
			if($isAnswerCorrect == true) {
				$correctAnswersCount = $correctAnswersCount+1;
			}

		}
		
		//Spočítej procentuální úšpěšnost
		$percentage = round(($correctAnswersCount/$questionsCount)*100);

		//Encode $answers to JSON
		$answersJson = json_encode($answers); 

		$data = array(
			'user_id' => $this->user->id,
			'test_id' => $testId,
			'answers' => $answersJson,
			'percentage' => $percentage,
			'ins_dt' => new \DateTime
			);

		//Zapiš nově provedený test do DB
		$this->testResults->addTestResult($data);
		// $stop();


		$this->flashMessage('Nový test byl úspěšně odeslán.', 'success');
		$this->redirect('this');
	}



	/**
	 * NewTest form form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentNewTestForm()
	{
		$form = new Nette\Application\UI\Form;

		$presenter = $this;
		$invalidateCallback = function () use ($presenter) {
			/** @var \Nette\Application\UI\Presenter $presenter */
			$presenter->invalidateControl('newTestForm');
			$presenter->invalidateControl('newTestScripts');
		};


		$form->addText('name', 'Název testu: ')
			->setRequired('Zadejte prosím název testu.');
		
		$attempts = ['Neomezeně', '1', '2', '3', '4', '5', '6'];
		$form->addSelect('attempts', 'Počet pokusů: ', $attempts);

		
		//Questions
		$questions = $form->addDynamic('questions', function (Container $question) use ($invalidateCallback) {
			$question->addText('question', ($question->name+1).'. Otázka: ');
			$question->addUpload('question_img', 'Obrázek k otázce:');

			//Answer type
			$types = array(
				'radio' => 'Výběr jedné odpovědi',
				'checkbox' => 'Výber více odpovědí',
				'text' => 'Textová odpověď'
				);
			$question->addRadioList('type', 'Typ odpovědi: ', $types);
					// ->setDefaultValue('radio');

			
			//
			$question->addText('answer_a', 'Odpověď A:');
					//->addConditionOn($question['question'], Form::FILLED)
					//->setRequired('Vypňte odpověď A.');
			$question->addText('answer_b', 'Odpověď B:');
					//->addConditionOn($question['question'], Form::FILLED)
					//->setRequired('Vypňte odpověď B.');
			$question->addText('answer_c', 'Odpověď C:');
			$question->addText('answer_d', 'Odpověď D:');

			$correctAnswers = array(
				'a' => 'Odpověď A',
				'b' => 'Odpověď B',
				'c' => 'Odpověď C',
				'd' => 'Odpověď D'
				);
			
			//Více správných odpovědí
			$question->addCheckboxList('correct_answers', 'Správné odpovědi', $correctAnswers);

			//Výběr jedné správné odpovědi
			$question->addSelect('correct_answer', 'Správná odpověď: ', $correctAnswers);

			//Text answer
			$question->addText('answer_text', 'Textová odpověď: ');


			$question->addSubmit('remove', '× Odstranit otázku')
				->setValidationScope(FALSE) # disables validation
				->setAttribute('class', 'ajax')
				->onClick[] =  array($this, 'removeElementClicked');
		}, 2);

		$questions->addSubmit('add', '+ Přidat otázku')
			->setValidationScope(FALSE)
			->setAttribute('class', 'btn btn-simple btn-add-question ajax')
			//->addCreateOnClick($invalidateCallback);
			->onClick[] = array($this, 'addElementClicked');


		$form->addSubmit('submit', 'Vytvořit test')
			->onClick[] = array($this, 'newTestFormFormSucceeded');

		// $form->onSuccess[] = array($this, 'newTestFormFormSucceeded');
		return $form;
	}

	public function addElementClicked(SubmitButton $button)
	{
		$button->parent->createOne();
		$this->invalidateControl('newTestForm');
		$this->invalidateControl('newTestScripts');
	}
	public function removeElementClicked(SubmitButton $button)
	{
		// first parent is container
		// second parent is it's replicator
		$questions = $button->parent->parent;
		$questions->remove($button->parent, TRUE);
		$this->invalidateControl('newTestForm');
		$this->invalidateControl('newTestScripts');
	}

	public function newTestFormFormSucceeded($button)
	{
		// $values = $form->values;
		$form = $button->form;
		$values = $form->getValues();

		$attempts = $values->attempts;
		if($attempts == 0) {
			$attempts = NULL;
		}

		$dataTest = array(
			'name' => $values->name,
			'attempts' => $attempts,
			'password' => NULL,
			'user_id' => $this->user->id,
			'ins_dt' => new \DateTime
			);

		//$stop();
		
		$test = $this->tests->addTest($dataTest);
		$testId = $test->id;
		// $testId = 8; //only for testing
		foreach ($form['questions']->values as $question) {
			if(!empty($question->question) && ( (!empty($question->answer_a) && !empty($question->answer_b) ) || !empty($question->answer_text)) ) {

				$dataQuestion = array(
					'question' => $question->question,
					'question_img' => NULL,
					'test_id' => $testId,
					'user_id' => $this->user->id,
					'type' => $question->type,
					'ins_dt' => new \DateTime
					);

				//Jedna správná odpověď
				if ($question->type == 'radio') {
					$dataQuestion2 = array(
						'answer_a' => $question->answer_a,
						'answer_b' => $question->answer_b,
						'answer_c' => $question->answer_c ? $question->answer_c : NULL,
						'answer_d' => $question->answer_d ? $question->answer_d : NULL,
						'correct_answer' => $question->correct_answer
					);
					$dataQuestion = array_merge($dataQuestion, $dataQuestion2);
				}

				//Více správných odpověďí
				if ($question->type == 'checkbox') {
					//Encode $correctAnswers to JSON
					$correctAnswersJson = json_encode($question->correct_answers); 
					$dataQuestion2 = array(
						'answer_a' => $question->answer_a,
						'answer_b' => $question->answer_b,
						'answer_c' => $question->answer_c ? $question->answer_c : NULL,
						'answer_d' => $question->answer_d ? $question->answer_d : NULL,
						'correct_answers' => $correctAnswersJson
					);
					$dataQuestion = array_merge($dataQuestion, $dataQuestion2);
				}

				//Textová odpověď
				if ($question->type == 'text') {
					$dataQuestion2 = array(
						'answer_text' => $question->answer_text
					);
					$dataQuestion = array_merge($dataQuestion, $dataQuestion2);
				}

				//Vložení otázky do DB
				$new_question = $this->questions->addQuestion($dataQuestion);
				// $stop();
			}
		}

		// $stop();


		$this->flashMessage('Nový test byl úspěšně vytvořen.', 'success');
		$this->redirect('this');
	}
}
