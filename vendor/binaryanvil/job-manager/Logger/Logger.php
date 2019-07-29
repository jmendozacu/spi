<?php
/**
 * Binary Anvil, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Binary Anvil, Inc. Software Agreement
 * that is bundled with this package in the file LICENSE_BAS.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.binaryanvil.com/software/license/
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryanvil.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this software to
 * newer versions in the future. If you wish to customize this software for
 * your needs please refer to http://www.binaryanvil.com/software for more
 * information.
 *
 * @category    BinaryAnvil
 * @package     JobManager
 * @copyright   Copyright (c) 2016-present Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\JobManager\Logger;

use BinaryAnvil\JobManager\Helper\Config;

class Logger extends \Monolog\Logger
{
    /**
     * @var \BinaryAnvil\JobManager\Helper\Config $helper
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Config $helper,
        $name,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);

        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = [])
    {
        return $this->_log($message, $context, 'critical', '[ ERROR ]');
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ INFO ]');
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = [])
    {
        return $this->_log($message, $context, 'debug', '[ DEBUG ]');
    }

    /**
     * {@inheritdoc}
     */
    public function warn($message, array $context = [])
    {
        return $this->_log($message, $context, 'warn', '[ WARN ]');
    }

    /**
     * {@inheritdoc}
     */
    public function success($message, array $context = [])
    {
        return $this->_log($message, $context, 'info', '[ SUCCESS ]');
    }

    // @codingStandardsIgnoreStart
    /**
     * Log message
     *
     * @param array|string $message
     * @param array $context
     * @param string $parent
     * @param string $level
     * @return bool
     */
    protected function _log($message, array $context = [], $parent = 'info', $level = '[ INFO ]')
    {
        if (!$this->helper->isDebugMode()) {
            return false;
        }

        if ($message instanceof \Exception) {
            $message = $message->getMessage() . "\n" . $message->getTraceAsString();
        }

        return parent::$parent($level . ' ' . $this->arrayToString($message), $context);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Convert array to string
     *
     * @param array|string $message
     * @return string
     */
    private function arrayToString($message)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        return $message;
    }
}
