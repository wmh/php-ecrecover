# PHP ECRecover

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-CC0--1.0-green.svg)](LICENSE)

A modern PHP library for recovering Ethereum addresses from signed messages using ECDSA signature recovery. This library supports `eth_sign`, `personal_sign`, and EIP-712 `eth_signTypedData` signature verification.

## Features

- ✅ **Modern PHP 8.1+** - Uses latest PHP features including typed properties, return types, and strict types
- ✅ **PSR-4 Autoloading** - Proper namespace organization
- ✅ **Type Safe** - Full type declarations with strict typing
- ✅ **Well Tested** - Includes PHPUnit tests
- ✅ **Multiple Signature Types** - Supports eth_sign, personal_sign, and EIP-712
- ✅ **No External Dependencies** - Only requires GMP extension and Keccak library

## Requirements

- PHP >= 8.1
- GMP extension (`ext-gmp`)
- Composer

## Installation

```bash
composer require wmh/php-ecrecover
```

Or clone and install:

```bash
git clone --recursive https://github.com/wmh/php-ecrecover.git
cd php-ecrecover
composer install
```

## Usage

### Basic eth_sign Verification

```php
<?php

require_once 'vendor/autoload.php';

use Wmh\EcRecover\EcRecover;

$messageHash = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
$signature = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';

$address = EcRecover::recover($messageHash, $signature);
echo "Recovered address: $address\n";
```

### Personal Sign Verification

```php
<?php

use Wmh\EcRecover\EcRecover;

$message = 'Hello!!';
$signature = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';

$address = EcRecover::personalRecover($message, $signature);
echo "Recovered address: $address\n";
```

### JavaScript Signing Example (Client-Side)

#### eth_sign

```javascript
const msg = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
const from = await ethereum.request({ method: 'eth_requestAccounts' });
const signature = await ethereum.request({
  method: 'eth_sign',
  params: [from[0], msg]
});
console.log('Signature:', signature);
```

#### personal_sign

```javascript
const text = 'Hello!!';
const msg = ethers.utils.hexlify(ethers.utils.toUtf8Bytes(text));
const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
const signature = await ethereum.request({
  method: 'personal_sign',
  params: [msg, accounts[0]]
});
console.log('Signature:', signature);
```

### EIP-712 Typed Data Verification

```php
<?php

use Wmh\EcRecover\EcRecover;

$typeHash = EcRecover::keccak256('string Messageuint32 A number');
$dataHash = EcRecover::keccak256('Hi, Alice!' . pack('N', 1337));
$signature = '0x5147f94643843d709bf7c374fb8d619b27da739413f7ab8de5c788a6b7d2d10e53c4789d8a0398dee6c9f6cb69e094fa801cc00fa4d19f3b71b03a7a4b7cfee11c';

$address = EcRecover::recoverTypedData($typeHash, $dataHash, $signature);
echo "Recovered address: $address\n";
```

## Running Examples

```bash
php example.php
```

## Running Tests

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit
```

## API Reference

### `EcRecover::recover(string $messageHash, string $signature): string`

Recovers Ethereum address from a signed message hash.

**Parameters:**
- `$messageHash` - The message hash (with or without 0x prefix)
- `$signature` - The signature hex string (with or without 0x prefix)

**Returns:** The recovered Ethereum address (with 0x prefix)

### `EcRecover::personalRecover(string $message, string $signature): string`

Recovers Ethereum address from a personal_sign signature.

**Parameters:**
- `$message` - The original message that was signed
- `$signature` - The signature hex string (with or without 0x prefix)

**Returns:** The recovered Ethereum address (with 0x prefix)

### `EcRecover::recoverTypedData(string $typeHash, string $dataHash, string $signature): string`

Recovers address from EIP-712 typed data signature.

**Parameters:**
- `$typeHash` - The type hash
- `$dataHash` - The data hash
- `$signature` - The signature hex string

**Returns:** The recovered Ethereum address (with 0x prefix)

### `EcRecover::keccak256(string $data): string`

Calculates Keccak256 hash of data.

**Parameters:**
- `$data` - The data to hash

**Returns:** The hash with 0x prefix

## Upgrade from v1.x

The library has been modernized with the following breaking changes:

1. **PHP 8.1+ required** - Upgraded from PHP 7.1
2. **Namespaced classes** - Now uses `Wmh\EcRecover` namespace
3. **Static methods** - All methods are now static on the `EcRecover` class
4. **Function name changes:**
   - `ecRecover()` → `EcRecover::recover()`
   - `personal_ecRecover()` → `EcRecover::personalRecover()`
   - `keccak256()` → `EcRecover::keccak256()`

### Migration Example

**Before (v1.x):**
```php
require_once './ecrecover_helper.php';
$address = ecRecover($hex, $signed);
```

**After (v2.x):**
```php
use Wmh\EcRecover\EcRecover;
$address = EcRecover::recover($hex, $signed);
```

## How It Works

This library implements ECDSA signature recovery on the secp256k1 elliptic curve (the same curve used by Bitcoin and Ethereum). When a message is signed with a private key, the signature contains enough information to recover the corresponding public key, which is then hashed to derive the Ethereum address.

The recovery process involves:
1. Parsing the signature into its components (r, s, v)
2. Using the recovery ID (v) to determine the correct public key
3. Performing elliptic curve point arithmetic to recover the public key
4. Hashing the public key with Keccak256 to get the Ethereum address

## References

- [Ethereum JSON-RPC API](https://ethereum.org/en/developers/docs/apis/json-rpc/)
- [EIP-191: Signed Data Standard](https://eips.ethereum.org/EIPS/eip-191)
- [EIP-712: Typed Structured Data Hashing and Signing](https://eips.ethereum.org/EIPS/eip-712)
- [secp256k1 Curve](https://en.bitcoin.it/wiki/Secp256k1)

## License

CC0-1.0 - Public Domain

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

- Original implementation inspired by [Verifying an Ethereum Signature](https://thomasclowes.com/verifying-an-ethereum-signature-on-the-server-php-2/)
- Uses [kornrunner/keccak](https://github.com/kornrunner/keccak) for Keccak256 hashing
- Elliptic curve math adapted from [CryptoCurrencyPHP](https://github.com/tuaris/CryptoCurrencyPHP)
