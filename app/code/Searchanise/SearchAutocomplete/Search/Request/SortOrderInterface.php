<?php

namespace Searchanise\SearchAutocomplete\Search\Request;

/**
 * Search sort order specification.
 */
interface SortOrderInterface
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    const TYPE_STANDARD = 'standardSortOrder';

    const DEFAULT_SORT_NAME     = 'relevance';
    const DEFAULT_SORT_FIELD     = 'title';
    const DEFAULT_SORT_DIRECTION = self::SORT_DESC;

    /**
     * Sort order name.
     *
     * @return string
     */
    public function getName();

    /**
     * Field used for sort.
     *
     * @return string
     */
    public function getField();

    /**
     * Sort order direction.
     *
     * @return string
     */
    public function getDirection();

    /**
     * Sort order type.
     *
     * @return string
     */
    public function getType();
}
