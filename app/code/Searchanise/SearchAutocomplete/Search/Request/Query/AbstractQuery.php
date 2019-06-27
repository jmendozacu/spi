<?php

namespace Searchanise\SearchAutocomplete\Search\Request\Query;

/**
 * Abstact query implementation
 */
abstract class AbstractQuery
{
    /**
     * List of the exclude attributes from convertation its option ids to names
     *
     * @var array
     */
    protected $excludeAttributes = [
        'visibility',
        'is_in_stock',
    ];

    /**
     * Returns Object manager
     *
     * @return \Magento\Framework\App\ObjectManager
     */
    protected function getObjectManager()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Returns option text by option id
     *
     * @param string $attribute Attribute name
     * @param number $value     Option id
     */
    protected function getAttributeOptionText($attribute, $value)
    {
        $productResource = $this->getObjectManager()->get('Magento\Catalog\Model\ResourceModel\ProductFactory')->create();
        $attr = $productResource->getAttribute($attribute);

        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionText($value);
        }

        return $value;
    }

    /**
     * Convert ids field value to name
     *
     * @param  string $field Attribute name
     * @param  mixed  $value Option id value
     * @return string|array
     */
    protected function _prepareSourceValue($field, $value)
    {
        if (empty($field) || in_array($field, $this->excludeAttributes)) {
            return $value;
        }

        $value = !is_array($value) ? [$value] : $value;

        foreach ($value as $k => $v) {
            $value[$k] = $this->getAttributeOptionText($field, $v);
        }

        return count($value) > 1 ? $value : current($value);
    }
}
