<?php
/**
 * Created by Ruslan Koloskov
 * Date: 10.07.14
 * Time: 1:30
 */

namespace App\Models;
use App\Upload;

class ImageCategory extends Image
{
	/** @var string картинка для категории (110 x 110) */
	public $imgPath;

	public function onConstruct()
	{
		$this->setDI();
	}

	public function setPaths()
	{
		if ($this->imgPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__category.' . $this->extension);
			if (file_exists($path)) $this->imgPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__category.' . $this->extension);
			else $this->imgPath = false;
		}
	}

	public function isFileExists()
	{
		if (file_exists($this->_url->path('public_html/Uploads/db_images/' . $this->id . '__category.' . $this->extension)))
			return true;
		else
			return false;
	}

	public static function uploadFotosAndReturn($categoryId)
	{
		$file = new Upload($_FILES['fotos'], 'ru');

		if (!$file->file_is_image || !$categoryId || !preg_match('/\d+/', $categoryId)) {
			$file->clean();
			return null;
		}

		$countImages = self::query()
			->where('belongs = \'category\'')
			->andWhere('belongs_id = ?1', [1 => $categoryId])
			->execute()->count();

		$image = new self();
		$image->extension = $file->file_src_name_ext;
		$image->belongs = 'category';
		$image->belongs_id = $categoryId;
		$image->sort = $countImages;

		if ($image->dbSave()) {
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 110;
			$file->image_y = 110;
			$file->file_new_name_body = $image->id . '__category';
			$file->process($image->_url->path('public_html/Uploads/db_images/'));

			if ($file->processed) {
				$file->clean();
				$image->setPaths();
				return $image;
			}
			else {
				$file->clean();
				return null;
			}
		} else {
			$file->clean();
			return null;
		}
	}
}