<?php
namespace Aheadworks\OneStepCheckout\Model\Serialize;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 * @package Aheadworks\OneStepCheckout\Model\Serialize
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create serializer instance
     *
     * @return SerializeInterface
     */
    public function create()
    {
        return $this->objectManager->create(SerializeInterface::class);
    }
}
