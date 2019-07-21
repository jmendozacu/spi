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
 * @category BinaryAnvil
 * @package SizeChart
 * @copyright Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license http://www.binaryanvil.com/software/license
 */
namespace BinaryAnvil\SizeChart\Controller\Adminhtml\Category\Image;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Category\Image\Upload as OriginClass;
use BinaryAnvil\SizeChart\Helper\Image;

class Upload extends OriginClass
{
    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $attributeCode = Image::SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE;
            if (!$attributeCode) {
                throw new \Exception('attribute_code missing');
            }

            $this->imageUploader->setBasePath(Image::SIZE_CHART_CATEGORY_BASE_IMAGE_PATH);
            $this->imageUploader->setBaseTmpPath(Image::SIZE_CHART_CATEGORY_BASE_IMAGE_TMP_PATH);

            $result = $this->imageUploader->saveFileToTmpDir($attributeCode);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
