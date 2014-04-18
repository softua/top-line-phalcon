<?
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:17
 */

namespace App\Controllers;
class BaseFrontController extends \Phalcon\Mvc\Controller
{
	public function initialize ()
	{
		$this->tag->setDoctype(\Phalcon\Tag::HTML5);
		$this->tag->setTitle($this->di->get('config')->name);
		$this->tag->setTitleSeparator(' :: ');

		$this->env = $this->config->env;
	}
}