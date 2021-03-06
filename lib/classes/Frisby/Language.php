<?php


namespace FrisbyModule\Frisby;


/**
 * Class Language
 * @package FrisbyModule\Frisby
 */
class Language
{

	public $currentLang;

	private $defaultLang;

	/**
	 * @return mixed
	 */
	public function getDefaultLang()
	{
		return $this->defaultLang;
	}


	/**
	 * @param string|null $currentLang
	 */
	public function setCurrentLang($currentLang)
	{

		$cookie = new Cookie($_COOKIE);
		$this->currentLang = $currentLang;

		$cookie->forever('lang', $currentLang);
	}

	/**
	 * Language constructor.
	 */
	public function __construct()
	{
		global $loader, $config, $app;
		$cookie = new Cookie($_COOKIE);
		$this->defaultLang = $config->get('defaultLang');
		$this->setCurrentLang($app->detectLang($this));
		require (file_exists($loader->lang($this->currentLang))) ? $loader->lang($this->currentLang) : $loader->lang($this->getDefaultLang());

	}

	/**
	 * @param $str
	 * @return mixed
	 */
	public function __($str)
	{
		return isset($GLOBALS['language'][$str]) ? $GLOBALS['language'][$str] : $str;
	}

	/**
	 * @return array
	 */
	public static function getAvailableLangs(){
		$dir = realpath('.').'\lib\languages';
		$res = [];
		if ($handle = opendir($dir)) {
			while ($file = readdir($handle)) {
				if (!is_dir($file)) {
					$res[] = explode('.',$file)[0];
				}
			}
		}
		asort($res,SORT_ASC);
		$result = [];
		foreach($res as $re){
			$result[] = $re;
		}
		return $result;
	}

	/**
	 * Generates link tags for Multilingual SEO
	 * @return string
	 */
	public static function generateHrefLang(){
		global $app,$lang;
		$html = "";
		foreach (self::getAvailableLangs() as $language) {
			if($language != $lang->getDefaultLang()) {
				$html .= '<link rel="alternate" href="' . $app->go($language) . '" hreflang="' . $language . '">' . PHP_EOL;
			}else{
				$html .= '<link rel="alternate" href="' . $app->go() . '" hreflang="' . $language . '">' . PHP_EOL;
			}
		}
		return $html;
	}


}