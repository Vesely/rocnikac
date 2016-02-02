<?php

namespace App\Model;

use Nette,
	Nette\Utils\Strings;


/**
 * Tests model.
 */
class Tests extends Nette\Object
{
	
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function getTests() 
	{
		return $this->database->table('tests')->where('del_flag', 0)->order('ins_dt DESC');
	}

	public function getTestsByUserId($user_id) 
	{
		return $this->getTests()->where('user_id', $user_id);
	}

	public function getTest($id) 
	{
		if($id) {
			return $this->getTests()->where('id', $id)->fetch();
		}
	}

	public function addTest($data)
	{
		if($data) {
			return $this->database->table('tests')->insert($data);
		}
		return false;
	}

	public function removeTest($id)
	{
		if($id) {
			return $this->database->table('tests')->where('id', $id)->update(array('del_flag' => 1));
		}
	}

	public function undoRemoveTest($id)
	{
		if($id) {
			return $this->database->table('tests')->where('id', $id)->update(array('del_flag' => 0));
		}
	}

}