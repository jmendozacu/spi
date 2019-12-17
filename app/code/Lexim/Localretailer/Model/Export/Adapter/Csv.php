<?php declare(strict_types=1);

namespace Lexim\Localretailer\Model\Export\Adapter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\Write;
use Magento\ImportExport\Model\Export\Adapter\AbstractAdapter;

class Csv extends AbstractAdapter
{
    /**
     * Field delimiter.
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Field enclosure character.
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * Source file handler.
     *
     * @var Write
     */
    protected $fileHandler;

    /**
     * {@inheritdoc }
     */
    public function __construct(Filesystem $filesystem, $destination = null)
    {
        register_shutdown_function([$this, 'destruct']);
        $uniqid = uniqid('importexport_');
        $destination = $uniqid . '.' . $this->getFileExtension();
        parent::__construct($filesystem, $destination, DirectoryList::TMP);
    }

    /**
     * Object destructor.
     *
     * @return void
     */
    public function destruct()
    {
        if (is_object($this->fileHandler)) {
            $this->fileHandler->close();
        }
    }

    /**
     * @return $this|AbstractAdapter
     * @throws FileSystemException
     */
    protected function _init()
    {
        $this->fileHandler = $this->_directoryHandle->openFile($this->_destination, 'w');
        return $this;
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/csv';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'csv';
    }

    /**
     * @param array $headerColumns
     * @return $this|AbstractAdapter
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function setHeaderCols(array $headerColumns)
    {
        if (null !== $this->_headerCols) {
            throw new LocalizedException(__('The header column names are already set.'));
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
            $this->fileHandler->writeCsv(array_keys($this->_headerCols), $this->delimiter, $this->enclosure);
        }
        return $this;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @return Csv
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function writeRow(array $rowData)
    {
        if (null === $this->_headerCols) {
            $this->setHeaderCols(array_keys($rowData));
        }
        $this->fileHandler->writeCsv(
            array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols)),
            $this->delimiter,
            $this->enclosure
        );
        return $this;
    }


    public function deleteFile()
    {
       return $this->_directoryHandle->delete($this->_destination);
    }
}
