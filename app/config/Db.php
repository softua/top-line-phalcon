<?php
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:50
 */

// Настраиваем сервис для работы с БД
$di->set('db', function(){
	return new \Phalcon\Db\Adapter\Pdo\Mysql([
		"host" => "localhost",
		"username" => "softua",
		"password" => "7TWaskME",
		"dbname" => "phalcon-tutorial"
	]);
});