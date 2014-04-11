<?
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:28
 */

$loader = new \Phalcon\Loader();
$loader->registerDirs(
	[
		'../app/controllers/',
		'../app/models/',
		'../app/config/'
	]
)->register();