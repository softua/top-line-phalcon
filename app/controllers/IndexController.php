<?

namespace App\Controllers;

class IndexController extends BaseFrontController
{
	public function indexAction()
	{
		$this->tag->appendTitle('Главная');

		$this->view->setVars([
			'env' => $this->env
		]);
		echo $this->view->render('index');
	}
}