<?

try {

	// Создание DI
	$di = new Phalcon\DI\FactoryDefault();

	// Подключаем конфигурацию как сервис
	require '../app/config/config.php';
	$di->set('config', function() use ($settings) {
		return new \Phalcon\Config($settings);
	});

    // Регистрация автозагрузчика
	$loader = new \Phalcon\Loader();
	$loader->registerDirs([
		$di->getShared('config')->app->controllers,
		$di->getShared('config')->app->models
	])->register();

    // Сервиса для работы с БД
    $di->set('db', function($di) {
	    return new \Phalcon\Db\Adapter\Pdo\Mysql([
		    'host' => $di->getShared('config')->database->host,
		    'username' => $di->getShared('config')->database->username,
		    'password' => $di->getShared('config')->database->password,
		    'dbname' => $di->getShared('config')->database->dbname
	    ]);
    });

	// Шаблонизатор Volt в DI
	$di->set('voltService', function($view, $di) {

		$volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

		$volt->setOptions(array(
			"compiledExtension" => ".compiled"
		));

		return $volt;
	});

    // Настраиваем компонент View/Simple с шаблонизатором
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View\Simple();
        $view->setViewsDir('../app/views/');
	    $view->registerEngines([
		    '.volt' => 'voltService'
	    ]);
        return $view;
    });

	// Роуты
	require __DIR__ . '/../app/config/Routes.php';

    // Обработка запроса
    $application = new \Phalcon\Mvc\Application($di);

	$application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}