<?php

namespace Iidev\Klaviyo\View\Model\Profile;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    /**
     * Schema of the "E-mail & Password" section
     *
     * @var array
     */
    protected $mainSchema = [
        'login' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Email',
            self::SCHEMA_LABEL => 'E-mail',
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL => 'New password',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password_conf' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL => 'Confirm password',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'business_category' => [
            self::SCHEMA_CLASS => 'Iidev\Klaviyo\View\FormField\Select\BusinessType',
            self::SCHEMA_LABEL => 'Your business category',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'membership_id' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
            \XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES => [
                'readonly' => true,
                'disabled' => true,
            ],
            self::SCHEMA_LABEL => 'Membership',
            self::SCHEMA_REQUIRED => false,
        ],
        'pending_membership_id' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\Membership',
            self::SCHEMA_LABEL => 'Pending membership',
            self::SCHEMA_REQUIRED => false,
        ],
    ];
}
