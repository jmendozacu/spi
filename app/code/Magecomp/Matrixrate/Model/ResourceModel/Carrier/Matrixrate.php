<?php
namespace Magecomp\Matrixrate\Model\ResourceModel\Carrier;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magecomp\Matrixrate\Model\ResourceModel\Carrier\RateQuery;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Matrixrate\Model\Carrier\Matrixrate as ModelMatrixrate;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as ReginCollectionFactory;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Matrixrate extends AbstractDb
{
    protected $_importWebsiteId = 0;

    protected $_importErrors = [];

    protected $_importedRows = 0;

    protected $_importIso2Countries;

    protected $_importIso3Countries;

    protected $_importRegions;

    protected $_importConditionName;

    protected $_conditionFullNames = [];

    protected $_coreConfig;

    protected $_storeManager;

    protected $_shippingMatrixrate;

    protected $_countryCollectionFactory;

    protected $_regionCollectionFactory;

    protected $_filesystem;
	
	protected $_logger;
	
	private $_readFactory;
	
	private $rateQueryFactory;

    public function __construct(    		
        Context $context,
        ScopeConfigInterface $coreConfig,
        StoreManagerInterface $storeManager,
        ModelMatrixrate $shippingMatrixrate,
        CountryCollection $countryCollectionFactory,
        ReginCollectionFactory $regionCollectionFactory,
        Filesystem $filesystem,
		ReadFactory $readFactory,
		LoggerInterface $logger,
		RateQueryFactory $rateQueryFactory,
        $connectionName = null
    ) {
       
        $this->_coreConfig = $coreConfig;
        $this->_storeManager = $storeManager;
        $this->_shippingMatrixrate = $shippingMatrixrate;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_filesystem = $filesystem;
		$this->_logger = $logger;
		$this->_readFactory = $readFactory;
		$this->rateQueryFactory = $rateQueryFactory;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('shipping_matrixrate', 'id');
    }
    
    public function getNewRate(RateRequest $request,$zipRangeSet=0)
    {
    	$connection = $this->getConnection();
        $adapter = $connection->select()->from($this->getMainTable());
		
		$connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable());

        $rateQuery = $this->rateQueryFactory->create(['request' => $request]);

        $rateQuery->prepareSelect($select,$zipRangeSet);
        $bindings = $rateQuery->getBindings();

        $result = $connection->fetchAll($select, $bindings);
		
		return $result;
	}
    
    public function uploadAndImport(DataObject $object)
    {
        if (empty($_FILES['groups']['tmp_name']['matrixrate']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['matrixrate']['fields']['import']['value'];
        $website = $this->_storeManager->getWebsite($object->getScopeId());

        $this->_importWebsiteId = (int)$website->getId();
        $this->_importUniqueHash = [];
        $this->_importErrors = [];
        $this->_importedRows = 0;

        $tmpDirectory = ini_get('upload_tmp_dir')? $this->_readFactory->create(ini_get('upload_tmp_dir'))
            : $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($csvFile);
        $stream = $tmpDirectory->openFile($path);

        $headers = $stream->readCsv();
        if ($headers === false || count($headers) < 5) {
            $stream->close();
            throw new LocalizedException(__('Please correct Matrix Rates File Format.'));
        }

        if ($object->getData('groups/matrixrate/fields/condition_name/inherit') == '1') {
            $conditionName = (string)$this->_coreConfig->getValue('carriers/matrixrate/condition_name', 'default');
        } else {
            $conditionName = $object->getData('groups/matrixrate/fields/condition_name/value');
        }
        $this->_importConditionName = $conditionName;

        $adapter = $this->getConnection();
        $adapter->beginTransaction();

        try {
            $rowNumber = 1;
            $importData = [];

            $this->_loadDirectoryCountries();
            $this->_loadDirectoryRegions();

            // delete old data by website and condition name
            $condition = [
                'website_id = ?' => $this->_importWebsiteId,
                'condition_name = ?' => $this->_importConditionName,
            ];
            $adapter->delete($this->getMainTable(), $condition);

            while (false !== ($csvLine = $stream->readCsv())) {
                $rowNumber++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = [];
                }
            }
            $this->_saveImportData($importData);
            $stream->close();
        } catch (LocalizedException $e) {
            $adapter->rollback();
            $stream->close();
            throw new LocalizedException(__($e->getMessage()));
        } catch (\Exception $e) {
            $adapter->rollback();
            $stream->close();
            $this->_logger->critical($e);
            throw new LocalizedException(
                __('Something went wrong while importing matrix rates.')
            );
        }

        $adapter->commit();

        if ($this->_importErrors) {
            $error = __(
                'We couldn\'t import this file because of these errors: %1',
                implode(" \n", $this->_importErrors)
            );
            throw new LocalizedException($error);
        }

        return $this;
    }

    protected function _loadDirectoryCountries()
    {
        if ($this->_importIso2Countries !== null && $this->_importIso3Countries !== null) {
            return $this;
        }

        $this->_importIso2Countries = [];
        $this->_importIso3Countries = [];

        /** @var $collection \Magento\Directory\Model\ResourceModel\Country\Collection */
        $collection = $this->_countryCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->_importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    protected function _loadDirectoryRegions()
    {
        if ($this->_importRegions !== null) {
            return $this;
        }

        $this->_importRegions = [];

        /** @var $collection \Magento\Directory\Model\ResourceModel\Region\Collection */
        $collection = $this->_regionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }

    protected function _getConditionFullName($conditionName)
    {
        if (!isset($this->_conditionFullNames[$conditionName])) {
            $name = $this->_carrierMatrixrate->getCode('condition_name_short', $conditionName);
            $this->_conditionFullNames[$conditionName] = $name;
        }

        return $this->_conditionFullNames[$conditionName];
    }

    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 7) {
            $this->_importErrors[] = __('Please correct Matrix Rates format in Row #%1. Invalid Number of Rows', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        // validate country
        if (isset($this->_importIso2Countries[$row[0]])) {
            $countryId = $this->_importIso2Countries[$row[0]];
        } elseif (isset($this->_importIso3Countries[$row[0]])) {
            $countryId = $this->_importIso3Countries[$row[0]];
        } elseif ($row[0] == '*' || $row[0] == '') {
            $countryId = '0';
        } else {
            $this->_importErrors[] = __('Please correct Country "%1" in Row #%2.', $row[0], $rowNumber);
            return false;
        }

        // validate region
        if ($countryId != '0' && isset($this->_importRegions[$countryId][$row[1]])) {
            $regionId = $this->_importRegions[$countryId][$row[1]];
        } elseif ($row[1] == '*' || $row[1] == '') {
            $regionId = 0;
        } else {
            $this->_importErrors[] = __('Please correct Region/State "%1" in Row #%2.', $row[1], $rowNumber);
            return false;
        }

        // detect city
        if ($row[2] == '*' || $row[2] == '') {
            $city = '*';
        } else {
            $city = $row[2];
        }


        // detect zip code
        if ($row[3] == '*' || $row[3] == '') {
            $zipCode = '*';
        } else {
            $zipCode = $row[3];
        }

        //zip from


        if ($row[4] == '*' || $row[4] == '') {
            $zip_to = '';
        } else {
            $zip_to = $row[4];
        }

        // validate condition from value
        $valueFrom = $this->_parseDecimalValue($row[5]);
        if ($valueFrom === false) {
            $this->_importErrors[] = __(
                'Please correct %1 From "%2" in Row #%3.',
                $this->_getConditionFullName($this->_importConditionName),
                $row[5],
                $rowNumber
            );
            return false;
        }
        // validate conditionto to value
        $valueTo = $this->_parseDecimalValue($row[6]);
        if ($valueTo === false) {
            $this->_importErrors[] = __(
                'Please correct %1 To "%2" in Row #%3.',
                $this->_getConditionFullName($this->_importConditionName),
                $row[6],
                $rowNumber
            );
            return false;
        }


        // validate price
        $price = $this->_parseDecimalValue($row[7]);
        if ($price === false) {
            $this->_importErrors[] = __('Please correct Shipping Price "%1" in Row #%2.', $row[7], $rowNumber);
            return false;
        }

        // validate shipping method
        if ($row[8] == '*' || $row[8] == '') {
            $this->_importErrors[] = __('Please correct Shipping Method "%1" in Row #%2.', $row[8], $rowNumber);
            return false;
        } else {
            $shippingMethod = $row[8];
        }

        // protect from duplicate
        $hash = sprintf("%s-%d-%s-%s-%F-%F-%s", $countryId, $city, $regionId, $zipCode, $valueFrom, $valueTo,$shippingMethod );
        if (isset($this->_importUniqueHash[$hash])) {
            $this->_importErrors[] = __(
                'Duplicate Row #%1 (Country "%2", Region/State "%3", City "%4", Zip from "%5", Zip to "%6", From Value "%7", To Value "%8", and Shipping Method "%9")',
                $rowNumber,
                $row[0],
                $row[1],
                $city,
                $zipCode,
                $zip_to,
                $valueFrom,
                $valueTo,
                $shippingMethod
            );
            return false;
        }
        $this->_importUniqueHash[$hash] = true;

        return [
            $this->_importWebsiteId,    // website_id
            $countryId,                 // dest_country_id
            $regionId,                  // dest_region_id,
            $city,                      // city,
            $zipCode,                   // dest_zip
            $zip_to,                    //zip to
            $this->_importConditionName,// condition_name,
            $valueFrom,                 // condition_value From
            $valueTo,                   // condition_value To
            $price,                     // price
            $shippingMethod
        ];
    }

    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = [
                'website_id',
                'dest_country_id',
                'dest_region_id',
                'dest_city',
                'dest_zip',
                'dest_zip_to',
                'condition_name',
                'condition_from_value',
                'condition_to_value',
                'price',
                'delivery_type',
            ];
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }

    protected function _parseDecimalValue($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        $value = (double)sprintf('%.4F', $value);
        if ($value < 0.0000) {
            return false;
        }
        return $value;
    }
}
