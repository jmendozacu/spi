<?php

namespace BinaryAnvil\SizeChart\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Helper\Context;

class Image extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \BinaryAnvil\SizeChart\Helper\Config $configHelper
     */
    protected $configHelper;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \BinaryAnvil\SizeChart\Helper\Config $config
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        Config $config
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->configHelper = $config;
        parent::__construct($context);
    }

    /**
     * @const size chart category attribute
     */
    const SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE = 'ba_size_chart';

    /**
     * @const size chart image paths
     */
    const SIZE_CHART_CATEGORY_BASE_IMAGE_PATH = 'catalog/category/sizechart';
    const SIZE_CHART_CATEGORY_BASE_IMAGE_TMP_PATH = 'catalog/category/sizechart/tmp';

    /**
     * Retrieve image URL
     *
     * @param string $image
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl($image)
    {
        $url = "";

        if ($image) {
            if (is_string($image)) {
                $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                    . self::SIZE_CHART_CATEGORY_BASE_IMAGE_TMP_PATH . DIRECTORY_SEPARATOR . $image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * Get size chart category image
     *
     * @param $category
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryImage($category)
    {
        if ($image = $category->getData(self::SIZE_CHART_CATEGORY_IMAGE_ATTRIBUTE)) {
            return $this->getImageUrl($image);
        } else {
            return false;
        }
    }


    /**
     * @param $product
     * @return bool|string
     * @throws LocalizedException
     */
    public function getSizeChart($product)
    {
        if (!$this->configHelper->isEnabled() || !$this->showInAttribute()) {
            return false;
        }

        $categoryIds = $product->getCategoryIds();
        try {
            $category = $this->categoryRepository->get($categoryIds[0]);
        } catch (\Exception $e) {
            return false;
        }
        return $this->getCategoryImage($category);
    }

    /**
     * Check where show size chart block
     *
     * @return string
     */
    public function showInAttribute()
    {
        return $this->configHelper->getShowInAttribute();
    }


    /**
     * @param $catId
     * @return bool|\Magento\Catalog\Api\Data\CategoryInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryById($catId)
    {
        if ($catId && intval($catId) > 0) {
            return $this->categoryRepository->get($catId);
        }
        return false;
    }
}
