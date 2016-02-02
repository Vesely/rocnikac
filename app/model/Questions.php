<?php

namespace App\Model;

use Nette,
	Nette\Utils\Strings;


/**
 * Questions model.
 */
class Questions extends Nette\Object
{
	
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function getQuestions() 
	{
		return $this->database->table('questions')->where('del_flag', 0);
	}

	public function getQuestionsInTest($id) 
	{
		return $this->getQuestions()->where('test_id', $id);
	}

	public function getQuestion($id) 
	{
		if($id) {
			return $this->getQuestions()->where('id', $id)->fetch();
		}
	}

	public function findQuestionByImg($question_img) 
	{
		if($question_img) {
			return $this->getQuestions()->where('question_img', $question_img)->fetch();
		}
	}

	public function addQuestion($data)
	{
		if($data) {
			return $this->database->table('questions')->insert($data);
		}
		return false;
	}



}