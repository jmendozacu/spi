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
 * @package     BinaryAnvil_Customer
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Customer\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use BinaryAnvil\Customer\Helper\Data as DataHelper;

class Occupation extends AbstractSource
{
    /**
     * @var \BinaryAnvil\Customer\Helper\Data
     */
    protected $dataHelper;

    /**
     * Occupation constructor.
     * @param \BinaryAnvil\Customer\Helper\Data $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = $this->dataHelper
            ->getUnserializedStoreConfig(DataHelper::XML_PATH_ENABLED_OCCUPATIONS);
        $value = 0;
        $this->_options = [['label' => 'Select Occupation...', 'value' => $value]];

        foreach ($options as $option) {
            $this->_options[] = ['label' => $option['occupation'], 'value' => ++ $value];
        }

        return $this->_options;
    }
}
