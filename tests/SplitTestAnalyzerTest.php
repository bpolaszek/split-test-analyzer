<?php

namespace BenTools\SplitTestAnalyzer\Tests;

use BenTools\SplitTestAnalyzer\SplitTestAnalyzer;
use BenTools\SplitTestAnalyzer\Variation;
use PHPUnit\Framework\TestCase;

class SplitTestAnalyzerTest extends TestCase
{
    /**
     * @var Variation[]
     */
    private $variations = [];

    public function setUp()
    {
        $this->variations = [
            new Variation('foo', 91928, 89),
            new Variation('bar', 93784, 107),
        ];
    }

    public function testResult()
    {
        $test = SplitTestAnalyzer::create()->withVariations(...$this->variations);
        $result = $test->getResult();
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey('bar', $result);
        $this->assertEqualsApproximatively($result['foo'], 13, 1);
        $this->assertEqualsApproximatively($result['bar'], 88, 1);
    }

    public function testOrderedResult()
    {
        $test = SplitTestAnalyzer::create()->withVariations(...$this->variations);
        $result = $test->getOrderedVariations();
        $keys = array_keys($result);
        $this->assertEquals('bar', $keys[0]);
        $this->assertEquals('foo', $keys[1]);
    }

    public function testBestVariation()
    {
        $test = SplitTestAnalyzer::create()->withVariations(...$this->variations);
        $this->assertEquals($this->variations[1], $test->getBestVariation());
    }

    private function assertEqualsApproximatively(float $expected, float $value, float $tolerance)
    {
        $left = $expected - $tolerance;
        $right = $expected + $tolerance;
        $this->assertGreaterThanOrEqual($left, $value);
        $this->assertLessThanOrEqual($right, $value);
    }
}
