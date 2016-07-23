<?php
/**
 * Created by PhpStorm.
 * User: Roxem
 * Date: 23.07.2016
 * Time: 10:05
 */

namespace App\Model;

use Nette;


class BaseManager extends Nette\Object
{
    /** @persistent */
    public $locale;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;

    public function __construct(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }
}