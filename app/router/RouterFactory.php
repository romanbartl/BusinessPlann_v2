<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
        $router[] = new Route('<locale cs|en|ru>/sign/<action>', array('presenter' => 'Sign'));
        $router[] = new Route('<locale cs|en|ru>/home', array('presenter' => 'Homepage', 'action' => 'default'));
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:redirect');

        return $router;
	}

}
