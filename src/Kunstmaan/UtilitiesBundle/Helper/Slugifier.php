<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

/**
 * Sulgifier is a helper to slugify a certain string
 */
class Slugifier
{
	/**
	 * Convert russian to translit
	 * @param $string
	 * @return string
	 */
	public function rus2translit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',    'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
			'ä' => 'a',   'á' => 'a',   'à' => 'a',
			'å' => 'a',   'é' => 'e',   'è' => 'e',
			'ë' => 'e',   'í' => 'i',   'ì' => 'i',
			'ï' => 'i',   'ó' => 'o',   'ò' => 'o',
			'ö' => 'o',   'ú' => 'u',   'ù' => 'u',
			'ü' => 'u',   'ñ' => 'n',   'ß' => 'ss',
			'æ' => 'ae',  'õ' => 'o',
		);
		return strtr(strtolower($string), $converter);
	}

    /**
     * Slugify a string
     *
     * @param string $text    Text to slugify
     * @param string $default Default return value (override when slugify would return an empty string)
     *
     * @return string
     */
    public static function slugify($text, $default = 'n-a', $replace = array("'"), $delimiter = '-')
    {
        if (!empty($replace)) {
            $text = str_replace($replace, ' ', $text);
        }

        // transliterate
        if (function_exists('iconv')) {
            $previouslocale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'en_US.UTF8');
			
			//convert russian letters to translit
			$slugifier = new Slugifier();
			$text = $slugifier->rus2translit($text);
			
            $text = iconv('utf-8', 'us-ascii//IGNORE//TRANSLIT', $text);
            setlocale(LC_CTYPE, $previouslocale);
        }

        $text = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
        $text = strtolower(trim($text, $delimiter));
        $text = preg_replace("/[\/_|+ -]+/", $delimiter, $text);

        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}
