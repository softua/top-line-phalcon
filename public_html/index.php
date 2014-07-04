<?

try {
	// Отключаем PHP сессии
	ini_set('session.auto_start', '0');
	ini_set('session.use_cookies', '0');

	// Создание DI
	$di = new \Phalcon\DI\FactoryDefault();

	// URI
	$di->setShared('url', function() {
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('http://' . $_SERVER['HTTP_HOST'] .'/');
		$url->setStaticBaseUri('http://' . $_SERVER['HTTP_HOST'] . '/public_html/');
		$url->setBasePath($_SERVER['DOCUMENT_ROOT'] . '/');
		return $url;
	});

    // Регистрация автозагрузчика
	$loader = new \Phalcon\Loader();
	$loader->registerNamespaces([
		'App' => $di['url']->path('app/') . 'classes/',
		'App\Controllers' => $di['url']->path('app/') . 'controllers/',
		'App\Models' => $di['url']->path('app/') . 'models/'
	])->register();

	// Подключаем конфигурацию как сервис
	$di->setShared('config', function() {
		return new \App\Config('development');
	});

    // Сервис для работы с БД
	$di->setShared('db', function() use($di)
	{
		return new \Phalcon\Db\Adapter\Pdo\Mysql([
			'host' => $di['config']->db['host'],
			'username' => $di['config']->db['username'],
			'password' => $di['config']->db['password'],
			'dbname' => $di['config']->db['dbname'],
			'options' => [
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
			]
		]);
	});

	// Collection Manager
	$di->set('collectionManager', function(){
		return new \Phalcon\Mvc\Collection\Manager();
	}, true);

	// Шаблонизатор Volt в DI
	$di->setShared('voltService', function($view, $di) {

		$volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

		$volt->setOptions(array(
			"compiledExtension" => ".compiled"
		));

		return $volt;
	});

    // Настраиваем компонент View/Simple с шаблонизатором
    $di->setShared('view', function(){
        $view = new \Phalcon\Mvc\View\Simple();
        $view->setViewsDir('../app/views/');
	    $view->registerEngines([
		    '.volt' => 'voltService'
	    ]);
        return $view;
    });

	// Шифрование
	$di->setShared('crypt', function() use($di) {
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey($di['config']->secret);
		$crypt->setMode('ecb');
		return $crypt;
	});

	// Куки
	$di->setShared('cookies', function() {
		$cookies = new \Phalcon\Http\Response\Cookies();
		$cookies->useEncryption(true);
		return $cookies;
	});

	// Роуты
	require $di['url']->path('app/classes/Routes.php');

    // Обработка запроса
    $application = new \Phalcon\Mvc\Application($di);

	$application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}