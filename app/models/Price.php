<?php
/**
 * Created by Ruslan Koloskov
 * Date: 21.07.14
 * Time: 21:36
 */

namespace App\Models;


class Price extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $pathname;
	public $created;

	public $dbFields = ['id', 'pathname', 'created'];

	public function getSource()
	{
		return 'prices';
	}

	public function initialize()
	{
		$this->useDynamicUpdate(true);
	}

	public function onConstruct()
	{
		$this->setDI();
	}

	public function setDI($di = null)
	{
		if ($this->_di === null) $this->_di = \Phalcon\DI::getDefault();
		if ($this->_url === null) $this->_url = $this->_di->get('url');
	}

	public function dbSave($data = null)
	{
		if ($this->save($data, $this->dbFields)) return true;
		else return false;
	}
} 