<? 

class IndexController extends BaseController
{
	public function indexAction()
	{
		$this->tag->appendTitle('Главная');

		$this->view->setVar('env', $this->env);
		echo $this->view->render('index');
	}
}