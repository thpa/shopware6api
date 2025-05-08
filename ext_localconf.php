<?php
defined('TYPO3') or die();

// Register Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Shopware6Api',
    'Productlist',
    [
        \ThomasPaul\Shopware6Api\Controller\ProductController::class => 'list,show'
    ],
    [
        \ThomasPaul\Shopware6Api\Controller\ProductController::class => 'list,show'
    ]
);

// Register FlexForm
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'shopware6api_productlist',
    'FILE:EXT:shopware6api/Configuration/FlexForms/Productlist.xml'
);
