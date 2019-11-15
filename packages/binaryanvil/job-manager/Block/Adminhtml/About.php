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
 * @package     JobManager
 * @copyright   Copyright (c) 2016-present Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\JobManager\Block\Adminhtml;

use Magento\Backend\Block\Context;
use Magento\Framework\View\Helper\Js;
use Magento\Backend\Model\Auth\Session;
use BinaryAnvil\JobManager\Helper\Data;
use Magento\Config\Block\System\Config\Form\Fieldset;

class About extends Fieldset
{
    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    // @codingStandardsIgnoreStart
    /**
     * {@inheritdoc}
     */
    protected function _getHeaderCommentHtml($element)
    {
        $comment = $element->getComment() ? $element->getComment() : '';

        $html  = '<div style="padding:10px; border: 1px solid #ddd; margin-bottom: 10px;">';
        $html .= '<div class="messages">
                    <div class="message message-error error">
                        <div data-ui-id="messages-message-error">
                            Please do not make any changes to these settings, all changes must be made by Lexim team.
                         </div>
                     </div>
                 </div>';
        $html .= '<div class="messages">
                    <div class="message message-notice notice" style="margin-bottom: 0;">
                        <div data-ui-id="messages-message-notice">
                            <a href="' . $this->getJobGridUrl() . '">Click here to see current jobs.</a>
                        </div>
                    </div>
                </div>';

        if (!empty($comment)) {
            $html .= '<p style="margin-top: 15px;">' . $comment . '</p>';
        }

        $html .='</div>';

        return $html;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Retrieve jobs grid url
     *
     * @return string
     */
    protected function getJobGridUrl()
    {
        return $this->getUrl('binaryanvil_jobmanager/job');
    }
}
