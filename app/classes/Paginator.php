<?php
/**
 * Created by Ruslan Koloskov
 * Date: 25.06.14
 * Time: 9:59
 */

namespace App;


class Paginator
{
	protected $_di;
	protected $_url;

	private $_paginator;
	public $items = [];
	public $before;
	public $first;
	public $next;
	public $last;
	public $current;
	public $totalPages;
	public $totalItems;
	public $links = [];

	public function __construct($data, $limit = 10, $page = 1)
	{
		$this->setDI();
		$this->_paginator = new \Phalcon\Paginator\Adapter\NativeArray([
			'data' => $data,
			'limit' => $limit,
			'page' => $page
		]);
	}

	public function setDI($di = null)
	{
		$this->_di = \Phalcon\DI::getDefault();
		$this->_url = $this->_di->get('url');
	}

	public function paginate($linkText)
	{
		$paginator = $this->_paginator->getPaginate();
		$this->items = $paginator->items;
		$this->before = $paginator->before;
		$this->first = $paginator->first;
		$this->next = $paginator->next;
		$this->last = $paginator->last;
		$this->current = $paginator->current;
		$this->totalPages = $paginator->total_pages;
		$this->totalItems = $paginator->total_items;

		if ($this->totalPages > 1) {
			for ($i = 0; $i < $this->totalPages; $i++) {
				$link = new Link();
				$link->generateUrl($linkText, $i+1);
				$link->name = $i+1;
				if ($i+1 == $this->current) {
					$link->active = true;
				}
				$this->links[] = $link;
			}
			if ($this->current != $this->first) {
				$firstLink = new Link();
				$firstLink->name = '←';
				$firstLink->generateUrl($linkText, $this->first);
				array_unshift($this->links, $firstLink);
			}
			if ($this->current != $this->last) {
				$lastLink = new Link();
				$lastLink->name = '→';
				$lastLink->generateUrl($linkText, $this->last);
				array_push($this->links, $lastLink);
			}
		}
		return $this;
	}
}