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

namespace BinaryAnvil\JobManager\Model;

use Magento\Framework\Filesystem;
use BinaryAnvil\JobManager\Helper\Data;
use BinaryAnvil\JobManager\Helper\Config;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class Adapter
{
    /**
     * @var \Magento\Framework\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var \BinaryAnvil\JobManager\Helper\Data $helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface $directoryHandle
     */
    protected $directoryHandle;

    /**
     * Adapter constructor
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \BinaryAnvil\JobManager\Helper\Data $helper
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Data $helper
    ) {
        $this->filesystem = $filesystem;
        $this->helper = $helper;
        $this->directoryHandle = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR, DriverPool::FILE);
    }

    /**
     * Lock tmp file
     *
     * @param string $fileName
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function lock($fileName = '')
    {
        if (empty($fileName)) {
            $fileName = Config::JOB_LOCK_FILE;
        }

        $lock = $this->directoryHandle->getRelativePath($fileName);

        if ($this->directoryHandle->isExist($lock)) {
            return false;
        }

        if ($this->directoryHandle->touch($lock)) {
            return true;
        }

        return false;
    }

    /**
     * Unlock tmp file
     *
     * @param string $fileName
     * @return bool
     */
    public function unlock($fileName = '')
    {
        if (empty($fileName)) {
            $fileName = Config::JOB_LOCK_FILE;
        }

        if ($this->directoryHandle->isExist($fileName)) {
            try {
                $lock = $this->directoryHandle->getAbsolutePath($fileName);
                $this->directoryHandle->getDriver()->deleteFile($lock);

                return true;
            } catch (FileSystemException $e) {
                $this->helper->log->critical($e);

                return false;
            }
        }

        return false;
    }
}
