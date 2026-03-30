<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Raideer\XmlParser\Span;

final class SpanTest extends TestCase
{
    public function testLength(): void
    {
        $span = new Span(5, 10, 1, 6);
        $this->assertEquals(5, $span->length());
    }

    public function testMerge(): void
    {
        $a = new Span(0, 5, 1, 1);
        $b = new Span(10, 20, 2, 3);

        $merged = $a->merge($b);

        $this->assertEquals(0, $merged->start);
        $this->assertEquals(20, $merged->end);
        $this->assertEquals(1, $merged->line);
        $this->assertEquals(1, $merged->column);
    }

    public function testMergeReversed(): void
    {
        $a = new Span(10, 20, 2, 3);
        $b = new Span(0, 5, 1, 1);

        $merged = $a->merge($b);

        $this->assertEquals(0, $merged->start);
        $this->assertEquals(20, $merged->end);
        $this->assertEquals(1, $merged->line);
        $this->assertEquals(1, $merged->column);
    }

    public function testJsonSerialize(): void
    {
        $span = new Span(0, 5, 1, 1);
        $json = json_encode($span);
        $data = json_decode($json, true);

        $this->assertEquals(0, $data['start']);
        $this->assertEquals(5, $data['end']);
        $this->assertEquals(1, $data['line']);
        $this->assertEquals(1, $data['column']);
    }
}
