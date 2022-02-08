<?php

namespace Stitch\Regression\Tests;

use PHPUnit\Framework\TestCase;
use Stitch\Regression\Regression;

class LogarithmicTest extends TestCase
{
    public function testNormal()
    {
        $data = [[1, 2.5], [2, 3.51], [3, 4.45], [4, 5.52], [5, 6.47], [6, 7.51]];
        $regression = new Regression();
        $result = $regression->logarithmic($data);
        $equation = $result->getEquation();
        $this->assertEquals([2.0011561106, 2.7287396024], $equation);
        $this->assertEquals(0.9339494713, $result->getR2());
        $this->assertEquals('y = 2.0011561106 + 2.7287396024 ln(x)', $result->getFormula());
        $points = [];
        foreach ($data as $v) {
            $points[] = [$v[0], $equation[0] + $equation[1] * log($v[0])];
        }
        $points = [];
        foreach ($data as $v) {
            $points[] = $result->getPredict()($v[0]);
        }
        $this->assertEquals($points, $result->getPoints());
    }

    public function testWithNull()
    {
        $data = [[1, 2.5], [2, 3.51], [3, 4.45], [4, null], [5, 6.47], [6, 7.51]];
        $regression = new Regression();
        $result = $regression->logarithmic($data);
        $equation = $result->getEquation();
        $this->assertEquals([1.1711420207, 3.353224064], $equation);
        $this->assertEquals(0.8806655621, $result->getR2());
        $this->assertEquals('y = 1.1711420207 + 3.353224064 ln(x)', $result->getFormula());
        $points = [];
        foreach ($data as $v) {
            $points[] = [$v[0], $equation[0] + $equation[1] * log($v[0])];
        }
        $points = [];
        foreach ($data as $v) {
            $points[] = $result->getPredict()($v[0]);
        }
        $this->assertEquals($points, $result->getPoints());
    }
}