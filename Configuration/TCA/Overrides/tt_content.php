<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'mkoptin',
    'Optin',
    'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_optin'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mkoptin_optin'] = 'layout,select_key,pages,recursive';
