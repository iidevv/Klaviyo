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

    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/Klaviyo/header/tracking_snippet.twig';
    }
}
