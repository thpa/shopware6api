<?php
defined('TYPO3') or die();

call_user_func(function () {
    $pluginSignature = 'shopware6api_productlist';

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:shopware6api/Configuration/FlexForms/ProductList.xml'
    );
});
