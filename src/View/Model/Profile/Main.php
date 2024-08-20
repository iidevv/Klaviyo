<?php

namespace Iidev\Klaviyo\View\Model\Profile;

use XLite\Core\Request;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    public function __construct()
    {
        parent::__construct();
        if ($this->isRegisterMode()) {
            $this->mainSchema['business_category'] = [
                self::SCHEMA_CLASS => 'Iidev\Klaviyo\View\FormField\Select\BusinessType',
                self::SCHEMA_LABEL => 'Your business category',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_MODEL_ATTRIBUTES => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                ],
            ];
        }
    }
}
