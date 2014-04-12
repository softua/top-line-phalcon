<?

try {

    // Регистрация автозагрузчика
	require __DIR__ . '/../app/config/Loader.php';

    // Создание DI
    $di = new Phalcon\DI\FactoryDefault();

    // Файл подключения сервиса для работы с БД
    require __DIR__ . '/../app/config/Db.php';

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