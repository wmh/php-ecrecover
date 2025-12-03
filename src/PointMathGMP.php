<?php

declare(strict_types=1);

namespace Wmh\EcRecover;

/**
 * Point arithmetic on elliptic curves using GMP
 */
class PointMathGMP
{
    /**
     * Multiply a point by a scalar using double-and-add algorithm
     */
    public function mulPoint(
        \GMP $x,
        \GMP $y,
        \GMP $scalar,
        \GMP $a,
        \GMP $b,
        \GMP $p
    ): array {
        if (gmp_cmp($scalar, gmp_init(0)) === 0) {
            return ['x' => null, 'y' => null]; // Point at infinity
        }

        if (gmp_cmp($scalar, gmp_init(1)) === 0) {
            return ['x' => $x, 'y' => $y];
        }

        $result = null;
        $addend = ['x' => $x, 'y' => $y];

        $scalarBin = gmp_strval($scalar, 2);
        for ($i = strlen($scalarBin) - 1; $i >= 0; $i--) {
            if ($scalarBin[$i] === '1') {
                if ($result === null) {
                    $result = $addend;
                } else {
                    $result = $this->addPoints(
                        $result['x'],
                        $result['y'],
                        $addend['x'],
                        $addend['y'],
                        $a,
                        $p
                    );
                }
            }
            
            if ($i > 0) {
                $addend = $this->doublePoint($addend['x'], $addend['y'], $a, $p);
            }
        }

        return $result ?? ['x' => null, 'y' => null];
    }

    /**
     * Add two points on an elliptic curve
     */
    public function addPoints(
        \GMP $x1,
        \GMP $y1,
        \GMP $x2,
        \GMP $y2,
        \GMP $a,
        \GMP $p
    ): array {
        if ($x1 === null || $y1 === null) {
            return ['x' => $x2, 'y' => $y2];
        }
        
        if ($x2 === null || $y2 === null) {
            return ['x' => $x1, 'y' => $y1];
        }

        if (gmp_cmp($x1, $x2) === 0) {
            if (gmp_cmp($y1, $y2) === 0) {
                return $this->doublePoint($x1, $y1, $a, $p);
            } else {
                return ['x' => null, 'y' => null]; // Point at infinity
            }
        }

        // slope = (y2 - y1) / (x2 - x1) mod p
        $dy = gmp_mod(gmp_sub($y2, $y1), $p);
        $dx = gmp_mod(gmp_sub($x2, $x1), $p);
        $slope = gmp_mod(gmp_mul($dy, gmp_invert($dx, $p)), $p);

        // x3 = slope^2 - x1 - x2 (mod p)
        $x3 = gmp_mod(gmp_sub(gmp_sub(gmp_pow($slope, 2), $x1), $x2), $p);
        
        // y3 = slope * (x1 - x3) - y1 (mod p)
        $y3 = gmp_mod(gmp_sub(gmp_mul($slope, gmp_sub($x1, $x3)), $y1), $p);

        return ['x' => $x3, 'y' => $y3];
    }

    /**
     * Double a point on an elliptic curve
     */
    public function doublePoint(\GMP $x, \GMP $y, \GMP $a, \GMP $p): array
    {
        if ($y === null || gmp_cmp($y, gmp_init(0)) === 0) {
            return ['x' => null, 'y' => null]; // Point at infinity
        }

        // slope = (3*x^2 + a) / (2*y) mod p
        $numerator = gmp_mod(gmp_add(gmp_mul(gmp_init(3), gmp_pow($x, 2)), $a), $p);
        $denominator = gmp_mod(gmp_mul(gmp_init(2), $y), $p);
        $slope = gmp_mod(gmp_mul($numerator, gmp_invert($denominator, $p)), $p);

        // x3 = slope^2 - 2*x (mod p)
        $x3 = gmp_mod(gmp_sub(gmp_pow($slope, 2), gmp_mul(gmp_init(2), $x)), $p);
        
        // y3 = slope * (x - x3) - y (mod p)
        $y3 = gmp_mod(gmp_sub(gmp_mul($slope, gmp_sub($x, $x3)), $y), $p);

        return ['x' => $x3, 'y' => $y3];
    }
}
