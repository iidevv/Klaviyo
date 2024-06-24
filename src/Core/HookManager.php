<?php

namespace Iidev\Klaviyo\Core;

use XLite\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class HookManager extends \Iidev\StripeSubscriptions\Core\HookManager
{


    public function setProMembership(Profile $profile, string $status): void
    {
        $tracking = new BackendTracking;

        $attributes = [
            "email" => $profile->getLogin()
        ];

        if ($status === 'Active') {
            $attributes['properties']['membership'] = 'pro member';

            $tracking->doCreateOrUpdateProfile($attributes);
        }

        if ($status === 'Inactive') {
            $attributes['properties']['membership'] = 'non-member';

            $tracking->doCreateOrUpdateProfile($attributes);
        }
        
        parent::setProMembership($profile, $status);
    }
}