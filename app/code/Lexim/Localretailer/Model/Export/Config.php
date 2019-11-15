<?php declare(strict_types=1);

namespace Lexim\Localretailer\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Config
{
    const XML_PATH = 'localretailer_export/general';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  false|string
     */
    public function getConfigData($field)
    {
        $path = self::XML_PATH . '/' . $field;
        return $this->scopeConfig->getValue($path);
    }

    /**
     * Retrieve config flag for store by field
     *
     * @param string $field
     * @return bool
     */
    public function getConfigFlag($field)
    {
        $path = self::XML_PATH . '/' . $field;
        return $this->scopeConfig->isSetFlag($path);
    }

    public function getFtpPassword()
    {
        $pass = $this->getConfigData('ftp_password');
        return $this->encryptor->decrypt($pass);
    }

    /**
     * Get Ftp config
     * @return array
     */
    public function getFtpInfo()
    {
        return [
            'host' => $this->getConfigData('ftp_host'),
            'port' => $this->getConfigData('ftp_port'),
            'user' => $this->getConfigData('ftp_user'),
            'password' => $this->getFtpPassword(),
            'ssl' => $this->getConfigFlag('ftp_ssl'),
            'passive' => true
        ];
    }
}
