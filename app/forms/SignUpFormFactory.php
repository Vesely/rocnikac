<?php

namespace App\Forms;

use Nette,
	Nette\Application\UI\Form,
	Nette\Security\User,
	\App\Model\UserManager;


class SignUpFormFactory extends Nette\Object
{
	/** @var User */
	private $user;

	/** @var \App\Model\UserManager */
    public $userManager;


	public function __construct(User $user, \App\Model\UserManager $userManager)
	{
		$this->user = $user;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form;
		
		$form->addText('fullname', 'Celé jméno:')
			->setRequired('Prosím vyplňte Vaše jméno.');
		
		$form->addText('email', 'E-mail:')
			->addRule(Form::FILLED, 'Vyplňte Váš email.')
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'Neplatná emailová adresa.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte Vaše heslo')
			->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);

		$form->addPassword('password2', 'Heslo znovu: ')
                ->addConditionOn($form['password'], Form::VALID)
                ->addRule(Form::FILLED, 'Napište Vaše heslo znovu.')
                ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password']);

		$role = array(
			'student' => 'Student',
			'teacher' => 'Učitel',
		);
		$form->addRadioList('role', 'Kdo jsi?', $role)
			->setDefaultValue('student');

		$form->addSubmit('send', 'Zaregistrovat se');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded($form, $values)
	{
		// $stop();
		$user = $this->userManager->add($values->fullname, $values->role, $values->email, $values->password);
		if($user == false) { 
			$form->addError('Tento email je již používán!');
		}else{
			$this->user->login($values->email, $values->password);
			$this->user->setExpiration('14 days', FALSE);
		}
	}

}
