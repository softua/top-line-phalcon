<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:53
 */

$di['router'] = function()
{
	$router = new \Phalcon\Mvc\Router(false);

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
			'controller' => 'index',
			'action' => 'index'
		]
	);

	$router->add(
		'/:controller/',
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