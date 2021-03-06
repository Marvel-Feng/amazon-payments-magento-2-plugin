<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\Core\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * Ensures default authorization mode is set if upgrading from earlier versions
 *
 * @deprecated As of February 2021, this Legacy Amazon Pay plugin has been
 * deprecated, in favor of a newer Amazon Pay version available through GitHub
 * and Magento Marketplace. Please download the new plugin for automatic
 * updates and to continue providing your customers with a seamless checkout
 * experience. Please see https://pay.amazon.com/help/E32AAQBC2FY42HS for details
 * and installation instructions.
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // Used update query because all scopes needed to have this value updated and this is a fast, simple approach
        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $select = $setup->getConnection()->select()->from(
                $setup->getTable('core_config_data'),
                ['config_id', 'value']
            )->where(
                'path = ?',
                'payment/amazon_payment/authorization_mode'
            );

            foreach ($setup->getConnection()->fetchAll($select) as $configRow) {
                if ($configRow['value'] === 'asynchronous') {
                    $row = [
                        'value' => 'synchronous_possible'
                    ];
                    $setup->getConnection()->update(
                        $setup->getTable('core_config_data'),
                        $row,
                        ['config_id = ?' => $configRow['config_id']]
                    );
                }
            }
        }
    }
}
