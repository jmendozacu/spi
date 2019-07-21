<?php
namespace Magecomp\Matrixrate\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\Factory as FomDataFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Backend\Model\UrlInterface;

class Export extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_backendUrl;

    public function __construct(
        FomDataFactory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
    }

    public function getElementHtml()
    {
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $params = ['website' => $buttonBlock->getRequest()->getParam('website')];

        $url = $this->_backendUrl->getUrl("matrixrate/export/exportrates", $params);
        $data = [
            'label' 	=> __('Export CSV'),
            'onclick'	=> "setLocation('".$url."' )",
            'class'		=> '',
        ];

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
