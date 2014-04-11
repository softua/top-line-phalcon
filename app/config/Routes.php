<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:53
 */

$di->set('router', function() {
	$router = new \Phalcon\Mvc\Router(false);
	$router->setDefaults([
		'controller' => 'index',
		'action' => 'index'
	]);

	return $router;
});