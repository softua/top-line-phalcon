<?php
/**
 * Created by Ruslan Koloskov
 * Date: 10.07.14
 * Time: 1:30
 */

namespace App\Models;
use App,
	App\Upload;

class ImageInfo extends Image
{
	/** @var string картинка для описания страницы (500 x 358) */
	public $imgDescriptionPath;
	/** @var  string картинка для списка инфо страниц (173 x 131) */
	public $imgListPath;
	/** @var  string картинка для миниатюры в админке (250 x 150) */
	public $imgAdminPath;

	public function onConstruct()
	{
		$this->setDI();
	}

	public function setPaths()
	{
		if ($this->imgDescriptionPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__info_description.' . $this->extension);
			if (file_exists($path)) $this->imgDescriptionPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__info_description.' . $this->extension);
			else $this->imgDescriptionPath = false;
		}

		if ($this->imgListPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__info_list.' . $this->extension);
			if (file_exists($path)) $this->imgDescriptionPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__info_list.' . $this->extension);
			else $this->imgDescriptionPath = false;
		}

		if ($this->imgAdminPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__admin_thumb.' . $this->extension);
			if (file_exists($path)) $this->imgAdminPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__admin_thumb.' . $this->extension);
			else $this->imgAdminPath = false;
		}
	}

	/**
	 * @return bool
	 */
	public function deleteImage()
	{
		if ($this->delete()) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__info_description.' . $this->extension);
			if (file_exists($path)) parent::deleteFiles($path);

			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__info_list.' . $this->extension);
			if (file_exists($path)) parent::deleteFiles($path);

			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__admin_thumb.' . $this->extension);
			if (file_exists($path)) parent::deleteFiles($path);

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * @param int $infoId
	 * @return ImageProduct | false | null
	 */
	public static function uploadImageAndReturn($infoId)
	{
		$file = new Upload($_FILES['fotos'], 'ru');

		if (!$file->file_is_image || !preg_match('/\d+/', $infoId)) {
			$file->clean();
			return null;
		}

		$sort = self::query()
			->where('belongs = \'info\'')
			->andWhere('belongs_id = ?1', [1 => $infoId])
			->execute()->count();

		$image = new self();
		$image->belongs = 'info';
		$image->belongs_id = $infoId;
		$image->extension = $file->file_src_name_ext;
		$image->sort = $sort;

		if ($image->dbSave()) {

			$path = \Phalcon\DI::getDefault()->get('url');

			// Загружаем картинку для описания инфо страницы

			$file->file_new_name_body = $image->id . '__info_description';
			$file->image_watermark = $path->getStatic('img/watermark.png');
			$file->image_watermark_position = 'TL';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 500;
			$file->image_y = 358;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем картинку для списка инфо страниц

			$file->file_new_name_body = $image->id . '__info_list';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 173;
			$file->image_y = 131;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем миниатюру для админки

			$file->file_new_name_body = $image->id . '__admin_thumb';
			$file->image_resize = true;
			$file->image_x = 250;
			$file->image_ratio_y = true;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			$file->clean();
			$image->setPaths();
			return $image;
		}
		else {
			$file->clean();
			return null;
		}
	}
}