<?php
defined('TYPO3') or die();

use ThomasPaul\Shopware6Api\Command\ProductImportCommand;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = ProductImportCommand::class;