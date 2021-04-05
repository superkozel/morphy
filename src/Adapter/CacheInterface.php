<?php


namespace Morphy\Adapter;


interface CacheInterface
{
	public function cacheSet($key, $value, $time);

	public function cacheGet($key);
}