<?php
$EMCONF[$_EXTKEY] = [
  'title' => 'Shopware6Api',
  'description' => 'TYPO3 extension to import and display products from the Shopware 6 Store API',
  'category' => 'plugin',
  'state' => 'beta',
  'author' => 'Thomas Paul',
  'author_email' => 'mail@thomaspaul.at',
  'version' => '0.0.1',
  'constraints' => [
    'depends' => [
      'typo3' => '13.0.0-13.9.99'
    ]
  ],
  'autoload' => [
    'psr-4' => [
      'ThomasPaul\\Shopware6Api\\' => 'Classes/'
    ]
  ],
  'autoload-dev' => [
    'psr-4' => [
      'ThomasPaul\\Shopware6Api\\Tests\\' => 'Tests/'
    ]
  ],
  'conflicts' => [],
  'suggests' => [],
];