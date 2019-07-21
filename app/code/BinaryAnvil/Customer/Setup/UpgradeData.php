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

/**
 * @codingStandardsIgnoreFile
 */

namespace BinaryAnvil\Customer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use BinaryAnvil\Customer\Model\Config\Source\AgeRange as AgeRangeSource;
use BinaryAnvil\Customer\Model\Config\Source\Occupation as OccupationSource;
use BinaryAnvil\Customer\Helper\Data as DataHelper;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\CustomerSetup;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * UpgradeData constructor.
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(EavConfig $eavConfig, CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $customerAttributesData = [
                'used_in_forms' => ['adminhtml_customer', 'customer_account_edit'],
                'is_used_for_customer_segment' => true,
                'is_system' => 0,
                'is_user_defined' => 1,
                'is_visible' => 1,
            ];

            $this->addNicknameAttributeToCustomer($customerSetup, $customerAttributesData);
            $this->addAgeRangeAttributeToCustomer($customerSetup, $customerAttributesData);
            $this->addOccupationAttributeToCustomer($customerSetup, $customerAttributesData);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @param array $customerAttributesData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addNicknameAttributeToCustomer(CustomerSetup $customerSetup, $customerAttributesData)
    {
        $customerSetup->removeAttribute(Customer::ENTITY, DataHelper::CUSTOMER_NICKNAME_ATTRIBUTE_CODE)
            ->addAttribute(
                Customer::ENTITY,
                DataHelper::CUSTOMER_NICKNAME_ATTRIBUTE_CODE,
                [
                    'type' => 'varchar',
                    'label' => __('Nickname'),
                    'input' => 'text',
                    'required' => false,
                    'system' => false,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'position' => 1,
                    'visible' => true
                ]
            );

        $attribute = $this->eavConfig
            ->getAttribute(Customer::ENTITY, DataHelper::CUSTOMER_NICKNAME_ATTRIBUTE_CODE);
        $attribute->addData($customerAttributesData);
        $attribute->save();
    }

    /**
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @param array $customerAttributesData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addAgeRangeAttributeToCustomer(CustomerSetup $customerSetup, $customerAttributesData)
    {
        $customerSetup->removeAttribute(Customer::ENTITY, DataHelper::CUSTOMER_AGE_RANGE_ATTRIBUTE_CODE)
            ->addAttribute(
                Customer::ENTITY,
                DataHelper::CUSTOMER_AGE_RANGE_ATTRIBUTE_CODE,
                [
                    'type' => 'int',
                    'label' => __('Age Range'),
                    'input' => 'select',
                    'source' => AgeRangeSource::class,
                    'required' => false,
                    'system' => false,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'default' => 0,
                    'position' => 200,
                    'visible' => true
                ]
            );

        $attribute = $this->eavConfig
            ->getAttribute(Customer::ENTITY, DataHelper::CUSTOMER_AGE_RANGE_ATTRIBUTE_CODE);
        $attribute->addData($customerAttributesData);
        $attribute->save();
    }

    /**
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @param array $customerAttributesData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addOccupationAttributeToCustomer(CustomerSetup $customerSetup, $customerAttributesData)
    {
        $customerSetup->removeAttribute(Customer::ENTITY, DataHelper::CUSTOMER_OCCUPATION_ATTRIBUTE_CODE)
            ->addAttribute(
                Customer::ENTITY,
                DataHelper::CUSTOMER_OCCUPATION_ATTRIBUTE_CODE,
                [
                    'type' => 'int',
                    'label' => __('Occupation'),
                    'input' => 'select',
                    'source' => OccupationSource::class,
                    'required' => false,
                    'system' => false,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'default' => 0,
                    'position' => 201,
                    'visible' => true
                ]
            );

        $attribute = $this->eavConfig
            ->getAttribute(Customer::ENTITY, DataHelper::CUSTOMER_OCCUPATION_ATTRIBUTE_CODE);
        $attribute->addData($customerAttributesData);
        $attribute->save();
    }
}
