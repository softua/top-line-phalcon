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
		$news = Models\PageModel::find([
			'type_id = 4 AND public = 1',
			'order' => 'time DESC',
			'limit' => 3
		]);
		if (count($news)) {
			$newsForView = [];
			foreach ($news as $item) {
				$tempNews = [];
				$tempNews['name'] = $item->name;
				$tempNews['date'] = date('d.m.Y', strtotime($item->time));
				$tempNews['date-2'] = date('Y-m-d', strtotime($item->time));
				$tempNews['short_content'] = $item->short_content;
				$tempNews['href'] = $this->url->get('company/news/') . $item->seo_name;
				$newsForView[] = $tempNews;
			}
		} else {
			$newsForView = null;
		}

		$this->view->news = $newsForView;

		echo $this->view->render('main');
	}
}