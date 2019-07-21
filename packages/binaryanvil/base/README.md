# Binary Anvil Magento 2 Base Module

[![Binary Anvil Base](https://img.shields.io/badge/version-1.0.3-green.svg)](https://bitbucket.binaryanvil.tools/projects/BINARYANVIL/repos/base/browse)
[![Wiki Status](https://img.shields.io/badge/wiki-partially-yellow.svg)](https://wiki.binaryanvil.tools/display/KB/M2+Expensions%3A+Base+package)
[![Package](https://img.shields.io/badge/package-1.0.3-blue.svg)](https://packages.binaryanvil.tools/#binaryanvil/base)

## Usage

Used to define main methods wich will be used in **every** project and as a placeholder for store config menu.

### Store config menu example

Create file `app/code/BinaryAnvil/MODULENAME/etc/adminhtml/system.xml` and add following *(replace `MODULENAME` width real module name)*:

```xml
<?xml version="1.0"?>
<!--
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
 * @package     BinaryAnvil_MODULENAME
 * @copyright   Copyright (c) 2015-2016 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Config:etc/system_file.xsd">
    <system>
        <section id="binaryanvil_MODULENAME" translate="label" type="text" sortOrder="20" showInDefault="1" showInWebsite="0" showInStore="0">
            <label>MODULE MENU TITLE</label>
            <tab>binaryanvil</tab>
            <resource>BinaryAnvil_Base::binaryanvil_base</resource>
            <group id="MODULENAME_general" translate="label" type="text" sortOrder="10" showInDefault="1" showInWebsite="0" showInStore="0">
                <label>General Settings</label>
                <field id="MODULENAME_enabled" translate="label" type="select" sortOrder="10" showInDefault="1" showInWebsite="0" showInStore="0">
                    <label>MODULENAME Enabled</label>
                    <source_model>Magento\Config\Model\Config\Source\Yesno</source_model>
                </field>
            </group>
        </section>
    </system>
</config>
```

### Admin panel menu example

Create file `app/code/BinaryAnvil/MODULENAME/etc/adminhtml/menu.xml` and add following *(replace `MODULENAME` width real module name)*:

```xml
<?xml version="1.0"?>
<!--
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
 * @package     MODULENAME
 * @copyright   Copyright (c) 2015-2016 Binary Anvil,Inc. (http://www.binaryanvil.com)
 * @license     http://www.binaryanvil.com/software/license
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Backend:etc/menu.xsd">
    <menu>
        <add id="BinaryAnvil_MODULENAME::binaryanvil_modulename"
             title="MODULENAME"
             resource="BinaryAnvil_MODULENAME::binaryanvil_modulename"
             parent="BinaryAnvil_Base::binaryanvil_base"
             module="BinaryAnvil_MODULENAME"
             sortOrder="20"/>
        <add id="BinaryAnvil_MODULENAME::modulenamesettings"
             title="Settings"
             resource="BinaryAnvil_MODULENAME::modulenamesettings"
             module="BinaryAnvil_MODULENAME"
             parent="BinaryAnvil_MODULENAME::binaryanvil_modulename"
             action="adminhtml/system_config/edit/section/binaryanvil_modulename"
             sortOrder="10"/>
    </menu>
</config>
```
