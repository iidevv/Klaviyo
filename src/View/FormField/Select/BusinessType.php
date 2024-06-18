<?php

namespace Iidev\Klaviyo\View\FormField\Select;

use XLite\Core\Config;

class BusinessType extends \XLite\View\FormField\Select\Regular
{
    protected function getBusinessCategoriesArray()
    {
        return explode(", ", Config::getInstance()->Iidev->Klaviyo->business_categories);
    }
    protected function getDefaultOptions()
    {
        $categoriesArray = $this->getBusinessCategoriesArray();

        $options = [
            "" => static::t('Not selected')
        ];

        foreach ($categoriesArray as $category) {
            $options[$category] = $category;
        }

        return $options;
    }
}
