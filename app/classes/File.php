<?php
/**
 * Created by Ruslan Koloskov
 * Date: 03.07.14
 * Time: 15:29
 */

namespace App;


class File
{
	protected $_di;
	protected $_url;

	public $id;
	public $name;
	public $pathName;
	public $productId;


	public function setDi($di)
	{
		$this->_di = $di;
		$this->_url = $this->_di->get('url');
	}

	public static function getFilesByProduct($di, Product $prod)
	{
		$dbFiles = Models\ProductFileModel::query()
			->where('product_id = ?1')->bind([1 => $prod->id])
			->orderBy('name')
			->execute();

		if (!count($dbFiles)) return null;

		$files = [];
		foreach ($dbFiles as $file) {
			$newFile = new self();
			$newFile->setDi($di);
			$newFile->id = $file->id;
			$newFile->name = $file->name;
			$newFile->pathName = $file->pathname;
			$newFile->productId = $file->product_id;
			$files[] = $newFile;
		}

		if (count($files)) return $files;
		else return null;
	}
}