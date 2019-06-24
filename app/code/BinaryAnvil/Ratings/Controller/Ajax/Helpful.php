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

namespace BinaryAnvil\Ratings\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use BinaryAnvil\Ratings\Api\ReviewHelpfulRepositoryInterface;
use BinaryAnvil\Ratings\Helper\Data as DataHelper;

class Helpful extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \BinaryAnvil\Ratings\Api\ReviewHelpfulRepositoryInterface
     */
    protected $reviewHelpfulRepository;

    /**
     * @var \BinaryAnvil\Ratings\Helper\Data
     */
    protected $dataHelper;

    /**
     * Helpful constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \BinaryAnvil\Ratings\Api\ReviewHelpfulRepositoryInterface $reviewHelpfulRepository
     * @param \BinaryAnvil\Ratings\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CustomerSession $customerSession,
        ReviewHelpfulRepositoryInterface $reviewHelpfulRepository,
        DataHelper $dataHelper
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
        $this->reviewHelpfulRepository = $reviewHelpfulRepository;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $customerId = $this->customerSession->getCustomer()->getId();

        $helpfulVotes = $this->customerSession->getData('helpful_votes');

        $reviewHelpful = $this->dataHelper
            ->getCustomerHelpfulByReviewId($params['review_id'], $customerId, $helpfulVotes);
        $reviewHelpful->setIsHelpful($params['is_helpful']);

        $this->reviewHelpfulRepository->save($reviewHelpful);

        if (!$customerId) {
            $helpfulVotes[$params['review_id']] = $reviewHelpful->getId();
            $this->customerSession->setHelpfulVotes($helpfulVotes);
        }

        return $this->jsonFactory->create()
            ->setData($this->dataHelper->getReviewHelpfulVotesCount($params['review_id']));
    }
}
