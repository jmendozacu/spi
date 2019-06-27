<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Response;

/**
 * Generate document from Searchanise hit response.
 */
class DocumentFactory
{
    const ENTITY_ID = 'product_id';

    /**
     * @var string
     */
    private $_instanceName;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Searchanise\SearchAutocomplete\Search\Adapter\Response\Document'
    ) {
        $this->_instanceName    = $instanceName;
        $this->_objectManager   = $objectManager;
    }

    /**
     * Create search dcument instance
     *
     * @param  array $rawDocument
     * @return Document
     */
    public function create($rawDocument)
    {
        /** @var \Magento\Framework\Search\DocumentField[] $fields */
        $rawDocument[Document::ID] = $rawDocument[self::ENTITY_ID];

        return $this->_objectManager->create(
            $this->_instanceName,
            [
            'data' => $rawDocument
            ]
        );
    }
}
