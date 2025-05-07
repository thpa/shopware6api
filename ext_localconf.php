<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use ThomasPaul\Shopware6Api\Controller\ProductController;

// Plugin-Registrierung
ExtensionUtility::configurePlugin(
    'Shopware6api',
    'Productlist',
    [
        ProductController::class => 'list,show',
    ],
    [
        ProductController::class => 'list,show',
    ]
);