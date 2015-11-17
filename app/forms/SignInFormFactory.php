<?php

namespace App\Forms;

use Nette,
	Nette\Application\UI\Form,
	Nette\Security\User;


class SignInFormFactory extends Nette\Object
{
	/** @var User */
	private $user;


	public function __construct(User $user)
	{
		$this->user = $user;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form;
		$form->addText('email', 'E-mail:')
			->setRequired('Prosím vyplňte Váš email.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte Vaše heslo');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded($form, $values)
	{
		$this->user->setExpiration('14 days', FALSE);

		try {
			$this->user->login($values->email, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}
