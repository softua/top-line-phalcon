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

		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('video/notfound');
	}

	public function indexAction()
	{
		// Список видео
		$videos = Models\Video::getVideos();

		$this->view->videos = $videos;
		$this->view->sidebar_categories = Models\Category::getMainCategories();

		echo $this->view->render('video/list');
	}
}