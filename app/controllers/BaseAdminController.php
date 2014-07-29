<?php
/**
 * Created by Ruslan Koloskov
 * Date: 14.04.14
 * Time: 16:30
 */

namespace App\Controllers;

class BaseAdminController extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->tag->setDoctype(\Phalcon\Tag::HTML5);

		$this->tag->setTitle($this->di['config']->name);
		$this->tag->setTitleSeparator(' :: ');

		$this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
	}
} 