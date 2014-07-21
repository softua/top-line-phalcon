<?php
/**
 * Created by Ruslan Koloskov
 * Date: 18.06.14
 * Time: 21:57
 */

namespace App\Controllers;
use App\Models;

class ProjectsController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Проекты');
		$this->view->active_link = 'projects';
	}

	public function indexAction()
	{
		$this->view->projects = Models\Project::getProjects();
		$this->view->sidebar_categories = Models\Category::getMainCategories(false);

		echo $this->view->render('projects/list');
	}

	public function showAction()
	{
		$seoName = trim(strip_tags($this->dispatcher->getParams()[0]));
		if (!$seoName) {
			return $this->response->redirect('projects/notfound');
		}
		else {
			$page = Models\Project::getPageBySeoName($seoName);
			if (!$page) {
				return $this->response->redirect('projects/notfound');
			}
			else {
				$page->setImages();
			}
		}

		$this->view->sidebar_categories = Models\Category::getMainCategories(false);
		$this->view->project = $page;

		echo $this->view->render('projects/description');
	}

	public function notFoundAction()
	{
		$this->response->setStatusCode(404, 'Not Found');

		$this->view->sidebar_categories = Models\Category::getMainCategories(false);

		echo $this->view->render('projects/notfound');
	}
}