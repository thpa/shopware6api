<?php
$EMCONF[$_EXTKEY] = [
  'title' => 'Shopware6Api',
  'description' => 'Zeigt Produkte aus Shopware 6 in TYPO3 an und speichert sie lokal.',
  'category' => 'plugin',
  'state' => 'beta',
  'author' => 'Thomas Paul',
  'author_email' => 'thomas@example.com',
  'version' => '1.0.0',
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