<?php
/**
 * Created by PhpStorm.
 * User: LittlePoo
 * Date: 14.12.2015
 * Time: 10:11
 */

namespace Morphy\Adapter;

class MorphyLaravelAdapter implements CacheInterface, DbInterface
{
    /**
     * @var Illuminate\Database\Connection
     */
    protected $_connection;


    protected $_cache;

	/**
	 * @return MorphyLaravelAdapter
	 */
	public static function create(Illuminate\Database\Connection $connection)
	{
		return new MorphyLaravelAdapter($connection);
	}

	public function __construct(Illuminate\Database\Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
	 * @param string $sql
	 * @return array
	 */
	public function queryRow($sql)
	{
		return (array)$this->_connection->selectOne(\DB::raw($sql));
	}

	/**
	 * @param string $sql
	 * @return array
	 */
	public function queryAll($sql)
	{
		return $this->_connection->select(\DB::raw($sql));
	}

	/**
	 * @param string $sql
	 * @return mixed
	 */
	public function execute($sql)
	{
		return $this->_connection->statement($sql);
	}

	public function getError()
	{
	}

	public function cacheSet($key, $value, $time)
	{
		return \Cache::add($key, $value, $time);
	}

	public function cacheGet($key)
	{
		return \Cache::has($key) ? \Cache::get($key) : false;
	}
}
