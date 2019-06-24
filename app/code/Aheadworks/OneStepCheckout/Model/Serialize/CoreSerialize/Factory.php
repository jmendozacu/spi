<?php
namespace Aheadworks\OneStepCheckout\Model\Serialize\CoreSerialize;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 * @package Aheadworks\OneStepCheckout\Model\Serialize\CoreSerialize
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
     * @return \Magento\Framework\Serialize\SerializerInterface
     */
    public function create()
    {
        return $this->objectManager->create(\Magento\Framework\Serialize\SerializerInterface::class);
    }
}
