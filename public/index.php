<?

try {
	// Отключаем PHP сессии
	ini_set('session.auto_start', '0');
	ini_set('session.use_cookies', '0');

	define('BASE_URL', '../app/');

	// Создание DI
	$di = new Phalcon\DI\FactoryDefault();

	// Подключаем конфигурацию как сервис
	require BASE_URL . 'config/config.php';
	$di->set('config', function() use ($settings) {
		return new \Phalcon\Config($settings);
	}, true);

    // Регистрация автозагрузчика
	$loader = new \Phalcon\Loader();
	$loader->registerNamespaces([
		'App' => BASE_URL . 'classes/',
		'App\Controllers' => BASE_URL . 'controllers/',
		'App\Models' => BASE_URL . 'models/'
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

	// Collection Manager
	$di->set('collectionManager', function(){
		return new Phalcon\Mvc\Collection\Manager();
	}, true);

	// Сервис для работы с MongoDB
	$di->set('mongo', function() use($di) {
		$username = $di->get('config')->mongo->username;
		$password = $di->get('config')->mongo->password;
		$host = $di->get('config')->mongo->host;
		$dbname = $di->get('config')->mongo->dbname;

		$connectionString = 'mongodb://' . $username . ':' . $password . '@' . $host;
		$connectionString2 = 'mongodb://' . $host;
		$mongo = new Mongo($connectionString2);
		return $mongo->selectDb($dbname);
	}, true);

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

	// Шифрование
	$di->set('crypt', function() use($di) {
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey($di->get('config')->secret);
		$crypt->setMode('ecb');
		return $crypt;
	}, true);

	// Куки
	$di->set('cookies', function() {
		$cookies = new \Phalcon\Http\Response\Cookies();
		$cookies->useEncryption(true);
		return $cookies;
	}, true);

	// Роуты
	require BASE_URL . 'config/Routes.php';

    // Обработка запроса
    $application = new \Phalcon\Mvc\Application($di);

	$application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}