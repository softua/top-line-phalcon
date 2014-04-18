<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:53
 */

$di->set('router', function() {
	$router = new \Phalcon\Mvc\Router(false);

	$router->setDefaults([
		'namespace' => 'App\Controllers'
	]);

	$router->notFound([
		'controller' => 'index',
		'action' => 'index'
	]);

	$router->add(
		'/',
		[
			'controller' => 'index',
			'action' => 'index'
		]
	);

	$router->add(
		'/:controller/:action',
		[
			'controller' => 1,
			'action' => 2
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
		'/admin/user/{id}',
		[
			'controller' => 'admin',
			'action' => 'user'
		]
	);

	$router->add(
		'/admin/delete/user/{id}',
		[
			'controller' => 'admin',
			'action' => 'deleteUser'
		]
	);

	return $router;
});