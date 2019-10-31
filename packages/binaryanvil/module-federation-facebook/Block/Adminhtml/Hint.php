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
 * @package     BinaryAnvil_FederationFacebook
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace BinaryAnvil\FederationFacebook\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class Hint extends Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html  = parent::_getElementHtml($element);

        $html .= '<div style="margin: 15px 0 10px 0;">';
        $html .= '<div class="messages">
                    <div class="message message-notice notice">
                        <div data-ui-id="messages-message-notice">
                            ' . __('To display like button use following snippets.') . '
                         </div>
                     </div>
                 </div>';
        $html  .= '<strong>' . __('In template file:') . '</strong>';
        $html  .= '<div style="border:1px solid #cecece; background: #eee; margin:10px 0 15px; padding: 0 20px;"><pre>';
        $html  .= '<code>&lt;?php $block->getLayout()<br/>';
        $html  .= '    ->createBlock(\'BinaryAnvil\FederationFacebook\Block\LikeWidget\')<br/>';
        $html  .= '    ->setTemplate(\'BinaryAnvil_FederationFacebook::like-widget.phtml\')<br/>';
        $html  .= '    ->tohtml(); ?&gt;</code>';
        $html  .= '</pre></div>';

        $html  .= '<strong>' . __('In XML layout update:') . '</strong>';
        $html  .= '<div style="border:1px solid #cecece; background: #eee; margin:10px 0; padding: 0 20px;"><pre>';
        $html  .= '<code>&lt;block name="binaryanvil.federationfacebook"' . '<br/>';
        $html  .= '    type="BinaryAnvil\FederationFacebook\Block\LikeWidget"' . '<br/>';
        $html  .= '    template="BinaryAnvil_FederationFacebook::like-widget.phtml"/&gt;' . '<br/></code>';
        $html  .= '</pre></div>';

        $html  .= '<strong>' . __('On CMS page:') . '</strong>';
        $html  .= '<div style="border:1px solid #cecece; background: #eee; margin:10px 0; padding: 0 20px;"><pre>';
        $html  .= '<code>{{block name="binaryanvil.federationfacebook"' . '<br/>';
        $html  .= '    type="BinaryAnvil\FederationFacebook\Block\LikeWidget"' . '<br/>';
        $html  .= '    template="BinaryAnvil_FederationFacebook::like-widget.phtml"}};' . '<br/></code>';
        $html  .= '</pre></div>';

        $html .='</div>';

        return $html;
    }
}
