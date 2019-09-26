<?php

namespace BinaryAnvil\Ratings\Preference\Magento\Review\Model;

use Magento\Review\Model\Rating as OriginalClass;

class Rating extends OriginalClass
{
    const IS_USED_IN_SUMMARY = 'is_used_in_summary';
    const LABEL_MIN = 'label_min';
    const LABEL_PERFECT = 'label_perfect';
    const LABEL_MAX = 'label_max';

    /**
     * @return bool
     */
    public function isUsedInSummary()
    {
        return $this->getData(self::IS_USED_IN_SUMMARY);
    }

    /**
     * @param bool $isUsedInSummary
     * @return $this
     */
    public function setIsUsedInSummary($isUsedInSummary)
    {
        return $this->setData(self::IS_USED_IN_SUMMARY, $isUsedInSummary);
    }

    /**
     * @return string|null
     */
    public function getLabelMin()
    {
        return $this->getData(self::LABEL_MIN);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelMin($label)
    {
        return $this->setData(self::LABEL_MIN, $label);
    }

    /**
     * @return string|null
     */
    public function getLabelPerfect()
    {
        return $this->getData(self::LABEL_PERFECT);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelPerfect($label)
    {
        return $this->setData(self::LABEL_PERFECT, $label);
    }

    /**
     * @return string|null
     */
    public function getLabelMax()
    {
        return $this->getData(self::LABEL_MAX);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabelMax($label)
    {
        return $this->setData(self::LABEL_MAX, $label);
    }


}
