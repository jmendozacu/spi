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
 * @package     BinaryAnvil_InfinityTheme
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\InfinityTheme\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Cms\Block\Block;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface $layout
     */
    protected $layout;

    /**
     * @var array
     */
    protected $cmsBlocks = [];

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param $data
     */
    public function __construct(
        LayoutInterface $layout,
        $data = []
    ) {
        $this->layout = $layout;
        $this->constructBlock($data);
    }

    /**
     * @param array $blocks
     */
    public function constructBlock($blocks)
    {
        foreach ($blocks as $blockName) {
            $this->cmsBlocks[$blockName] = $this->layout->createBlock(Block::class)
                ->setBlockId($blockName)->toHtml();
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->cmsBlocks;
    }
}
