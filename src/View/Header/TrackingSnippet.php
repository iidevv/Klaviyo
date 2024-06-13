<?php

namespace Iidev\Klaviyo\View\Header;

use XLite\View\AView;
use XLite\Core\Config;
use XCart\Extender\Mapping\ListChild;
use \XLite\Core\Session;
use \XLite\Core\Auth;

/**
 * @ListChild (list="head", zone="customer")
 */
class TrackingSnippet extends AView
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Iidev/Klaviyo/tracking_snippet.js';

        return $list;
    }
    public function getPublicKey()
    {
        return Config::getInstance()->Iidev->Klaviyo->public_key;
    }

    public function getLogin()
    {
        $profile = Auth::getInstance()->getProfile();

        return $profile ? $profile->getLogin() : null;
    }

    public function setUserIdentity()
    {
        if (!$this->getLogin())
            return;

        Session::getInstance()->identity = $this->getLogin();
    }

    public function isUserIdentified()
    {
        return Session::getInstance()->identity;
    }

    /**
     * @return array
     */
    protected function getTrackingData()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            $controller = \XLite::getController();

            switch (true) {
                case get_class($controller) === 'XLite\Controller\Customer\Product':
                    
                    $product = $controller->getProduct();

                    if ($product) {
                        $category = $product->getCategory();
                        $categoryName = $category->isPersistent()
                            ? [$this->getCategoryPathName($category)]
                            : ['Catalog'];

                        $result = [
                            'type' => 'product',
                            'item' => [
                                "ProductName" => $product->getName(),
                                "ProductID" => $product->getSku(),
                                "SKU" => $product->getSku(),
                                "Categories" => $categoryName,
                                "ImageURL" => $product->getImage()->getURL(),
                                "URL" => $product->getURL(),
                                "Brand" => $product->getBrandName(),
                                "Price" => $product->getNetPrice(),
                            ]
                        ];
                        if($product->getMarketPrice()) {
                            $result['item']['CompareAtPrice'] = $product->getMarketPrice();
                        }
                    }
                    break;

                case get_class($controller) === 'XLite\Controller\Customer\Cart':
                    $cart = $controller->getCart();
                    $itemsSku = [];
                    foreach ($cart->getItems() as $item) {
                        $itemsSku[] = $item->getProduct()->getSku();
                    }

                    $result = [
                        'type' => 'cart',
                        'ecomm_prodid' => $itemsSku,
                        'ecomm_totalvalue' => $cart->getTotal(),
                    ];
                    break;
            }

            return $result;
        });
    }


    /**
     * @param $category
     *
     * @return string
     */
    protected function getCategoryPathName(\XLite\Model\Category $category)
    {
        return $this->executeCachedRuntime(static function () use ($category) {
            $categoryPath = $category->getPath();

            if (count($categoryPath) > 5) {
                $categoryPath = array_merge(array_slice($categoryPath, 0, 4), end($categoryPath));
            }

            $categoryName = implode(
                ', ',
                array_map(
                    static function ($elem) {
                        return $elem->getName();
                    },
                    $categoryPath
                )
            );

            return $categoryName;
        }, ['getCategoryPathName', $category->getCategoryId()]);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/Klaviyo/header/tracking_snippet.twig';
    }
}
