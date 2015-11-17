<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Utils\Strings;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @inject @var Model\Tests */
    public $tests;

    /** @inject @var Model\Questions */
    public $questions;

     /** @inject @var Model\TestResults */
    public $testResults;


	public function webalize($string)
	{
		return Strings::webalize($string);
	}

}
