<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Utils\Image;


/**
 * Admin presenter.
 */
class AdminPresenter extends BasePresenter
{

	public function startup()
	{
		parent::startup();

		// if (!$this->user->isLoggedIn()) {
		// 	$this->redirect('Sign:in');
		// }
	}

	public function renderDefault()
	{
		if($this->user->isInRole('student')){
			$this->redirect('Test:default');
		}else{
			$this->template->myTests = $this->tests->getTestsByUserId($this->user->id);
			$httpRequest = $this->getHttpRequest();
			$uri = $httpRequest->getUrl();
			$this->template->host = $uri->host;
		}
	}

	public function renderAttempts($id)
	{
		$test = $this->tests->getTest($id);
		if(empty($test)){
			// throw new \Exception("Test neexistuje!");
			$this->flashMessage('Požadovaný test neexistuje!', 'error');
			$this->redirect('Admin:default');
		}else{
			$this->template->test = $this->tests->getTest($id);
			$this->template->testResults = $this->testResults->getTestResultsByTestId($id);
		}
	}

	public function renderClearuploads()
	{
		// $dir = 
		$dir = $this->context->parameters['wwwDir'] . '/uploads/';
		$images = scandir($dir, 1);
		$removedImages = array();
		foreach($images as $key => $image) {
			$extension = stristr($image,'.');
			if($extension == '.jpg'){
				$found = $this->questions->findQuestionByImg($image);
				if($found === FALSE) {
					$removedImages[$key] = $image;
					unlink($dir.$image);
				}
			}
		}
		echo 'Bylo odstraněno '.count($removedImages) . ' obrazků.';

		exit;
	}


	public function handleRemoveTest($id)
	{
		if ($id) {
			$removed = $this->tests->removeTest($id);
			$link = $this->link('UndoRemoveTest!', array('id' => $id));

			$this->flashMessage('Test byl smazán. <a href="'.$link.'">Obnovit test</a>', 'success');
			$this->redirect(':Admin:default');
		}
	}

	public function handleUndoRemoveTest($id)
	{
		if ($id) {
			$undo = $this->tests->undoRemoveTest($id);
			
			$this->flashMessage('Test byl obnoven.', 'success');
			$this->redirect(':Admin:default');
		}
	}
}
