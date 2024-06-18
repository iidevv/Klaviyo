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
        return "https://a.klaviyo.com/api";
    }

    protected function getPrivateKey()
    {
        return Config::getInstance()->Iidev->Klaviyo->secret_key;
    }

    protected function getInitialList()
    {
        return Config::getInstance()->Iidev->Klaviyo->initial_list;
    }

    protected function isSuccessfulCode($code)
    {
        return in_array((int) $code, [200, 201, 202, 204], true);
    }

    public function event($attributes)
    {
        $data = [
            "data" => [
                "type" => "event",
                "attributes" => $attributes
            ]
        ];

        $result = $this->doRequest('POST', 'events', $data);

        return $this->isSuccessfulCode($result->code);
    }

    public function createAndSubscribeProfile($login, $properties)
    {
        $result = $this->createProfile($login, $properties);
        if ($result) {
            return $this->subscribeProfile($login, $properties['$source']);
        } else {
            $this->getLogger('Klaviyo')->warning("createAndSubscribeProfile. Profile not created: ".$login);
            return 0;
        }
    }

    public function createProfile($login, $properties = [])
    {
        $data = [
            "data" => [
                "type" => "profile",
                "attributes" => [
                    "email" => $login,
                    "properties" => $properties
                ]
            ]
        ];

        $result = $this->doRequest('POST', 'profiles', $data);

        return $this->isSuccessfulCode($result->code);
    }

    public function subscribeProfile($login, $source)
    {
        if (!$this->getInitialList()) {
            $this->getLogger('Klaviyo')->error("Initial list not specified");
            return 0;
        }

        $data = [
            "data" => [
                "type" => "profile-subscription-bulk-create-job",
                "attributes" => [
                    "custom_source" => $source,
                    "profiles" => [
                        "data" => [
                            [
                                "type" => "profile",
                                "attributes" => [
                                    "email" => $login,
                                    "subscriptions" => [
                                        "email" => [
                                            "marketing" => [
                                                "consent" => "SUBSCRIBED"
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "relationships" => [
                    "list" => [
                        "data" => [
                            "type" => "list",
                            "id" => $this->getInitialList()
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->doRequest('POST', 'profile-subscription-bulk-create-jobs', $data);

        return $this->isSuccessfulCode($result->code);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     *
     * @return \PEAR2\HTTP\Request\Response
     * @throws \Exception
     */
    protected function doRequest($method, $path, $data = [], $headers = [])
    {
        $data = json_encode($data);

        $this->getLogger('Klaviyo')->debug(__FUNCTION__ . 'Request. Initial data', [
            $method,
            $path,
            $data
        ]);

        $url = $this->getKlaviyoUrl() . '/' . $path;

        $request = new \XLite\Core\HTTP\Request($url);

        $request->verb = $method;

        $key = $this->getPrivateKey();

        $request->setHeader('Authorization', sprintf('Klaviyo-API-Key %s', $key));
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

        if ($response->code === 409) {
            return $response;
        }

        if (!$response || !$this->isSuccessfulCode($response->code)) {
            $this->getLogger('Klaviyo')->error(__FUNCTION__ . 'Response error', [
                $response->body,
                $response->code
            ]);

            throw new Exception($response->body, $response->code);
        }

        return $response;
    }
}
