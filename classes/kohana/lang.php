<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Lang {

	/**
	 * @var  string  hard-coded default language, must match a language key from lang config file
	 */
	public static $default = 'en';

	/**
	 * @var  string  name of the cookie that contains language
	 */
	public static $cookie = 'lang';

	/**
	 * Looks for the best default language available and returns it.
	 * A language cookie and HTTP Accept-Language headers are taken into account.
	 *
	 *     $lang = Lang::default();
	 *
	 * @return  string  language key, e.g. "en", "fr", "nl", etc.
	 */
	public static function find_default()
	{
		// All supported languages
		$langs = (array) Kohana::config('lang');

		// Look for language cookie first
		if ($lang = Cookie::get(Lang::$cookie))
		{
			// Valid language found in cookie
			if (isset($langs[$lang]))
				return $lang;

			// Delete cookie with invalid language
			Cookie::delete(Lang::$cookie);
		}

		// Parse HTTP Accept-Language headers
		foreach (Request::accept_lang() as $lang => $quality)
		{
			// Return the first language found (the language with the highest quality)
			if (isset($langs[$lang]))
				return $lang;
		}

		// Return the hard-coded default language as final fallback
		return Lang::$default;
	}

}
