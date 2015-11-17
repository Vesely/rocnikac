<?php

namespace App\Presenters;

use Nette,
	App\Forms\SignInFormFactory,
	App\Forms\SignUpFormFactory,
	Nette\Security\Passwords;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
	/** @var SignInFormFactory @inject */
	public $factoryIn;

	/** @var SignUpFormFactory @inject */
	public $factoryUp;

	

	public function beforeRender()
	{
		// $pw = Passwords::hash('123456');
		// $stop();
	}


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factoryIn->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Admin:');
		};
		return $form;
	}


	/**
	 * Sign-up form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		$form = $this->factoryUp->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Admin:');
		};
		return $form;
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jsi úspěšně odhlášen.');
		$this->redirect('Sign:default');
	}

}
