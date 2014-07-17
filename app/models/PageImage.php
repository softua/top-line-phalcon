<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 21:20
 */

namespace App\Models;


class PageImage
{
	protected $_di;
	protected $_url;

	public $id;
	public $extension;
	public $pageId;
	public $sort;
	public $pageDescriptionPath;
	public $pageListPath;
	public $adminThumbPath;

	private function _setPath()
	{
		// Page description
		if ($this->descriptionPath === null) {
			$path = 'staticPages/images/' . $this->id . '__page_description.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->pageDescriptionPath = $this->_url->getStatic($path);
			} else {
				$this->pageDescriptionPath = false;
			}
		}
		// Page list
		if ($this->pageListPath === null) {
			$path = 'staticPages/images/' . $this->id . '__page_list.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->pageListPath = $this->_url->getStatic($path);
			} else {
				$this->pageListPath = false;
			}
		}
		// Admin thumb
		if ($this->adminThumbPath === null) {
			$path = 'staticPages/images/' . $this->id . '__admin_thumb.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->adminThumbPath = $this->_url->getStatic($path);
			} else {
				$this->adminThumbPath = false;
			}
		}
	}

	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public function setPath()
	{
		if ($this->pageDescriptionPath === null || $this->pageListPath === null || $this->adminThumbPath === null) {
			$this->_setPath();
		}
	}
}