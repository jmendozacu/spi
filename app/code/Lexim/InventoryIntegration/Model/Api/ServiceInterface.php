<?php

namespace Lexim\InventoryIntegration\Model\Api;

interface ServiceInterface
{
    /**
     * post
     */
    const POST = 'post';

    /**
     * get
     */
    const GET = 'get';

    /**
     * put
     */
    const PUT = 'put';

    /**
     * patch
     */
    const PATCH = 'patch';

    /**
     * delete
     */
    const DELETE = 'delete';

    /**
     * Make API call
     *
     * @param string $url
     * @param string $body
     * @param string $method HTTP request type
     * @return array Response body from API call
     */
    public function makeRequest($url, $body = '', $method = self::POST);

    /**
     * Connect to API
     *
     * @param string $username
     * @param string $password
     * @param string $connectUrl
     * @return bool Whether connect succeeded or not
     */
    public function connect($username, $password, $connectUrl = null);

    /**
     * @param string $product
     * @param string $version
     * @param string $mageInfo
     * @return mixed
     */
    public function setUserAgent($product, $version, $mageInfo);

    /**
     * @param string      $header
     * @param string|null $value
     * @return mixed
     */
    public function setHeader($header, $value = null);
}
