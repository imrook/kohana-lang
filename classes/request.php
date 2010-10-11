<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {

	/**
	 * @var  string  the language of the main request
	 */
	public static $lang;

	/**
	 * Extension of the main request singleton instance. If none given, the URI will
	 * be automatically detected. If the URI contains no language segment, the user
	 * will be redirected to the same URI with the default language prepended.
	 * If the URI does contain a language segment, I18n and locale will be set.
	 * Also, a cookie with the current language will be set. Finally, the language
	 * segment is chopped off the URI and normal request processing continues.
	 *
	 * @param   string   URI of the request
	 * @return  Request
	 * @uses    Request::detect_uri
	 */
	public static function instance( & $uri = TRUE)
	{
		// All supported languages
		$langs = (array) Kohana::config('lang');

		if ($uri === TRUE)
		{
			// We need the current URI
			$uri = Request::detect_uri();
		}

		// Normalize URI
		$uri = ltrim($uri, '/');

		// Look for a supported language in the first URI segment
		if ( ! preg_match('~^(?:'.implode('|', array_keys($langs)).')(?=/|$)~i', $uri, $matches))
		{
			// Find the best default language
			$lang = Lang::find_default();

			// Use the default server protocol
			$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

			// Redirect to the same URI, but with language prepended
			header($protocol.' 302 Found');
			header('Location: '.URL::base(TRUE, TRUE).$lang.'/'.$uri);

			// Stop execution
			exit;
		}

		// Language found in the URI
		Request::$lang = strtolower($matches[0]);

		// Store target language in I18n
		I18n::$lang = $langs[Request::$lang]['i18n_code'];

		// Set locale
		setlocale(LC_ALL, $langs[Request::$lang]['locale']);

		// Update language cookie if needed
		if (Cookie::get(Lang::$cookie) !== Request::$lang)
		{
			Cookie::set(Lang::$cookie, Request::$lang);
		}

		// Remove language from URI
		$uri = (string) substr($uri, strlen(Request::$lang));

		// Continue normal request processing with the URI without language
		return parent::instance($uri);
	}

}
