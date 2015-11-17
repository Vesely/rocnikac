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
		$this->template->myTests = $this->tests->getTestsByUserId($this->user->id);
	}
}
