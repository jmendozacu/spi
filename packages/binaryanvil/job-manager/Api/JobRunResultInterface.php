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

namespace BinaryAnvil\JobManager\Api;

interface JobRunResultInterface
{
    const FIELD_NAME_ERRORS = 'errors';

    const FIELD_NAME_SUCCESS = 'success';

    const FIELD_NAME_NOTICE  = 'notice';

    const FIELD_NAME_STATUS = 'status';

    const FIELD_NAME_REQUEST = 'request';

    const FIELD_NAME_RESPONSE = 'response';

    /**
     * Get job run errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Set job run errors
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors = []);

    /**
     * Get job run succcess message
     *
     * @return string
     */
    public function getSuccess();

    /**
     * Set job run success message
     *
     * @param string $success
     * @return $this
     */
    public function setSuccess($success);

    /**
     * Get job run notice message
     *
     * @return string
     */
    public function getNotice();

    /**
     * Set job run notice message
     *
     * @param array $notice
     * @return $this
     */
    public function setNotice(array $notice = []);

    /**
     * Get job run request
     *
     * @return string
     */
    public function getRequest();

    /**
     * Set job run request
     *
     * @param array $request
     * @return $this
     */
    public function setRequest(array $request = []);

    /**
     * Get job run response
     *
     * @return string
     */
    public function getResponse();

    /**
     * Set job run response
     *
     * @param array $response
     * @return $this
     */
    public function setResponse(array $response = []);

    /**
     * Get job run status
     *
     * @return bool
     */
    public function isDone();

    /**
     * Get job run result status
     * Can be only boolean true or false (default = false)
     *
     * @param bool $status
     * @return $this
     */
    public function setDone($status = false);

    /**
     * Get instance data
     *
     * @param string $key
     * @param string|int $index
     * @return array
     */
    public function getData($key = '', $index = null);
}
