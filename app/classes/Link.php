<?php
/**
 * Created by Ruslan Koloskov
 * Date: 04.07.14
 * Time: 2:22
 */

namespace App;


class Link
{
	protected $_di;
	protected $_url;
	public $name;
	public $active = false;
	public $href;
	public $title;

	public function __construct($di)
	{
		$this->setDi($di);
	}

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public function generateUrl($url, $page)
	{
		$this->href = $this->_url->get($url . $page);
	}
} 