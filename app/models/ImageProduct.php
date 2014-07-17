<?php
/**
 * Created by Ruslan Koloskov
 * Date: 10.07.14
 * Time: 1:30
 */

namespace App\Models;
use App\Upload;

class ImageProduct extends Image
{
	/** @var string оригинал картинки */
	public $imgOriginPath;
	/** @var string оригинал с водяным знаком */
	public $imgOriginWPath;
	/** @var string картинка для описания товара (290x300) */
	public $imgDescriptionPath;
	/** @var string картинка для миниатюры в описании товара (55x47) */
	public $imgThumbPath;
	/** @var string картинка для списка товаров (155x155) */
	public $imgListPath;
	/** @var string миниатюра для панели "Лидеры продаж" (198x160) */
	public $imgTopPath;
	/** @var string картинка для миниатюры в админке (250x150) */
	public $imgAdminPath;


	public function setPaths()
	{
		if ($this->imgOriginPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__original.' . $this->extension);
			if (file_exists($path)) $this->imgOriginPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__original.' . $this->extension);
			else $this->imgOriginPath = false;
		}

		if ($this->imgOriginWPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__original_w.' . $this->extension);
			if (file_exists($path)) $this->imgOriginWPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__original_w.' . $this->extension);
			else $this->imgOriginWPath = false;
		}

		if ($this->imgDescriptionPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_description.' . $this->extension);
			if (file_exists($path)) $this->imgDescriptionPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__product_description.' . $this->extension);
			else $this->imgDescriptionPath = false;
		}

		if ($this->imgThumbPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_thumb.' . $this->extension);
			if (file_exists($path)) $this->imgThumbPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__product_thumb.' . $this->extension);
			else $this->imgThumbPath = false;
		}

		if ($this->imgListPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_list.' . $this->extension);
			if (file_exists($path)) $this->imgListPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__product_list.' . $this->extension);
			else $this->imgListPath = false;
		}

		if ($this->imgTopPath === null) {
			$path = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_top.' . $this->extension);
			if (file_exists($path)) $this->imgTopPath = $this->_url->getStatic('Uploads/db_images/' . $this->id . '__product_top.' . $this->extension);
			else $this->imgTopPath = false;
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
	public function deleteImages()
	{
		// оригинал картинки
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__original.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// оригинал картинки с водяным знаком
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__original_w.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// картинка для описания товара
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_description.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// картинка для миниатюры в описании товара
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_thumb.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// картинка для списка товаров
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_list.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// миниатюра для панели "Лидеры продаж"
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__product_top.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		// картинка для миниатюры в админке
		$pathName = $this->_url->path('public_html/Uploads/db_images/' . $this->id . '__admin_thumb.' . $this->extension);
		if (file_exists($pathName)) {
			parent::deleteFiles($pathName);
		}

		if ($this->delete()) return true;
		else return false;
	}

	/**
	 * @param int $productId
	 * @return ImageProduct | false | null
	 */
	public static function uploadImageAndReturn($productId)
	{
		$file = new Upload($_FILES['fotos'], 'ru');

		if (!$file->file_is_image || !preg_match('/\d+/', $productId)) {
			$file->clean();
			return null;
		}

		$sort = self::query()
			->where('belongs = \'product\'')
			->andWhere('belongs_id = ?1', [1 => $productId])
			->execute()->count();

		$image = new self();
		$image->setDI();
		$image->belongs = 'product';
		$image->belongs_id = $productId;
		$image->extension = $file->file_src_name_ext;
		$image->sort = $sort;

		if ($image->dbSave()) {

			$path = \Phalcon\DI::getDefault()->get('url');

			// Загружаем оригинальный файл

			$file->file_new_name_body = $image->id . '__original';
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем оригинальный файл с водяным знаком

			$file->file_new_name_body = $image->id . '__original_w';
			$file->image_watermark = $path->getStatic('img/watermark.png');
			$file->image_watermark_position = 'TL';
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем картинку для описания товара

			$file->file_new_name_body = $image->id . '__product_description';
			$file->image_watermark = $path->getStatic('img/watermark.png');
			$file->image_watermark_position = 'TL';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 290;
			$file->image_y = 300;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем миниатюру для описания товара

			$file->file_new_name_body = $image->id . '__product_thumb';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 55;
			$file->image_y = 47;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем картинку для списка товаров

			$file->file_new_name_body = $image->id . '__product_list';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 155;
			$file->image_y = 155;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем картинку для панели "Лидеры продаж"

			$file->file_new_name_body = $image->id . '__product_top';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 190;
			$file->image_y = 160;
			$file->process($path->path('public_html/Uploads/db_images/'));
			if (!$file->processed) {
				$file->clean();
				return false;
			}

			// Загружаем миниатюру для админки

			$file->file_new_name_body = $image->id . '__admin_thumb';
			$file->image_resize = true;
			$file->image_ratio = true;
			$file->image_x = 250;
			$file->image_y = 150;
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