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
  ],
  'columns' => [
    'shopware_id' => [
      'exclude' => 1,
      'label' => 'Shopware ID',
      'config' => ['type' => 'input', 'readOnly' => true]
    ],
    'name' => [
      'label' => 'Name',
      'config' => ['type' => 'input']
    ],
    'description' => [
      'label' => 'Beschreibung',
      'config' => ['type' => 'text']
    ],
    'price' => [
      'label' => 'Preis',
      'config' => ['type' => 'input', 'eval' => 'double2']
    ],
    'image' => [
      'label' => 'Bild',
      'config' => [
        'type' => 'inline',
        'foreign_table' => 'sys_file_reference',
        'foreign_field' => 'uid_foreign',
        'foreign_table_field' => 'tablenames',
        'foreign_match_fields' => [
          'fieldname' => 'image'
        ],
        'appearance' => [
          'createNewRelationLinkTitle' => 'Bild hinzufügen'
        ],
        'maxitems' => 1
      ]
    ],
    'is_active' => [
      'label' => 'Aktiv?',
      'config' => ['type' => 'check']
    ]
  ],
  'types' => [
    '0' => ['showitem' => 'shopware_id, name, price, image, description, is_active']
  ]
];