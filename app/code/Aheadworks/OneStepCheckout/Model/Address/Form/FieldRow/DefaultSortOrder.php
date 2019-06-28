<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow;

/**
 * Class DefaultSortOrder
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow
 */
class DefaultSortOrder
{
    /**
     * @var array
     */
    private $defaultFieldRowsSortOrder = [
        'name-field-row' => 0,
        'address-field-row' => 1,
        'city-field-row' => 2,
        'country-field-row' => 3,
        'region-field-row' => 3,
        'zip-field-row' => 3,
        'phone-field-row' => 4,
        'company-field-row' => 5,
        'fax-field-row' => 6
    ];

    /**
     * Get default row sort order
     *
     * @param string $rowId
     * @return int|null
     */
    public function getSortOrder($rowId)
    {
        return isset($this->defaultFieldRowsSortOrder[$rowId])
            ? $this->defaultFieldRowsSortOrder[$rowId]
            : null;
    }

    /**
     * Calculation of row sort order,
     * including non specified in $this->defaultFieldRowsSortOrder
     *
     * @param string $rowId
     * @param int|null $previous
     * @return int
     */
    public function calculateSortOrder($rowId, $previous = null)
    {
        $sortOrder = $this->getSortOrder($rowId);
        if (!$sortOrder) {
            return $previous !== null
                ? $previous + 1
                : max($this->defaultFieldRowsSortOrder) + 1;
        }
        return $sortOrder;
    }
}