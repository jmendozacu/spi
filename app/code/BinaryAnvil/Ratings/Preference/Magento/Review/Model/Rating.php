<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     BinaryAnvil_Ratings
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

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
