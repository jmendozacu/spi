<?php
namespace Aheadworks\OneStepCheckout\Model\Serialize;

/**
 * Class PhpSerialize
 * @package Aheadworks\OneStepCheckout\Model\Serialize
 */
class PhpSerialize implements SerializeInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize($data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string)
    {
        return unserialize($string);
    }
}
