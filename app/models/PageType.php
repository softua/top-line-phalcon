<?php
/**
 * Created by Ruslan Koloskov
 * Date: 20.06.14
 * Time: 12:16
 */

namespace App\Models;


class PageType extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $name;
	public $full_name;

	public $dbFields = ['id', 'name', 'full_name'];

	public function getSource()
	{
		return 'pages_types';
	}

	public function initialize()
	{
		$this->hasMany('id', '\App\Models\PageModel', 'type_id', [
			'alias' => 'pages'
		]);

		$this->setDI();
		$this->useDynamicUpdate(true);
	}

	public function setDI($di = null)
	{
		if (!$this->_di) $this->_di = \Phalcon\DI::getDefault();
		if (!$this->_url) $this->_url = $this->_di->get('url');
	}

	public function dbSave($data = null)
	{
		return $this->save(null, $this->dbFields);
	}
}