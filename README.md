# Shopware6Api for TYPO3

TYPO3 extension to import and display products from the Shopware 6 Store API.

## Features

- Import Shopware 6 products into TYPO3
- Store imported products in a sysfolder
- Display products via Extbase plugin (list & detail)
- API configuration via Extension Settings
- Fallback storagePid configurable via TypoScript
- Symfony Console Command for manual or scheduled imports

## Installation

Install via Composer:

```bash
composer require thomaspaul/shopware6api
```

Activate the extension in the TYPO3 Extension Manager or via CLI.

## Configuration

Add your Shopware Store API credentials in the Extension Configuration:

```
apiBaseUrl = https://your-shop-domain.com
accessKey  = your-shopware-access-key
```

Optionally, define a default sysfolder for imported records:

```typoscript
plugin.tx_shopware6api_productlist.settings.storagePid = 123
```

## Import via CLI

Run the following command to fetch and import products:

```bash
vendor/bin/typo3 shopware6api:import
```

## Usage

Insert the **Shopware Product List** plugin on a page and configure the desired storage location via the FlexForm.
