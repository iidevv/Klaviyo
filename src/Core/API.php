<?php

namespace Iidev\Klaviyo\Core;

use XLite\Core\Config;
use XLite\InjectLoggerTrait;
use Exception;

class API
{
    use InjectLoggerTrait;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getKlaviyoUrl()
    {
        return "https://a.klaviyo.com/api/";
    }

    protected function getPrivateKey()
    {
        return Config::getInstance()->Iidev->Klaviyo->private_key;
    }

    public function events($body)
    {

        $result = $this->doRequest('POST', 'events', json_encode($body));

        return (int) $result->code === 200;
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $data
     *
     * @return \PEAR2\HTTP\Request\Response
     * @throws \Exception
     */
    protected function doRequest($method, $path, $data = '', $headers = [])
    {
        $this->getLogger('Klaviyo')->debug(__FUNCTION__ . 'Request. Initial data', [
            $method,
            $path,
            $data
        ]);

        $url = $this->getKlaviyoUrl() . '/' . $path;

        $request = new \XLite\Core\HTTP\Request($url);

        $request->verb = $method;

        $request->setHeader('Authorization', sprintf('Klaviyo-API-Key %s', $this->getPrivateKey()));
        $request->setHeader('Revision', '2023-10-15');
        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');

        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $request->setHeader($key, $value);
            }
        }

        $request->body = $data;

        $this->getLogger('Klaviyo')->debug(__FUNCTION__ . 'Request', [
            $method,
            $url,
            $request->headers,
            $request->body,
        ]);

        $response = $request->sendRequest();

        $this->getLogger('Klaviyo')->debug(__FUNCTION__ . 'Response', [
            $method,
            $url,
            $response ? $response->headers : 'empty',
            $response ? $response->body : 'empty',
            $request->getErrorMessage(),
        ]);

        if (!$response || !in_array((int) $response->code, [200, 201, 202, 204], true)) {
            throw new Exception($request->getErrorMessage(), $response->code);
        }

        return $response;
    }
}
