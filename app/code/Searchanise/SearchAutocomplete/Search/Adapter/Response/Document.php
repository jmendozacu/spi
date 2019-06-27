<?php

namespace Searchanise\SearchAutocomplete\Search\Adapter\Response;

/**
 * Search document
 */
class Document extends \Magento\Framework\Api\Search\Document
{
    /**
     * @var string
     */
    const SCORE_DOC_FIELD_NAME  = "_score";

    /**
     * @var string
     */
    const SOURCE_DOC_FIELD_NAME = "_source";

    /**
     * Return search document score.
     *
     * @return float
     */
    public function getScore()
    {
        return (float) $this->_get(self::SCORE_DOC_FIELD_NAME);
    }
    /**
     * Document source data.
     *
     * @return array
     */
    public function getSource()
    {
        return $this->_get(self::SOURCE_DOC_FIELD_NAME);
    }
}
