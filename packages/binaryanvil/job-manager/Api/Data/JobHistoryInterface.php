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
namespace BinaryAnvil\JobManager\Api\Data;

interface JobHistoryInterface
{
    /**
     * Job history id
     *
     * @return int | null
     */
    public function getId();

    /**
     * Job id
     *
     * @return int | null
     */
    public function getJobId();

    /**
     * Job history message
     *
     * @return string | null
     */
    public function getMessage();

    /**
     * Set job history message
     *
     * @param string $message
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     */
    public function setMessage($message);

    /**
     * Job history message type
     *
     * @return int
     */
    public function getMessageType();

    /**
     * Set job history message type
     *
     * @param int $messageType
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     */
    public function setMessageType($messageType);

    /**
     * Job history message time
     *
     * @return string
     */
    public function getMessageTime();

    /**
     * Set job history message time
     *
     * @param string $messageTime
     * @return \BinaryAnvil\JobManager\Api\Data\JobHistoryInterface
     */
    public function setMessageTime($messageTime);
}
