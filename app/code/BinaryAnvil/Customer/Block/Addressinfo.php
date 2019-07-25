<?php

namespace BinaryAnvil\Customer\Block;

class Addressinfo extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->currentCustomerAddress = $currentCustomerAddress;
        $this->_addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;        
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Array for Shipping Address
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getDefaultShippingAddress()
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default shipping address.');
        }
        return $address;
    }

    /**
     * Array for Billing Address
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getDefaultBillingAddress()
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default billing address.');
        }
        return $address;
    }
    /* Html Format */
    public function getDefaultShippingAddressHtml($address) {
        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default Shipping address.');
        }
    }
    /* Html Format */
    public function getDefaultBillingAddressHtml($address) {
        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default billing address.');
        }
    }


    /**
     * Render an address as HTML and return the result
     *
     * @param AddressInterface $address
     * @return string
     */
    protected function _getAddressHtml($address)
    {
        /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($this->addressMapper->toFlatArray($address));
    }

    /*
     * return bool
     */
    public function getLogin() {
        return $this->httpContext->isLoggedIn();
    }

}

?>