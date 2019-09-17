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

namespace BinaryAnvil\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const CUSTOMER_ENTITY_TYPE = 'customer';
    const CUSTOMER_GENDER_ATTRIBUTE_CODE = 'gender';
    const CUSTOMER_AGE_RANGE_ATTRIBUTE_CODE = 'age_range';
    const CUSTOMER_OCCUPATION_ATTRIBUTE_CODE = 'occupation';
    const CUSTOMER_NICKNAME_ATTRIBUTE_CODE = 'nickname';

    const CUSTOMER_TOP_SIZE_ATTRIBUTE_CODE = 'top_size';
    const CUSTOMER_BOTTOM_SIZE_ATTRIBUTE_CODE = 'bottom_size';
    const CUSTOMER_SHOE_SIZE_ATTRIBUTE_CODE = 'shoe_size';
    const CUSTOMER_UNDER_SCRUB_SIZE_ATTRIBUTE_CODE = 'underscrub_size';
    const CUSTOMER_BOTTOM_LENGTH_ATTRIBUTE_CODE = 'bottom_length';
    const CUSTOMER_COLOR_PREFERENCE_ATTRIBUTE_CODE = 'color_preference';
    const CUSTOMER_SCRUB_HABITS_ATTRIBUTE_CODE = 'scrub_habits';
    const CUSTOMER_JACKET_SIZE_ATTRIBUTE_CODE = 'jacket_size';
    const CUSTOMER_COLOR_CODE_WORKSPACE_ATTRIBUTE_CODE = 'color_coded_workspace';
    const CUSTOMER_PREFER_WAY_OF_SHOP_ATTRIBUTE_CODE = 'prefer_way_of_shop';
    const CUSTOMER_DAISY_AWARD_WINNER_ATTRIBUTE_CODE = 'daisy_award_winner';
    const CUSTOMER_PLACE_OF_WORK_ATTRIBUTE_CODE = 'place_of_work';

    const XML_PATH_ENABLED_AGE_RANGES = 'customer/account_information/enabled_age_ranges';
    const XML_PATH_ENABLED_OCCUPATIONS = 'customer/account_information/enabled_occupations';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        EavConfig $eavConfig,
        Json $serializer,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->eavConfig = $eavConfig;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * @param string $attributeCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerOptions(string $attributeCode)
    {
        $attribute = $this->eavConfig
            ->getAttribute(self::CUSTOMER_ENTITY_TYPE, $attributeCode);
        $options = $attribute->getSource()->getAllOptions();

        return $options;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGenderOptions()
    {
        return $this->getCustomerOptions(self::CUSTOMER_GENDER_ATTRIBUTE_CODE);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerAgeRangeOptions()
    {
        return $this->getCustomerOptions(self::CUSTOMER_AGE_RANGE_ATTRIBUTE_CODE);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerOccupationOptions()
    {
        return $this->getCustomerOptions(self::CUSTOMER_OCCUPATION_ATTRIBUTE_CODE);
    }

    /**
     * @param string $customerAttributeCode
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerOptionsByAttributeCode($customerAttributeCode = "")
    {
        if (!$customerAttributeCode || $customerAttributeCode === "") {
            return false;
        }

        return $this->getCustomerOptions($customerAttributeCode);
    }

    /**
     * @param string $configPath
     * @return array
     */
    public function getUnserializedStoreConfig($configPath)
    {
        $config = $this->scopeConfig->getValue($configPath, 'stores');

        if ($config !== null) {
            return $this->serializer->unserialize($config);
        }

        return [];
    }
}
