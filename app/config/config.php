<?php
/**
 * Created by Ruslan Koloskov
 * Date: 14.04.14
 * Time: 10:46
 */

$settings = [
	'name' => 'Топ-линия',
//	'env' => 'production',
	'env' => 'development',
	'secret' => 'dsf4iorj23i%fdsdgdsadferfd89wej',
	'database' => [
		'host' => 'localhost',
		'username' => 'softua',
		'password' => '7TWaskME',
		'dbname' => 'phalcon-tutorial'
	],

	'mongo' => [
		'host' => 'localhost:27017',
		'username' => 'softua',
		'password' => '7TWaskME',
		'dbname' => 'top-line'
	],

	'app' => [
		'controllers' => 'controllers/',
		'models' => 'models/'
	]
];