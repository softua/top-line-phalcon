<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:53
 */

$di['router'] = function()
{
	$router = new \Phalcon\Mvc\Router(false);

	$router->removeExtraSlashes(true);

	$router->setDefaults([
		'namespace' => 'App\Controllers'
	]);

	$router->notFound([
		'controller' => 'admin',
		'action' => 'notfound'
	]);

	$router->add(
		'/',
		[
			'controller' => 'main',
			'action' => 'index'
		]
	);

	$router->add(
		'/:controller',
		[
			'controller' => 1,
			'action' => 'index'
		]
	);

	$router->add(
		'/:controller/:action/:params',
		[
			'controller' => 1,
			'action' => 2,
			'params' => 3
		]
	);

	return $router;
};