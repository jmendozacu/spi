<?php

namespace Magecomp\Matrixrate\Controller\Adminhtml\Export;

use Magento\Framework\App\ResponseInterface;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Config\Controller\Adminhtml\System\AbstractConfig;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;

class Exportrates extends AbstractConfig
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'matrixrate.csv';
      
        $gridBlock = $this->_view->getLayout()->createBlock( 
            'Magecomp\Matrixrate\Block\Adminhtml\Shipping\Carrier\Matrixrate\Grid'
        );
        
        $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
		if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $website->getConfig('carriers/matrixrate/condition_name');
        }
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
		
        $content = $gridBlock->getCsvFile();
       
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
       
    }
}
