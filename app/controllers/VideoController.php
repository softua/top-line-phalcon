<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class VideoController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Видео');
		$this->view->active_link = 'video';
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not Found');

		$mainCategories = Models\CategoryModel::find([
			'parent_id = 0',
			'order' => 'sort, name'
		]);
		$mainCategoriesForView = [];
		for ($i = 0; $i < count($mainCategories); $i++)
		{
			$mainCategoriesForView[$i]['name'] = $mainCategories[$i]->name;
			$areThereChildrenCats = Models\CategoryModel::findFirst([
				'parent_id = :id:',
				'bind' => ['id' => $mainCategories[$i]->id]
			]);
			if ($areThereChildrenCats)
			{
				$mainCategoriesForView[$i]['path'] = '/catalog/show/' . $mainCategories[$i]->seo_name . '/';

			} else {

				$mainCategoriesForView[$i]['path'] = '/products/list/' . $mainCategories[$i]->seo_name . '/';
			}
		}

		$this->view->sidebar_categories = $mainCategoriesForView;

		echo $this->view->render('video/notfound');
	}

	public function indexAction()
	{
		// Список видео
		$videos = Models\PageModel::find([
			'type_id = 3 AND public = 1',
			'order' => 'time DESC'
		]);
		if (!$videos)
			$videosForView = null;
		else {
			$videosForView = [];
			foreach ($videos as $video) {
				$tempVideo = [];
				$tempVideo['name'] = $video->name;
				$tempVideo['short_content'] = $video->short_content;
				$tempVideo['video_content'] = $video->video_content;
				$tempVideo['seo_name'] = $video->seo_name;
				$videosForView[] = $tempVideo;
			}
		}

		$this->view->videos = $videosForView;
		$this->view->sidebar_categories = \App\Category::getMainCategories($this->di, false);

		echo $this->view->render('video/list');
	}
}