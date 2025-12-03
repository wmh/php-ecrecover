<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Wmh\EcRecover\EcRecover;

echo "=== Ethereum Signature Recovery Examples ===\n\n";

// Example 1: eth_sign & verify
echo "1. eth_sign verification:\n";
$hex = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
$address = EcRecover::recover($hex, $signed);
echo "   Message Hash: $hex\n";
echo "   Signature:    $signed\n";
echo "   Recovered:    $address\n\n";

// Example 2: personal_sign
echo "2. personal_sign verification:\n";
$msg = 'Hello!!';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
$address = EcRecover::personalRecover($msg, $signed);
echo "   Message:   $msg\n";
echo "   Signature: $signed\n";
echo "   Recovered: $address\n\n";

// Example 3: EIP-712 eth_signTypedData
echo "3. EIP-712 eth_signTypedData verification:\n";
$typeHash = EcRecover::keccak256('string Messageuint32 A number');
$dataHash = EcRecover::keccak256('Hi, Alice!' . pack('N', 1337));
$signed = '0x5147f94643843d709bf7c374fb8d619b27da739413f7ab8de5c788a6b7d2d10e53c4789d8a0398dee6c9f6cb69e094fa801cc00fa4d19f3b71b03a7a4b7cfee11c';
$address = EcRecover::recoverTypedData($typeHash, $dataHash, $signed);
echo "   Type Hash: $typeHash\n";
echo "   Data Hash: $dataHash\n";
echo "   Signature: $signed\n";
echo "   Recovered: $address\n\n";

echo "✓ All examples completed successfully!\n";
