<?php

declare(strict_types=1);

namespace Wmh\EcRecover\Tests;

use PHPUnit\Framework\TestCase;
use Wmh\EcRecover\EcRecover;

class EcRecoverTest extends TestCase
{
    private const EXPECTED_ADDRESS = '0x61eb5b07a56799499f904c247022e580663b0e13';

    public function testEthSignRecover(): void
    {
        $hex = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
        $signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
        
        $address = EcRecover::recover($hex, $signed);
        
        $this->assertEquals(self::EXPECTED_ADDRESS, $address);
    }

    public function testPersonalSignRecover(): void
    {
        $msg = 'Hello!!';
        $signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
        
        $address = EcRecover::personalRecover($msg, $signed);
        
        $this->assertEquals(self::EXPECTED_ADDRESS, $address);
    }

    public function testTypedDataRecover(): void
    {
        $typeHash = EcRecover::keccak256('string Messageuint32 A number');
        $dataHash = EcRecover::keccak256('Hi, Alice!' . pack('N', 1337));
        $signed = '0x5147f94643843d709bf7c374fb8d619b27da739413f7ab8de5c788a6b7d2d10e53c4789d8a0398dee6c9f6cb69e094fa801cc00fa4d19f3b71b03a7a4b7cfee11c';
        
        $address = EcRecover::recoverTypedData($typeHash, $dataHash, $signed);
        
        $this->assertEquals(self::EXPECTED_ADDRESS, $address);
    }

    public function testKeccak256(): void
    {
        $result = EcRecover::keccak256('test');
        
        $this->assertStringStartsWith('0x', $result);
        $this->assertEquals(66, strlen($result)); // 0x + 64 hex chars
    }

    public function testStrToHex(): void
    {
        $result = EcRecover::strToHex('Hello');
        
        $this->assertEquals('0x48656c6c6f', $result);
    }

    public function testInvalidSignatureThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid signature v value');
        
        $hex = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
        $invalidSigned = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f99';
        
        EcRecover::recover($hex, $invalidSigned);
    }
}
