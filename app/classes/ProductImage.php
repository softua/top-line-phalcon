<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 21:20
 */

namespace App;


class ProductImage
{
	protected $_di;
	protected $_url;

	public $id;
	public $extension;
	public $productId;
	public $sort;

	// Варианты картинок
	public $adminThumbPath;
	public $originalPath;
	public $originalWatermarkedPath;
	public $productDescriptionPath;
	public $productListPath;
	public $productThumbPath;
	public $productTopPath;


	private function _setPath()
	{
		// Admin thumb
		if ($this->adminThumbPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__admin_thumb.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->adminThumbPath = $this->_url->getStatic($path);
			} else {
				$this->adminThumbPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Original
		if ($this->originalPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__original.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->originalPath = $this->_url->getStatic($path);
			} else {
				$this->originalPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Original watermarked
		if ($this->originalWatermarkedPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__original_w.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->originalWatermarkedPath = $this->_url->getStatic($path);
			} else {
				$this->originalWatermarkedPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Product description
		if ($this->productDescriptionPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__product_description.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->productDescriptionPath = $this->_url->getStatic($path);
			} else {
				$this->productDescriptionPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Product list
		if ($this->productListPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__product_list.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->productListPath = $this->_url->getStatic($path);
			} else {
				$this->productListPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Product thumbnail
		if ($this->productThumbPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__product_thumb.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->productThumbPath = $this->_url->getStatic($path);
			} else {
				$this->productThumbPath = $this->_url->getStatic('img/no_foto.png');
			}
		}

		// Product top
		if ($this->productTopPath === null) {
			$path = 'products/' . $this->productId . '/images/' . $this->id . '__product_top.' . $this->extension;
			if (file_exists($this->_url->path('public_html/' . $path))) {
				$this->productTopPath = $this->_url->getStatic($path);
			} else {
				$this->productTopPath = $this->_url->getStatic('img/no_foto.png');
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
		if (!$this->adminThumbPath) {
			$this->_setPath();
		}
	}
}