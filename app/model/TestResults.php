<?php

namespace App\Model;

use Nette,
	Nette\Utils\Strings;


/**
 * TestResults model.
 */
class TestResults extends Nette\Object
{
	
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function getTestResults() 
	{
		return $this->database->table('test_results')->where('del_flag', 0)->order('ins_dt DESC');
	}

	public function getTestResultsByTestId($test_id) 
	{
		return $this->getTestResults()->where('test_id', $test_id);
	}

	public function getTestResultsByUserId($user_id) 
	{
		return $this->getTestResults()->where('user_id', $user_id);
	}

	public function getTestResultsByTestIdAndUserId($test_id, $user_id) 
	{
		return $this->getTestResults()->where('test_id', $test_id)->where('user_id', $user_id)->fetch();
	}

	public function addTestResult($data)
	{
		if($data) {
			return $this->database->table('test_results')->insert($data);
		}
		return false;
	}

}