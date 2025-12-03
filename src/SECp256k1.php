<?php

declare(strict_types=1);

namespace Wmh\EcRecover;

/**
 * SECp256k1 elliptic curve implementation
 * 
 * Implements the secp256k1 curve used by Bitcoin and Ethereum.
 */
class SECp256k1
{
    private array $curve;

    public function __construct()
    {
        // secp256k1 curve parameters
        $this->curve = [
            'p' => gmp_init('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F'),
            'a' => gmp_init('0x0'),
            'b' => gmp_init('0x7'),
            'n' => gmp_init('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141'),
            'G' => [
                'x' => gmp_init('0x79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798'),
                'y' => gmp_init('0x483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8')
            ]
        ];
    }

    public function getCurve(): array
    {
        return $this->curve;
    }

    /**
     * Get point on curve from x coordinate
     * 
     * @param \GMP $x X coordinate
     * @param bool $isYEven Whether Y should be even
     * @return array{x: \GMP, y: \GMP} Point on curve
     * @throws \Exception If point is not on curve
     */
    public function getPoint(\GMP $x, bool $isYEven): array
    {
        $p = $this->curve['p'];
        
        // Calculate y^2 = x^3 + 7 (mod p)
        $x3 = gmp_powm($x, gmp_init(3), $p);
        $y2 = gmp_mod(gmp_add($x3, $this->curve['b']), $p);
        
        // Calculate y = sqrt(y^2) mod p using Tonelli-Shanks
        $y = $this->modSqrt($y2, $p);
        
        if ($y === null) {
            throw new \Exception('Point is not on curve');
        }
        
        // Choose even or odd y
        $yIsEven = gmp_mod($y, gmp_init(2)) == 0;
        if ($yIsEven !== $isYEven) {
            $y = gmp_sub($p, $y);
        }
        
        return ['x' => $x, 'y' => $y];
    }

    /**
     * Modular square root (Tonelli-Shanks algorithm for p ≡ 3 (mod 4))
     */
    private function modSqrt(\GMP $a, \GMP $p): ?\GMP
    {
        // For secp256k1, p ≡ 3 (mod 4), so we can use: y = a^((p+1)/4) mod p
        $exp = gmp_div(gmp_add($p, gmp_init(1)), gmp_init(4));
        $y = gmp_powm($a, $exp, $p);
        
        // Verify
        if (gmp_cmp(gmp_powm($y, gmp_init(2), $p), gmp_mod($a, $p)) !== 0) {
            return null;
        }
        
        return $y;
    }

    /**
     * Multiply point by scalar
     */
    public function mulPoint(array $point, \GMP $scalar): array
    {
        $pointMath = new PointMathGMP();
        return $pointMath->mulPoint(
            $point['x'],
            $point['y'],
            $scalar,
            $this->curve['a'],
            $this->curve['b'],
            $this->curve['p']
        );
    }

    /**
     * Add two points on the curve
     */
    public function addPoints(array $point1, array $point2): array
    {
        $pointMath = new PointMathGMP();
        return $pointMath->addPoints(
            $point1['x'],
            $point1['y'],
            $point2['x'],
            $point2['y'],
            $this->curve['a'],
            $this->curve['p']
        );
    }
}
