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
}
