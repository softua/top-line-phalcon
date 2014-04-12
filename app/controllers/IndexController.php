<? 

class IndexController extends BaseController
{
	public function indexAction()
	{
		$this->tag->appendTitle('Главная');

		echo $this->view->render('index/index');
	}
}