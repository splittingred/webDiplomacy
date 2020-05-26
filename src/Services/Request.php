<?php

namespace Diplomacy\Services;

/**
 * @package Diplomacy\Services
 */
class Request
{
    const TYPE_REQUEST = 'request';
    const TYPE_POST = 'post';
    const TYPE_GET = 'get';

    /** @var array */
    protected $requestVars;
    /** @var array */
    protected $postVars;
    /** @var array */
    protected $getVars;

    public function __construct()
    {
        $this->requestVars = $_REQUEST;
        $this->postVars = $_POST;
        $this->getVars = $_GET;
    }

    /**
     * @param string $k
     * @param null $default
     * @param string $type
     * @return mixed|null
     */
    public function get(string $k, $default = null, string $type = self::TYPE_REQUEST)
    {
        $vars = $this->getParameters($type);
        return array_key_exists($k, $vars) ? $vars[$k] : $default;
    }

    /**
     * @param string $k
     * @return bool
     */
    public function exists(string $k, string $type = self::TYPE_REQUEST) : bool
    {
        $vars = $this->getParameters($type);
        return array_key_exists($k, $vars);
    }

    /**
     * @param string $k
     * @param string $type
     * @return bool
     */
    public function isEmpty(string $k, string $type = self::TYPE_REQUEST) : bool
    {
        $vars = $this->getParameters($type);
        return empty($vars[$k]);
    }

    /**
     * @param array $parameters
     * @param string $type
     */
    public function setParameters(array $parameters = [], string $type = self::TYPE_REQUEST) : void
    {
        if ($type == self::TYPE_REQUEST) {
            $this->requestVars = array_merge($this->requestVars, $parameters);
        } elseif ($type == self::TYPE_GET) {
            $this->requestVars = array_merge($this->getVars, $parameters);
        } elseif ($type == self::TYPE_POST) {
            $this->postVars = array_merge($this->postVars, $parameters);
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function getParameters($type = self::TYPE_REQUEST)
    {
        $vs = $this->requestVars;
        switch (strtoupper($type)) {
            case self::TYPE_POST: $vs = $this->postVars; break;
            case self::TYPE_GET: $vs = $this->getVars; break;
        }
        return $vs;
    }
}