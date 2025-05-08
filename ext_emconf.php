<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Shopware 6 API',
    'description' => 'TYPO3 extension to import and display products from the Shopware 6 Store API',
    'category' => 'plugin',
    'author' => 'Thomas Paul',
    'author_email' => 'mail@thomaspaul.at',
    'state' => 'alpha',
    'clearCacheOnLoad' => 1,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'php' => '8.1.0-8.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'ThomasPaul\\Shopware6Api\\' => 'Classes/',
        ],
    ],
    'autoload-dev' => [
        'psr-4' => [
            'ThomasPaul\\Shopware6Api\\Tests\\' => 'Tests/',
        ],
    ],
];