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

	public function renderDefault($to, $toTest=null)
	{
		$httpResponse = $this->getHttpResponse();
		if ($to == 'new') {	
			$httpResponse->setCookie('to', 'new', '2 days');
		}
		if (($to == 'test') && (!empty($toTest))) {	
			$httpResponse->setCookie('to', 'test', '2 days');
			$httpResponse->setCookie('toTest', $toTest, '2 days');
			$test = $this->tests->getTest($toTest);
			if($test){
				$this->template->toTest = $test;
			}
		}
	}


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factoryIn->create();
		$form->onSuccess[] = function ($form) {
			$httpRequest = $form->getPresenter()->getHttpRequest();
			$httpResponse = $form->getPresenter()->getHttpResponse();
			$cookie = $httpRequest->getCookie('to');
			$cookieTest = $httpRequest->getCookie('toTest');
			
			if (!empty($cookie)) {
				if($cookie == 'new'){
					$httpResponse->deleteCookie('to');
					$form->getPresenter()->redirect('Test:new');
				}
				if($cookie == 'test'){
					$httpResponse->deleteCookie('to');
					$form->getPresenter()->redirect('Test:test', array('id' => $cookieTest));
				}
			}else{
				$form->getPresenter()->redirect('Admin:');
			}
		};
		return $form;
	}


	/**
	 * Sign-up form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		// $httpRequest = $this->getHttpRequest();
		$params = $this->params;
		$to = NULL;
		if(array_key_exists('to', $params)) {
			$to = $params['to'];
		}
		
		$form = $this->factoryUp->create($to);
		$form->onSuccess[] = function ($form) {
			$httpRequest = $form->getPresenter()->getHttpRequest();
			$httpResponse = $form->getPresenter()->getHttpResponse();
			$cookie = $httpRequest->getCookie('to');
			$cookieTest = $httpRequest->getCookie('toTest');
			
			if (!empty($cookie)) {
				if($cookie == 'new'){
					$httpResponse->deleteCookie('to');
					$form->getPresenter()->redirect('Test:new');
				}
				if($cookie == 'test'){
					$httpResponse->deleteCookie('to');
					$form->getPresenter()->redirect('Test:test', array('id' => $cookieTest));
				}
			}else{
				$form->getPresenter()->redirect('Admin:');
			}
		};
		return $form;
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jsi úspěšně odhlášen.');
		$this->redirect('Homepage:default');
	}

}
