<?
namespace App\Models;

class User extends \Phalcon\Mvc\Model
{
	protected $_di;
	protected $_url;

	//DB properties
	public $id;
	public $login;
	public $password;
	public $name;
	public $email;
	public $role_id;

	public $dbFields = ['id', 'login', 'password', 'name', 'email', 'role_id'];

	public function getSource()
	{
		return 'users';
	}

	public function initialize()
	{
		$this->belongsTo('role_id', '\App\Models\RoleModel', 'id', [
			'alias' => 'role'
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