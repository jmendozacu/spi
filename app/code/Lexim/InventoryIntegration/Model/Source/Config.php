<?php declare(strict_types=1);

namespace Lexim\InventoryIntegration\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_URL_INTEGRATION = 'lexim/inventory_integration/url';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getIntegrationUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_URL_INTEGRATION);
    }
}
