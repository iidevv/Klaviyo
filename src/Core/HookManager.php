<?php

namespace Iidev\Klaviyo\Core;

use XLite\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class HookManager extends \Iidev\StripeSubscriptions\Core\HookManager
{

    private function setProMembership(Profile $profile, string $status): void
    {
        parent::setProMembership($profile, $status);

        $tracking = new BackendTracking;

        $attributes = [
            "email" => $profile->getLogin()
        ];

        if ($status === 'Active') {
            $attributes['membership'] = 'pro member';

            $tracking->doCreateOrUpdateProfile($attributes);
            return;
        }

        if ($status === 'Inactive') {
            $attributes['membership'] = 'non-member';

            $tracking->doCreateOrUpdateProfile($attributes);
            return;
        }
    }
}