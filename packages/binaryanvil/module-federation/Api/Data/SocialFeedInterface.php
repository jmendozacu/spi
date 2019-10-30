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
 * @package     BinaryAnvil_Federation
 * @copyright   Copyright (c) 2018-2019 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */

namespace BinaryAnvil\Federation\Api\Data;

/**
 * Interface SocialFeedInterface
 * @package BinaryAnvil\Federation\Api\Data
 */
interface SocialFeedInterface
{
    /**#@+
     *
     * Field list for
     * 'binaryanvil_social_instance' table
     *
     * @type string
     */
    const KEY           = 'key';
    const PACKAGE       = 'package';
    const ENTITY_ID     = 'entity_id';
    const BLOCK_CLASS   = 'block_class';
    /**#@- */

    /**
     * Get entity_id
     *
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param string $entityId
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     */
    public function setEntityId($entityId);

    /**
     * Get key of social feed
     *
     * @return string|null
     */
    public function getKey();

    /**
     * Set key of social feed
     *
     * @param string $key
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     */
    public function setKey($key);

    /**
     * Get block class name
     *
     * @return string|null
     */
    public function getBlockClass();

    /**
     * Set block class name
     *
     * @param string $blockClass
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     */
    public function setBlockClass($blockClass);

    /**
     * Get package (module name)
     *
     * @return string|null
     */
    public function getPackage();

    /**
     * Set package (module name)
     *
     * @param string $package
     * @return \BinaryAnvil\Federation\Api\Data\SocialFeedInterface
     */
    public function setPackage($package);
}
