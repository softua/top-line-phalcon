<?php
/**
 * Created by Ruslan Koloskov
 * Date: 22.07.14
 * Time: 22:10
 */

namespace App\Models;


class Client extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $email;
	public $phone;

	public $dbFields = ['id', 'email', 'phone'];

	public function getSource()
	{
		return 'clients';
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