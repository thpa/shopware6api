<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use ThomasPaul\Shopware6Api\Controller\ProductController;

ExtensionUtility::configurePlugin(
    'ThomasPaul.Shopware6Api',
    'ProductList',
    [
        ProductController::class => 'list,show',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// Optional: TypoScript Setup
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    'plugin.tx_shopware6api_productlist {
        settings {
            storagePid = {$plugin.tx_shopware6api_productlist.settings.storagePid}
            header = {$plugin.tx_shopware6api_productlist.settings.header}
            description = {$plugin.tx_shopware6api_productlist.settings.description}
            showDescription = {$plugin.tx_shopware6api_productlist.settings.showDescription}
            limit = {$plugin.tx_shopware6api_productlist.settings.limit}
            orderBy = {$plugin.tx_shopware6api_productlist.settings.orderBy}
            orderDirection = {$plugin.tx_shopware6api_productlist.settings.orderDirection}
            detailButtonText = {$plugin.tx_shopware6api_productlist.settings.detailButtonText}
        }
    }'
);
