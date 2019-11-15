<?php declare(strict_types=1);

namespace Lexim\Localretailer\Model\Export;

use Exception;
use Lexim\Localretailer\Model\Export\Entity\AbstractEntity;
use Lexim\Localretailer\Model\Export\Adapter\Csv;
use Magento\Framework\App\ObjectManager;
use Lexim\Localretailer\Model\ResourceModel\LocalRetailerstore;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Sales\Model\Order\Invoice\Item;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

class Invoice extends AbstractEntity
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Ftp
     */
    private $ftpAdapter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StdlibDateTime
     */
    protected $dateTime;

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var LocalRetailerstore
     */
    private $localRetailerstoreResource;

    /**
     * @var LoggerInterface $log
     */
    private $logger;

    private $fileDate = null;

    private $countryInformationAcquirer;

    protected $headerColumns = [
        'InvoiceID',
        'CreateStamp',
        'OrderID',
        'WebOrderNumber',
        'ExternalOrderNumber',
        'PaymentMethod',
        'EmployeeNumber',
        'Source',
        'CouponCode',
        'Division',
        'ShipStateorProvince',
        'ShipCountry',
        'Alt2',
        'UPC',
        'Quantity',
        'MerchTotal',
        'Discount',
        'MerchNetDiscount',
        'Shipping',
        'Tax',
        'StoreId',
        'AutoSelection'
    ];

    /**
     * Invoice constructor.
     * @param StdlibDateTime $dateTime
     * @param Ftp $ftpAdapter
     * @param Config $config
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param LocalRetailerstore $localRetailerstoreResource
     * @param ProductFactory $productFactory
     */
    public function __construct(
        StdlibDateTime $dateTime,
        Ftp $ftpAdapter,
        Config $config,
        LoggerInterface $logger,
        CollectionFactory  $collectionFactory,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        LocalRetailerstore $localRetailerstoreResource,
        ProductFactory $productFactory
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
        $this->ftpAdapter = $ftpAdapter;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->localRetailerstoreResource = $localRetailerstoreResource;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->productFactory = $productFactory;
    }

    public function export($previousFlag = false)
    {
        if ($this->config->getConfigFlag('is_enabled')) {
            try {
                //Filter invoice
                if (count($invoiceData = $this->invoiceData($previousFlag))) {
                    $this->csv = ObjectManager::getInstance()->get(Csv::class);
                    $this->csv->setHeaderCols($this->headerColumns);
                    foreach ($invoiceData as $data) {
                        $this->csv->writeRow($data);
                    }
                    //Upload to FTP
                    $ftp = $this->config->getFtpInfo();
                    $this->ftpAdapter->open($ftp);
                    $dirName = trim($this->config->getConfigData('ftp_path'), '\\/');
                    $folderPath = '/' . $dirName;
                    if (false === $this->ftpAdapter->cd($folderPath)) {
                        if (false === $this->ftpAdapter->mkdir($dirName, 775)) {
                            throw new \Exception('Cannot create folder.');
                        }
                    }
                    $this->ftpAdapter->cd($folderPath);
                    $filePath = 'Invoice-' . str_replace('/', '_', $this->fileDate) . '.csv';
                    $this->ftpAdapter->write($filePath, $this->csv->getContents());
                    $this->ftpAdapter->close();
                    $this->csv->deleteFile();
                } else {
                    $this->logger->info('There is no data to export.');
                };
            } catch (Exception $exception) {
                $this->csv->deleteFile();
                $this->logger->error('Export invoice has error: ' . $exception->getMessage());
            }
        }
    }

    protected function invoiceData($previousFlag)
    {
        $itemsData = [];
        $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        if (true === $previousFlag) {
            //This case is for generating data from cron job.
            $toDate = $this->dateTime->formatDate($dateTime->getTimestamp(), false);
            // create one day interval
            $interval = new \DateInterval('P1D');
            $dateTime->sub($interval);
            $fromDate  = $this->dateTime->formatDate($dateTime->getTimestamp(), false);
            $condition1 = ['date' => true, 'gteq' => $fromDate];
            $condition2 = ['date' => true, 'lt' => $toDate];

        } else {
            //This case is for generating data from command line case.
            $fromDate  = $this->dateTime->formatDate($dateTime->getTimestamp(), false);
            $toDate = $this->dateTime->formatDate($dateTime->getTimestamp());
            $condition1 = ['date' => true, 'gteq' => $fromDate];
            $condition2 = ['date' => true, 'lteq' => $toDate];
        }
        $this->fileDate = $fromDate;
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToFilter('created_at', $condition1);
        $collection->addAttributeToFilter('created_at', $condition2);
        foreach ($collection->getItems() as $invoice) {
            /** @var Order\Invoice $invoice */
            $order = $invoice->getOrder();
            $address = $order->getShippingAddress();
            if ($address) {
                $address = $order->getBillingAddress();
            }
            $customerId = $order->getCustomerId();
            $localRetailerstore = $this->localRetailerstoreResource->getStoreInfoByCustomerId($customerId);
            $autoSelection = 'FALSE';
            $storeId = '';
            if ($localRetailerstore) {
                $autoSelection = 'TRUE';
                $storeId = $localRetailerstore['retailer_id'];
            }
            $country = $this->countryInformationAcquirer->getCountryInfo($address->getCountryId());
            foreach ($invoice->getItems() as $invoiceItem) {
                if (!$invoiceItem->getOrderItem()->getParentItem()) {
                    $product = $this->productFactory->create();
                    $product->load($product->getIdBySku($invoiceItem->getSku()));
                    /** @var Item $invoiceItem */
                    $obj = [];
                    $obj['InvoiceID'] = $invoice->getIncrementId();
                    $obj['CreateStamp'] = date('d/m/Y h:i A', strtotime($invoice->getCreatedAt()));;
                    $obj['OrderID'] = '';
                    $obj['WebOrderNumber'] = $order->getIncrementId();
                    $obj['ExternalOrderNumber'] = $order->getIncrementId();
                    $obj['PaymentMethod'] = $this->getPaymentInfo($order);
                    $obj['EmployeeNumber'] = 'orderapi';
                    $obj['Source'] = 'SP01WEB';
                    $obj['CouponCode'] = $order->getCouponCode();
                    $obj['Division'] = 'Y';
                    $obj['ShipStateorProvince'] = $address->getRegionCode();;
                    $obj['ShipCountry'] = $country->getThreeLetterAbbreviation();
                    $obj['Alt2'] = $invoiceItem->getSku();
                    $obj['UPC'] = $product->getUpc();
                    $obj['Quantity'] = $invoiceItem->getQty();
                    $obj['MerchTotal'] = ($invoiceItem->getPrice() * $invoiceItem->getQty());
                    $obj['Discount'] = $invoiceItem->getDiscountAmount();
                    $obj['MerchNetDiscount'] = $invoiceItem->getRowTotalInclTax();
                    $obj['Shipping'] = $order->getShippingAmount();
                    $obj['Tax'] = $invoiceItem->getTaxAmount();
                    $obj['StoreId'] = $storeId;
                    $obj['AutoSelection'] = $autoSelection;
                    $itemsData[] = $obj;
                }

            }
        }
        return $itemsData;
    }

    /**
     * @param Order $order
     * @return string|null
     */
    private function getPaymentInfo($order)
    {
        $method = $order->getPayment()->getMethod();
        if (in_array($method, ['authnetcim'])) {
            $cardTypeTranslationMap = [
                'AE'    => 'American Express',
                'DI'    => 'Discover',
                'DC'    => 'Diners Club',
                'JCB'   => 'JCB',
                'MC'    => 'MasterCard',
                'VI'    => 'Visa',
            ];
            $ccType = $order->getPayment()->getCcType();
            if (isset($cardTypeTranslationMap[$ccType])) {
                return $cardTypeTranslationMap[$ccType];
            }
        }
        return $method;
    }
}
