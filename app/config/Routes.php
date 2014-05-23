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

	$router->add(
		'/admin/getcategories/{parent}',
		[
			'controller' => 'admin',
			'action' => 'getcategories'
		]
	);

	$router->add(
		'/admin/category/add/{parent}',
		[
			'controller' => 'admin',
			'action' => 'addcategory'
		]
	);

	$router->add(
		'/admin/category/edit/{id}',
		[
			'controller' => 'admin',
			'action' => 'editcategory'
		]
	);

	$router->add(
		'/admin/category/delete/{id}',
		[
			'controller' => 'admin',
			'action' => 'deletecategory'
		]
	);

	$router->add(
		'/admin/getproducts/{id}',
		[
			'controller' => 'admin',
			'action' => 'getproducts'
		]
	);

	$router->add(
		'/admin/editseoname/{id}',
		[
			'controller' => 'admin',
			'action' => 'editseoname'
		]
	);

	$router->add(
		'/admin/editproduct/{id}',
		[
			'controller' => 'admin',
			'action' => 'editproduct'
		]
	);

	$router->add(
		'/admin/addcategorytoproduct/{categoryId}/{productId}',
		[
			'controller' => 'admin',
			'action' => 'addcategorytoproduct'
		]
	);

	$router->add(
		'/admin/deleteproductcategory/{categoryId}/{productId}',
		[
			'controller' => 'admin',
			'action' => 'deleteproductcategory'
		]
	);

	return $router;
});