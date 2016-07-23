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
        //$form->getElementPrototype()->class('ajax');
        $form->getElementPrototype()->novalidate('novalidate');
        $form->setTranslator($this->translator);

        $form->addText('email')
            ->setAttribute('placeholder', 'E-mail')
            ->addRule(Form::EMAIL, 'messages.signinForm.incorrect_email')
            ->addRule(Form::FILLED, 'messages.signinForm.empty_email');

        $form->addPassword('password')
            ->setAttribute('placeholder', 'forms.signin.password')
            ->addRule(Form::PATTERN, 'messages.signinForm.incorrect_passwd', '[^<>]*')
            ->addRule(Form::FILLED, 'messages.signinForm.empty_passwd');

        $form->addSubmit('signInBut', 'forms.signin.submit');

        $form->onError[] = function() {
            $this->redrawControl('signInFormSnippet');
        };
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
        //$form->getElementPrototype()->class('ajax');
        $form->getElementPrototype()->novalidate('novalidate');
        $form->setTranslator($this->translator);

        $form->addText('name')
            ->setAttribute('placeholder', 'forms.signup.name')
            ->addRule(Form::PATTERN, 'messages.signupForm.incorrect_name', '[^<>(0-9)]*')
            ->addRule(Form::MIN_LENGTH, 'messages.signupForm.name_length', 2)
            ->addRule(Form::FILLED, 'messages.signupForm.empty_name');

        $form->addText('surname')
            ->setAttribute('placeholder', 'forms.signup.surname')
            ->addRule(Form::PATTERN, 'messages.signupForm.incorrect_surname', '[^<>(0-9)]*')
            ->addRule(Form::MIN_LENGTH, 'messages.signupForm.surname_length', 2)
            ->addRule(Form::FILLED, 'messages.signupForm.empty_surname');

        $form->addText('email')
            ->setAttribute('placeholder', 'E-mail')
            ->addRule(Form::EMAIL, 'messages.signupForm.incorrect_email')
            ->addRule(Form::FILLED, 'messages.signupForm.empty_email');

        $form->addPassword('password')
            ->setAttribute('placeholder', 'forms.signup.password')
            ->addRule(Form::PATTERN, 'messages.signupForm.incorrect_passwd', '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}')
            ->addRule(Form::FILLED, 'messages.signupForm.empty_passwd');

        $form->addPassword('passwordControl')
            ->setAttribute('placeholder', 'forms.signup.password_again')
            ->addRule(Form::PATTERN, 'messages.signupForm.incorrect_passwd', '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}')
            ->addRule(Form::EQUAL, 'messages.signupForm.same_passwds', $form['password'])
            ->addRule(Form::FILLED, 'messages.signupForm.empty_passwd');

        $form->addSubmit('signUpBut', 'forms.signup.submit');

        $form->onError[] = function() {
            $this->redrawControl('');
        };
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
