<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    Nette\Security\User,
    App\Model\UserManager,
    \Doctrine\DBAL\Exception\UniqueConstraintViolationException;


class SignPresenter extends BasePresenter
{
    /**
     * @var \App\Model\UserManager
     */
    private $userManager;


    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * TODO
     */
    public function actionUp() {
        if ($this->getUser()->isLoggedIn()) {
            //$this->redirect();
        }
    }


    /**
     * TODO
     */
    public function actionIn() {
        if ($this->getUser()->isLoggedIn()) {
            //$this->redirect();
        }
    }


    /**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
        $form = new Form();
        $form->getElementPrototype()->novalidate('novalidate');
        $form->setTranslator($this->translator);

        $form->addText('email', 'E-mail')
            ->setAttribute('id', 'email')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::FILLED, 'forms.empty_field')
            ->addRule(Form::PATTERN, 'forms.incorrect_email', '(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])');


        $form->addPassword('password', 'forms.password')
            ->setAttribute('id', 'password')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::FILLED, 'forms.empty_field')
            ->addRule(Form::PATTERN, 'forms.incorrect_passwd', '[^<>]*');

        $form->addSubmit('signInBut', 'forms.signin.submit')
            ->setAttribute('class', 'mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect');

        $form->onSuccess[] = array($this, 'signInFormSucceeded');

        return $form;
	}


    public function signInFormSucceeded(Form $form, $values)
    {
        try {
            $this->getUser()->login($values->email, $values->password, $this->template->basePath);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }


    protected function createComponentSignUpForm() {
        $form = new Form;
        $form->getElementPrototype()->novalidate('novalidate');
        $form->setTranslator($this->translator);

        $form->addText('name', 'forms.signup.name')
            ->setAttribute('id', 'name')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::PATTERN, 'forms.signup.incorrect_name', '[^<>(0-9)]*')
            ->addRule(Form::MIN_LENGTH, 'forms.signup.name_length', 2)
            ->addRule(Form::FILLED, 'forms.empty_field');

        $form->addText('surname', 'forms.signup.surname')
            ->setAttribute('id', 'surname')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::PATTERN, 'forms.signup.incorrect_surname', '[^<>(0-9)]*')
            ->addRule(Form::MIN_LENGTH, 'forms.signup.name_length', 2)
            ->addRule(Form::FILLED, 'forms.empty_field');

        $form->addText('email', 'E-mail')
            ->setAttribute('id', 'email')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::PATTERN, 'forms.incorrect_email', '(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])')
            ->addRule(Form::FILLED, 'forms.empty_field');

        $form->addPassword('password', 'forms.password')
            ->setAttribute('id', 'password')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::PATTERN, 'forms.incorrect_passwd', '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}')
            ->addRule(Form::FILLED, 'forms.empty_field');

        $form->addPassword('passwordControl', 'forms.signup.password_again')
            ->setAttribute('id', 'passwordControl')
            ->setAttribute('class', 'mdl-textfield__input')
            ->addRule(Form::PATTERN, 'forms.incorrect_passwd', '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}')
            ->addRule(Form::EQUAL, 'forms.signup.same_passwds', $form['password'])
            ->addRule(Form::FILLED, 'forms.empty_field');

        $form->addSubmit('signUpBut', 'forms.signup.submit')
            ->setAttribute('class', 'mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect');


        $form->onSuccess[] = array($this, 'signUpFormSucceeded');

        return $form;
    }

    /**
     * @param $form
     * @param $values
     */
    public function signUpFormSucceeded($form, $values) {
        //if($this->isAjax()) {
            try {
                $this->userManager->register($values['name'],
                    $values['surname'],
                    $values['email'],
                    $values['password']);
                //$this->redirect('Settings:user');
            } catch(UniqueConstraintViolationException $e) {
                $form->addError($e->getMessage());
            }
            //$this->redrawControl('');
        //}
    }

}
