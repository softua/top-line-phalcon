<?php
/**
 * Created by Ruslan Koloskov
 * Date: 24.07.14
 * Time: 15:07
 */

namespace App\Controllers;


class SearchController extends BaseFrontController
{
	public function initialize()
	{
		parent::initialize();

		$this->tag->appendTitle('Поиск');
	}

	public function indexAction()
	{
		
	}
} 