<?

namespace App\Controllers;

use App\Models;

class MainController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Главная');
		$this->view->active_link = 'main';
	}

	public function indexAction()
	{
		echo $this->view->render('main');
	}
}