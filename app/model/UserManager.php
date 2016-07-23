<?php

namespace App\Model;

use App\Colors;
use Nette,
    Nette\Security\Passwords,
    App\User,
    \Kdyby\Doctrine\EntityManager,
    \Doctrine\DBAL\Exception\UniqueConstraintViolationException;


/**
 * Users management.
 */
class UserManager extends BaseManager implements Nette\Security\IAuthenticator
{
    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $entityManager;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;


    public function __construct(EntityManager $entityManager, \Kdyby\Translation\Translator $translator)
    {
        parent::__construct($translator);
        $this->entityManager = $entityManager;
    }

    /**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password, $basePath) = $credentials;

        if(!$user = $this->entityManager->getRepository(User::getClassName())->findOneBy(array('email' => $email))
            OR !Passwords::verify($password, $user->password)) //TODO - přeložit zprávu!
            throw new Nette\Security\AuthenticationException(
                $this->translator->translate('messages.signinForm.incorrect_passwd_or_email'), self::IDENTITY_NOT_FOUND);

        $arr = array();
        $arr['name'] = $user->name;
        $arr['surname'] = $user->surname;
        $arr['email'] = $user->email;
        $arr['picture'] = $basePath . '/images/users/';
        $colors = $this->entityManager->getRepository(Colors::getClassName())->findOneBy(array('id' => $user->color_id));
        $arr['color_id'] = '#' . $colors->color;

        return new Nette\Security\Identity($user->id, 'User', $arr);
	}


	/**
     * TODO hláška při duplicitě!
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 * @throws UniqueConstraintViolationException
	 */
	public function register($name, $surname, $email, $password)
	{
		try {
            $user = new User;
            $user->name = $name;
            $user->surname = $surname;
            $user->email = $email;
            $user->password = Passwords::hash($password);
            $user->picture = 0;
            $user->color_id = 1;

            $this->entityManager->persist($user);
            $this->entityManager->flush();
		} catch (UniqueConstraintViolationException $e) {
            //throw new $e('Message');
		}
	}

}
