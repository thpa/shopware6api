<?php
return [
  'ctrl' => [
    'title' => 'Shopware Product',
    'label' => 'name',
    'tstamp' => 'tstamp',
    'crdate' => 'crdate',
    'cruser_id' => 'cruser_id',
    'delete' => 'deleted',
    'searchFields' => 'name,description,shopware_id',
    'iconfile' => 'EXT:shopware6api/Resources/Public/Icons/product.svg',
    'enablecolumns' => [
      'disabled' => 'hidden',
    ],
  ],
  'columns' => [
    'shopware_id' => [
      'exclude' => 1,
      'label' => 'Shopware ID',
      'config' => ['type' => 'input', 'readOnly' => true]
    ],
    'name' => [
      'label' => 'Name',
      'config' => [
        'type' => 'input',
        'required' => true,
      ],
    ],
    'description' => [
      'label' => 'Description',
      'config' => [
        'type' => 'text',
      ],
    ],
    'price' => [
      'label' => 'Price',
      'config' => [
        'type' => 'input',
        'eval' => 'double2',
      ],
    ],
    'images' => [
      'label' => 'Images',
      'config' => [
        'type' => 'inline',
        'foreign_table' => 'sys_file_reference',
        'foreign_field' => 'uid_foreign',
        'foreign_sortby' => 'sorting_foreign',
        'foreign_table_field' => 'tablenames',
        'foreign_match_fields' => [
          'fieldname' => 'images',
          'tablenames' => 'tx_shopware6api_domain_model_product',
        ],
        'appearance' => [
          'collapseAll' => true,
          'expandSingle' => true,
        ],
        'maxitems' => 9999,
      ],
    ],
    'is_active' => [
      'label' => 'Is Active',
      'config' => [
        'type' => 'check',
        'default' => 1,
      ],
    ]
  ],
  'types' => [
    '1' => [
      'showitem' => 'name, description, price, is_active, images, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden',
    ],
  ]
];