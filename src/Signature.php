<?php

declare(strict_types=1);

namespace Wmh\EcRecover;

/**
 * Signature recovery implementation for Ethereum
 * 
 * This class handles ECDSA signature recovery using the secp256k1 curve.
 */
class Signature
{
    /**
     * Recover public key from signature
     * 
     * @param \GMP $r R component of signature
     * @param \GMP $s S component of signature
     * @param \GMP $hash Message hash
     * @param int $recoveryFlags Recovery ID (0 or 1)
     * @return array{x: string, y: string} Public key coordinates as hex strings
     * @throws \Exception If recovery fails
     */
    public static function recoverPublicKey(\GMP $r, \GMP $s, \GMP $hash, int $recoveryFlags): array
    {
        $secp256k1 = new SECp256k1();
        $isYEven = ($recoveryFlags & 1) === 0;
        $isSecondKey = ($recoveryFlags & 2) !== 0;

        // Get curve parameters
        $curve = $secp256k1->getCurve();
        $n = $curve['n'];
        $G = $curve['G'];

        // Calculate e from hash
        $e = $hash;

        // If second key, add n to r
        if ($isSecondKey) {
            $r = gmp_add($r, $n);
        }

        // Get point R from r coordinate
        try {
            $R = $secp256k1->getPoint($r, $isYEven);
        } catch (\Exception $e) {
            throw new \Exception('Failed to recover public key: ' . $e->getMessage());
        }

        // Calculate public key: Q = r^-1 * (s*R - e*G)
        $rInv = gmp_invert($r, $n);
        
        // Calculate s*R
        $sR = $secp256k1->mulPoint($R, $s);
        
        // Calculate e*G
        $eG = $secp256k1->mulPoint($G, $e);
        
        // Calculate -e*G
        $negEG = [
            'x' => $eG['x'],
            'y' => gmp_sub($curve['p'], $eG['y'])
        ];
        
        // Calculate s*R - e*G
        $point = $secp256k1->addPoints($sR, $negEG);
        
        // Calculate Q = r^-1 * (s*R - e*G)
        $Q = $secp256k1->mulPoint($point, $rInv);

        // Convert to hex strings
        return [
            'x' => str_pad(gmp_strval($Q['x'], 16), 64, '0', STR_PAD_LEFT),
            'y' => str_pad(gmp_strval($Q['y'], 16), 64, '0', STR_PAD_LEFT)
        ];
    }
}
