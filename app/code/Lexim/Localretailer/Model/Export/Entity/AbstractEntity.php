<?php declare(strict_types=1);

namespace Lexim\Localretailer\Model\Export\Entity;

abstract class AbstractEntity
{
    /**
     * Error counter.
     *
     * @var int
     */
    protected $errorsCount = 0;

    /**
     * Processed counter
     * @var int
     */
    protected $processedRowsCount = 0;

    abstract public function export();

    /**
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->errorsCount;
    }

    /**
     * Returns number of processed rows
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->processedRowsCount;
    }
}
