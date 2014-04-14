<?
/**
 * Created by Ruslan Koloskov
 * Date: 11.04.14
 * Time: 13:17
 */

class BaseController extends \Phalcon\Mvc\Controller
{
	public function initialize ()
	{
		$this->tag->setDoctype(\Phalcon\Tag::HTML5);
		$this->tag->setTitle('Топ-линия');
		$this->tag->setTitleSeparator(' :: ');

		//TODO: Перенести это в конфигурацию.
		$this->env = 'development';
	}
} 