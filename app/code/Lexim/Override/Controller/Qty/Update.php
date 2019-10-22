<?php

namespace Lexim\Override\Controller\Qty;

/**
 * Class Update
 * @package Lexim\Override\Controller\Qty
 */
class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_baseUrl;

    protected $_resultData = [
        'status' => "200",
        'message' => ''
    ];

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_baseUrl = $context->getUrl()->getBaseUrl();
    }

    /**
     * Update qty
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $token = $this->getToken();
        $this->updateQty($token);
        return $result->setData($this->getResultData());
    }

    /**
     * @return \Magento\Framework\UrlInterface|string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @return array
     */
    public function getResultData()
    {
        return $this->_resultData;
    }

    /**
     * @param $data
     */
    public function setResultData($data)
    {
        $this->_resultData = $data;
    }

    /**
     * @return bool|mixed|string
     */
    protected function getToken()
    {
        // Get token
        $adminUrl = $this->getBaseUrl() . 'rest/V1/integration/admin/token/';
        $data = [
            "username" => "mjin",
            "password" => "3Yujo&djP"
        ];

        $data_string = json_encode($data);
        $ch = curl_init($adminUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            )
        );
        $token = curl_exec($ch);
        $token = json_decode($token);
        return $token;
    }

    /**
     * @param bool $token
     * @return bool
     */
    public function updateQty($token = false)
    {
        if (!$token) {
            return false;
        }

        //Use above token into header
        $headers = array("Authorization: Bearer $token", "Content-Type: application/json");

        // Get Json data
        //$json = file_get_contents('http://s.allheart.com/inventory/mage.php');

        $json = '{
            "inv":  [
                {
                    "yid": "1",
                    "u": "",
                    "c": "",
                    "s": "CK-KCKSTRTAMR",
                    "w": "",
                    "d": 0,
                    "dd": "",
                    "rrp": 0,
                    "av": 24,
                    "sp": 0
                },
                {
                    "yid": "2",
                    "u": "",
                    "c": "",
                    "s": "CK-KCKSTRTDLT",
                    "w": "",
                    "d": 0,
                    "dd": "",
                    "rrp": 0,
                    "av": 36,
                    "sp": 0
                } 
            ]
        }';

        $data = json_decode($json);

        if (isset($data->inv) && is_array($data->inv)) {
            foreach ($data->inv as $item) {
                if (isset($item->s) && $item->s !== '') {
                    $requestUrl = $this->getBaseUrl() . 'rest/V1/products/' . $item->s . '/stockItems/1';

                    if (isset($item->av) && intval($item->av) > 0) {
                        $sampleProductData = array(
                            'stockItem' => [
                                "qty" => $item->av
                            ]
                        );

                        $productData = json_encode($sampleProductData);

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $requestUrl);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $productData);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        curl_close($ch);
                        unset($productData);
                        unset($sampleProductData);

                        $this->_resultData['message'] .= "Updated qty = " . $item->av . " for Product_Sku = " . $item->s . ". ";
                    }
                }
            }
        }

        return true;
    }


}