<?php
/**
 * Created by Ruslan Koloskov
 * Date: 31.05.14
 * Time: 13:36
 */

namespace App;


class Config
{
	public $name;
	public $env;
	public $secret;
	public $cookie;
	public $mongo;
	public $db;

	public function __construct($env)
	{
		$this->name = 'Топ-линия';
		$this->env = $env;
		$this->secret = 'dsf4iorj23i%fdsdgdsadferfd89wej';
		$this->cookie['lifetime'] = 86400; // сутки

		if ($this->env == 'production')
		{
			$this->db = [
				'host' => 'localhost',
				'username' => 'softuane_admin',
				'password' => '7TWaskME',
				'dbname' => 'softuane_top_line'
			];

		} elseif ($this->env == 'development')
		{
			$this->db = [
				'host' => '176.9.36.18',
				'username' => 'softuane_admin',
				'password' => '7TWaskME',
				'dbname' => 'softuane_top_line'
			];
		}
	}
}