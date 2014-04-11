<?

try {

    // Регистрация автозагрузчика
	require __DIR__ . '/../app/config/Loader.php';

    // Создание DI
    $di = new Phalcon\DI\FactoryDefault();

    // Файл подключения сервиса для работы с БД
    require __DIR__ . '/../app/config/Db.php';

    // Настраиваем компонент View
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir('../app/views/');
        return $view;
    });

	// Роуты
	require __DIR__ . '/../app/config/Routes.php';

    // Обработка запроса
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}