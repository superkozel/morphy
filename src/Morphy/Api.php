<?php


namespace Morphy;


use Curl\Curl;

class Api
{
    /**
     * @var Curl
     */
    protected $_curl;

    public function __construct()
    {
        $this->_curl = new Curl();
    }

    public function request($action, $data)
    {

    }

    public function declension()
    {

    }
}