<?
namespace App\Models;

class User extends \Phalcon\Mvc\Model
{
	public function getSource()
	{
		return 'users';
	}
}