<?php
require_once './ecrecover_helper.php';

// ecrecover
$hex = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
echo ecRecover($hex, $signed), "\n";

// personal_ecrecover
$msg = 'Hello!!';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
echo personal_ecRecover($msg, $signed), "\n";

// EIP712: eth_signTypedData
$presha_str = hex2bin(substr(keccak256('string Messageuint32 A number'), 2) . substr(keccak256('Hi, Alice!'. pack('N', 1337)), 2));
$hex = keccak256($presha_str);
$signed = '0x5147f94643843d709bf7c374fb8d619b27da739413f7ab8de5c788a6b7d2d10e53c4789d8a0398dee6c9f6cb69e094fa801cc00fa4d19f3b71b03a7a4b7cfee11c';
echo ecRecover($hex, $signed), "\n";
