<?php

namespace Searchanise\SearchAutocomplete\Helper;

use Searchanise\SearchAutocomplete\Model\Configuration;
use \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as productAttributeCollectionFactory;

/**
 * Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PARENT_PRIVATE_KEY = 'parent_private_key';

    const DISABLE_VAR_NAME = 'disabled_module_searchanise';
    const DISABLE_KEY      = 'Y';

    const DEBUG_VAR_NAME = 'debug_module_searchanise';
    const DEBUG_KEY      = 'Y';

    const TEXT_FIND          = 'TEXT_FIND';
    const TEXT_ADVANCED_FIND = 'TEXT_ADVANCED_FIND';

    private $disableText;
    private $debugText;

    /**
     * @var array
     */
    private static $searchaniseTypes = [
        self::TEXT_FIND,
        self::TEXT_ADVANCED_FIND,
    ];

    /**
     * @var \Searchanise\SearchAutocomplete\Model\Request
     */
    private $searchaniseRequest = null;

    /**
     * @var string
     */
    private $searchaniseCurentType = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Searchanise\SearchAutocomplete\Model\Configuration
     */
    private $configuration;

    /**
     * @var \Searchanise\SearchAutocomplete\Model\RequestFactory
     */
    private $requestFactory;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $catalogSearchHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $catalogProductAttributeCollectionFactory;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    private $pager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Searchanise\SearchAutocomplete\Model\Configuration $configuration,
        \Searchanise\SearchAutocomplete\Model\RequestFactory $requestFactory,
        \Magento\CatalogSearch\Helper\Data $catalogSearchHelper,
        productAttributeCollectionFactory $catalogProductAttributeCollectionFactory,
        \Magento\Theme\Block\Html\Pager $pager
    ) {
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
        $this->requestFactory = $requestFactory;
        $this->catalogSearchHelper = $catalogSearchHelper;
        $this->catalogProductAttributeCollectionFactory = $catalogProductAttributeCollectionFactory;
        $this->pager = $pager;

        parent::__construct($context);
    }

    /**
     * Init request
     *
     * @return \Searchanise\SearchAutocomplete\Helper\Data
     */
    public function initSearchaniseRequest()
    {
        $this->searchaniseRequest = $this->requestFactory->create();

        return $this;
    }

    /**
     * Returns searchanise request
     *
     * @return \Searchanise\SearchAutocomplete\Model\Request
     */
    public function getSearchaniseRequest()
    {
        return $this->searchaniseRequest;
    }

    /**
     * Set current request
     *
     * @param \Searchanise\SearchAutocomplete\Model\Request $request
     */
    public function setSearchaniseRequest(\Searchanise\SearchAutocomplete\Model\Request $request)
    {
        $this->searchaniseRequest = $request;
    }

    /**
     * Set current type
     *
     * @param string $type
     */
    public function setSearchaniseCurentType($type = null)
    {
        $this->searchaniseCurentType = $type;
    }

    /**
     * Returns current type
     *
     * @return string
     */
    public function getSearchaniseCurentType()
    {
        return $this->searchaniseCurentType;
    }

    /**
     * Get disable text
     *
     * @return boolean
     */
    public function getDisableText()
    {
        if (!isset($this->disableText)) {
            $this->disableText = $this->_getRequest()->getParam(self::DISABLE_VAR_NAME);
        }

        return $this->disableText;
    }

    /**
     *  Checks if the text is disabled
     *
     * @return boolean
     */
    public function checkEnabled()
    {
        return ($this->getDisableText() != self::DISABLE_KEY);
    }

    /**
     * Get results from path
     *
     * @param  number $store_id Store identifier
     * @return string
     */
    public function getResultsFormPath($store_id = null)
    {
        $store = $this->storeManager->getStore($store_id);

        return $store->getUrl('', ['_secure' => $store->isCurrentlySecure()]) . 'searchanise/result';
    }

    /**
     * Check debug
     *
     * @param  boolean $checkPrivateKey
     * @return boolean
     */
    public function checkDebug($checkPrivateKey = false)
    {
        $checkDebug = ($this->getDebugText() == self::DEBUG_KEY) ? true : false;

        if ($checkDebug && $checkPrivateKey) {
            $checkDebug = $checkDebug && $this->checkPrivateKey();
        }

        return $checkDebug;
    }

    /**
     * Get debug text
     *
     * @return mixed
     */
    public function getDebugText()
    {
        if (!isset($this->debugText)) {
            $this->debugText = $this->_getRequest()->getParam(self::DEBUG_VAR_NAME);
        }

        return $this->debugText;
    }

    /**
     * checks if the private key exists
     *
     * @return boolean
     */
    public function checkPrivateKey()
    {
        static $check;

        if (!isset($check)) {
            $parentPrivateKey = $this->_getRequest()->getParam(self::PARENT_PRIVATE_KEY);

            if ((empty($parentPrivateKey))
                || ($this->configuration->getValue(Configuration::XML_PATH_PARENT_PRIVATE_KEY) != $parentPrivateKey)
            ) {
                $check = false;
            } else {
                $check = true;
            }
        }

        return $check;
    }

    /**
     * Main execute funtion
     *
     * @return \Searchanise\SearchAutocomplete\Model\Request
     */
    public function search(array $searchRequest)
    {
        $request = $this
            ->initSearchaniseRequest()
            ->getSearchaniseRequest()
            ->setStore($this->storeManager->getStore())
            ->setSearchaniseCurentType($searchRequest['type'])
            ->setSearchParams($searchRequest['request'])
            ->sendSearchRequest();

        $this->setSearchaniseRequest($request);

        return $request;
    }
}
