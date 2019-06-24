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

namespace BinaryAnvil\Ratings\Preference\Magento\Review\Controller\Adminhtml\Rating;

use Magento\Review\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Save extends RatingController
{
    /**
     * Save rating
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->initEnityId();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Magento\Review\Model\Rating $ratingModel */
                $ratingModel = $this->_objectManager->create(\Magento\Review\Model\Rating::class);
                $stores = $this->getRequest()->getParam('stores');
                $position = (int)$this->getRequest()->getParam('position');
                $stores[] = 0;
                $isActive = (bool)$this->getRequest()->getParam('is_active');

                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                    ->setRatingCodes($this->getRequest()->getParam('rating_codes'))
                    ->setStores($stores)
                    ->setPosition($position)
                    ->setId($this->getRequest()->getParam('id'))
                    ->setIsActive($isActive)
                    ->setEntityId($this->coreRegistry->registry('entityId'))
                    // I added It. Start
                    ->setIsUsedInSummary($this->getRequest()->getParam('is_used_in_summary') !== null)
                    ->setLabelMin($this->getRequest()->getParam('label_min'))
                    ->setLabelPerfect($this->getRequest()->getParam('label_perfect'))
                    ->setLabelMax($this->getRequest()->getParam('label_max'))
                    // I added It. End
                    ->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = $this->_objectManager->create(\Magento\Review\Model\Rating\Option::class);
                        if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setRatingId($ratingModel->getId())
                            ->setPosition($i)
                            ->save();
                        $i++;
                    }
                }

                $this->messageManager->addSuccess(__('You saved the rating.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setRatingData(false);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)
                    ->setRatingData($this->getRequest()->getPostValue());
                $resultRedirect->setPath('review/rating/edit', ['id' => $this->getRequest()->getParam('id')]);
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('review/rating/');
        return $resultRedirect;
    }
}
