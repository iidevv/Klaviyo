<?php

namespace Iidev\Klaviyo\Controller\Customer;

use XLite\InjectLoggerTrait;
use XCart\Extender\Mapping\Extender;
use Iidev\Klaviyo\Core\API;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    use InjectLoggerTrait;
    protected function doActionRegister()
    {
        $result = parent::doActionRegister();

        if ($result && $this->getModelForm()) {
            $profile = $this->getModelForm()->getModelObject();

            $api = new API();
            $result = $api->createAndSubscribeProfile($profile->getLogin(), 'signup');
        }

        return $result;
    }
}
