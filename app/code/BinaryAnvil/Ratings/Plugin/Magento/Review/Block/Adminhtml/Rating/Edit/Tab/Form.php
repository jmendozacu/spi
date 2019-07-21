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

namespace BinaryAnvil\Ratings\Plugin\Magento\Review\Block\Adminhtml\Rating\Edit\Tab;

use BinaryAnvil\Ratings\Preference\Magento\Review\Model\Rating;
use Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form as OriginClass;
use Magento\Framework\Registry;
use Closure;

class Form
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Form constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get form HTML
     *
     * @param \Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form $subject
     * @param \Closure $proceed
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetFormHtml(OriginClass $subject, Closure $proceed)
    {
        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->addFieldset('additional_settings', ['legend' => __('Additional Settings')]);
            $fieldset->addField(
                Rating::IS_USED_IN_SUMMARY,
                'checkbox',
                [
                    'name' => Rating::IS_USED_IN_SUMMARY,
                    'label' => __('Use for summary rating'),
                    'id' => Rating::IS_USED_IN_SUMMARY,
                    'title' => __('Use for summary rating'),
                    'required' => false
                ]
            );
            $fieldset->addField(
                Rating::LABEL_MIN,
                'text',
                [
                    'name' => Rating::LABEL_MIN,
                    'label' => __('Label for Minimum'),
                    'id' => Rating::LABEL_MIN,
                    'title' => __('Label for Minimum'),
                    'required' => false
                ]
            );
            $fieldset->addField(
                Rating::LABEL_PERFECT,
                'text',
                [
                    'name' => Rating::LABEL_PERFECT,
                    'label' => __('Label for Perfect'),
                    'id' => Rating::LABEL_PERFECT,
                    'title' => __('Label for Perfect'),
                    'required' => false
                ]
            );
            $fieldset->addField(
                Rating::LABEL_MAX,
                'text',
                [
                    'name' => Rating::LABEL_MAX,
                    'label' => __('Label for Maximum'),
                    'id' => Rating::LABEL_MAX,
                    'title' => __('Label for Maximum'),
                    'required' => false,
                    'note' => __('Perfect is maximum, if it is empty')
                ]
            );

            $subject->setForm($form);

            /** @var \BinaryAnvil\Ratings\Preference\Magento\Review\Model\Rating $ratingData */
            if ($ratingData = $this->registry->registry('rating_data')) {
                $subject->getForm()->getElement(Rating::IS_USED_IN_SUMMARY)->setIsChecked(
                    $ratingData->isUsedInSummary()
                );
                $subject->getForm()->getElement(Rating::LABEL_MIN)->setValue(
                    $ratingData->getLabelMin()
                );
                $subject->getForm()->getElement(Rating::LABEL_PERFECT)->setValue(
                    $ratingData->getLabelPerfect()
                );
                $subject->getForm()->getElement(Rating::LABEL_MAX)->setValue(
                    $ratingData->getLabelMax()
                );
            }
        }

        return $proceed();
    }
}
