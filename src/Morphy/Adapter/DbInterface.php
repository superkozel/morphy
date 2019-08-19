<?php


namespace Morphy\Adapter;


interface DbInterface
{
    /**
     * @param string $sql
     * @return array
     */
    public function queryRow($sql);

    /**
     * @param string $sql
     * @return array
     */
    public function queryAll($sql);

    /**
     * @param string $sql
     * @return mixed
     */
    public function execute($sql);
}