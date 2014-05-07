<?php
/**
 * Created by Ruslan Koloskov
 * Date: 05.05.14
 * Time: 9:54
 */

namespace App;


class Translit
{
	public static function get_seo_keyword($str, $toLowerCase = false)
	{
		$tr = [
			"А" => "A", "а" => "a",
			"Б" => "B", "б" => "b",
			"В" => "V", "в" => "v",
			"Г" => "G", "г" => "g",
			"Д" => "D", "д" => "d",
			"Е" => "E", "е" => "e",
			"Ж" => "J", "ж" => "j",
			"З" => "Z", "з" => "z",
			"И" => "I", "и" => "i",
			"Й" => "Y", "й" => "y",
			"К" => "K", "к" => "k",
			"Л" => "L", "л" => "l",
			"М" => "M", "м" => "m",
			"Н" => "N", "н" => "n",
			"О" => "O", "о" => "o",
			"П" => "P", "п" => "p",
			"Р" => "R", "р" => "r",
			"С" => "S", "с" => "s",
			"Т" => "T", "т" => "t",
			"У" => "U", "у" => "u",
			"Ф" => "F", "ф" => "f",
			"Х" => "H", "х" => "h",
			"Ц" => "TS", "ц" => "ts",
			"Ч" => "CH", "ч" => "ch",
			"Ш" => "SH", "ш" => "sh",
			"Щ" => "SCH", "щ" => "sch",
			"Ъ" => "", "ъ" => "y",
			"Ы" => "", "ы" => "yi",
			"Ь" => "", "ь" => "y",
			"Э" => "E", "э" => "e",
			"Ю" => "YU", "ю" => "yu",
			"Я" => "YA", "я" => "ya",
			" " => "_",
			"." => ".",
			"/" => "_"
		];

		$res = strtr($str, $tr);

		if (preg_match('/[^A-Za-z0-9_\-]/', $res)) {
			$res = preg_replace('/[^A-Za-z0-9_\-]/', '', $res);
		}

		if($toLowerCase) return urlencode(strtolower($res));
		else return urlencode($res);
	}
}