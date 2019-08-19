<?php

namespace Morphy;

use Curl\Curl;

class Morphy
{
	protected static $_adapter;

	public static function setAdapter($adapter)
	{
		static::$_adapter = $adapter;
	}

	public static function getAdapter()
	{
		return static::$_adapter;
	}

	protected static $config = [
	    'url' => 'https://ws3.morpher.ru',
    ];

	public static function config($attr, $value = null) {
        if (! is_null($value)) {
            static::$config[$attr] = $value;
        }
        else {
            return static::$config[$attr];
        }
    }

	public static function install()
	{
		static::getAdapter()->execute(
			"CREATE TABLE IF NOT EXISTS `morphy` (
			  `id` int(11) NOT NULL auto_increment,
			  `word` varchar(128) NOT NULL,
			  `word_r` varchar(128) NOT NULL,
			  `word_d` varchar(128) NOT NULL,
			  `word_v` varchar(128) NOT NULL,
			  `word_t` varchar(128) NOT NULL,
			  `word_p` varchar(128) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8"
		);

		static::getAdapter()->execute(
			"CREATE TABLE IF NOT EXISTS `morphy_ch` (
			  `id` int(11) NOT NULL auto_increment,
			  `edch` varchar(128) NOT NULL,
			  `mnch` varchar(128) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8"
		);
	}

	/**
     * @return MorphyResult
     */
	public static function morph($word)
	{
		if (empty($word))
			dd('Empty morphy word');

		$row = static::getAdapter()->queryRow("select * from `morphy` where word='$word'");

		if ($row) {
			$m = array($row['word'], $row['word_r'], $row['word_d'], $row['word_v'], $row['word_t'], $row['word_p'], $word);
			return static::arr2form($m);
		}

		if (static::getAdapter()->cacheGet('morphy_limit_reached')) {
			static::debug('Мы уже знаем что лимит исчерпан');

			$m = array($word, $word, $word, $word, $word, $word, $word);
			return static::arr2form($m);
		}

        $curl = new Curl();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_TIMEOUT);

//			$proxy = array('http', '84.42.3.3', '3128');
//			if (!empty($proxy[0])) {
//				curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy[0]);
//				curl_setopt($ch, CURLOPT_PROXY, $proxy[1]);
//				curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[2]);
//			}
//

        $curl->get(static::config('url') . '/russian/declension', array(
            's' => mb_ereg_replace("\s", "%20", $word),
        ));

		static::debug('Пробуем получить склонение словосочетания через сервис');
		$result = $curl->response;
		static::debug($result);

		try {
			$resultArray = static::xml2array($result);
			static::debug($resultArray);
		}
		catch (\Exception $e) {
			$resultArray = false;
		}

		if (! $resultArray) {
			static::debug('Ошибка сервиса: ' . $result);

			static::getAdapter()->cacheSet('morphy_limit_reached', 1, 60*60*1);

			$m = array($word, $word, $word, $word, $word, $word, $word);
			return static::arr2form($m);
		}
		else if (! empty($resultArray['code'])) {
			if ($resultArray['code'] == 1) {
				static::getAdapter()->cacheSet('morphy_limit_reached', 1, 60*60*3);
				static::debug('Морфи сказал что лимит исперчан, кэшируем');
			}
			else {
				static::getAdapter()->cacheSet('morphy_limit_reached', 1, 60*60*1);
				static::debug('Неизвестная ошибка морфи');
			}

			$m = array($word, $word, $word, $word, $word, $word, $word);
			return static::arr2form($m);
		}
		else {
			$result = mb_ereg_replace("'", "&#39;", $result);

			preg_match("#<Р>([^<]+)</Р>\s*<Д>([^<]+)</Д>\s*<В>([^<]+)</В>\s*<Т>([^<]+)</Т>\s*<П>([^<]+)</П>#is", $result, $m);
			//var_static::debug($m);die();
			if (! empty($m[1])) {
				$saveOk = static::getAdapter()->
					execute("insert into morphy (word, word_r, word_d, word_v, word_t, word_p) values ('{$word}', '{$m[1]}', '{$m[2]}', '{$m[3]}', '{$m[4]}', '{$m[5]}')");

				if (! $saveOk)
					dd('error saving morphy row');

				$m[0] = $word;
				return static::arr2form($m);

			} else {
				$m = array($word, $word, $word, $word, $word, $word, $word);
				return static::arr2form($m);
			}
		}

		dd('Morphy unknown behaviour');
	}

	protected static function debug($v)
	{
        return;

		echo '<pre>';
		var_dump($v);
		echo '</pre>';
	}
	
	protected static function xml2array($xmlstring)
	{
		return json_decode(json_encode(simplexml_load_string($xmlstring)),true);
	}

	protected static function arr2form($m)
	{
		$form = [];

		$form['i'] = $m[0];
		$form['r'] = $m[1];
		$form['d'] = $m[2];
		$form['v'] = $m[3];
		$form['t'] = $m[4];
		$form['p'] = $m[5];

		return new MorphyResult($form);
	}

	function mn2ed($word)
	{
		$word = mb_strtolower($word, 'utf-8');
		$line = static::getAdapter()->queryRow("select * from morphy_ch where mnch='$word'");

		if ($line) {
			if (!empty($line['edch'])) {
				return $line['edch'];
			}
		} else {
			static::getAdapter()->execute("INSERT INTO morphy_ch (mnch,edch) VALUES ('$word', '')");
		}

		return $word;
	}

	function ed2mn($word)
	{
		$line = static::getAdapter()->queryRow("select * from morphy_ch where edch='$word'");
		if ($line) {
			return $line['mnch'];
		}

		return $word;
	}
}