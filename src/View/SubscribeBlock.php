<?php

namespace Iidev\Klaviyo\View;

use XCart\Extender\Mapping\Extender as Extender;
/**
 * @Extender\Mixin
 */
class SubscribeBlock extends \XC\NewsletterSubscriptions\View\SubscribeBlock
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/Klaviyo/form/subscribe.twig';
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return \XLite\Core\Config::getInstance()->Iidev->Klaviyo->footer_form;
    }
}
