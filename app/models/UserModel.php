<?
namespace App\Models;

class UserModel extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'users';
	}

	public function initialize()
	{
		$this->belongsTo('role_id', '\App\Models\Role', 'id', [
			'alias' => 'role'
		]);
	}

	public $id;
	public $login;
	public $password;
	public $name;
	public $email;
	public $role_id;
}