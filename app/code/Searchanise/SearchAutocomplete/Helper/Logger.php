<?php

namespace Searchanise\SearchAutocomplete\Helper;

class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TYPE_ERROR = 'Error';
    const TYPE_INFO = 'Info';
    const TYPE_WARNING = 'Warning';
    const TYPE_DEBUG = 'Debug';

    /**
     * @var \Searchanise\SearchAutocomplete\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Response
     */
    private $response = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Searchanise\SearchAutocomplete\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    public function log($message = '', $type = self::TYPE_ERROR)
    {
        switch ($type) {
            case self::TYPE_ERROR:
                $this->_logger->error('Searchanise #' . $message);
                break;
            case self::TYPE_WARNING:
                $this->_logger->warning('Searchanise #' . $message);
                break;
            case self::TYPE_DEBUG:
                $this->_logger->debug('Searchanise #' . $message);
                break;
            default:
                $this->_logger->info('Searchanise #' . $message);
        }

        if ($this->dataHelper->checkDebug(true)) {
            $this->printR("Searchanise # {$type}: {$message}");
        }
    }

    public function setResponseContext(\Magento\Framework\HTTP\PhpEnvironment\Response $httpResponse = null)
    {
        $this->response = $httpResponse;
        return $this;
    }

    public function printR()
    {
        static $count = 0;
        $args = func_get_args();
        $content = '';

        if (!empty($args)) {
            $content .= '<ol style="font-family: Courier; font-size: 12px; border: 1px solid #dedede; background-color: #efefef; float: left; padding-right: 20px;">';

            foreach ($args as $k => $v) {
                $v = htmlspecialchars(var_dump($v, true));
                if ($v == '') {
                    $v = '    ';
                }

                $content .= '<li><pre>' . $v . "\n" . '</pre></li>';
            }

            $content .= '</ol><div style="clear:left;"></div>';
        }

        $count++;

        if (!empty($content) && !empty($this->response)) {
            $this->response->appendBody($content);
        }
    }
}
