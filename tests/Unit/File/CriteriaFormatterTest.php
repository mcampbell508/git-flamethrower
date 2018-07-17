<?php

namespace Shopworks\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Shopworks\Git\Review\File\CriteriaFormatter;

/** @covers CriteriaFormatter */
class CriteriaFormatterTest extends TestCase
{
    /** @var CriteriaFormatter $formatter */
    private $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new CriteriaFormatter();
    }

    /** @test */
    public function it_can_format_backslashes_into_the_appropriate_regex_format(): void
    {
        $this->assertEquals(
            '/\\/test\\//',
            $this->formatter->format('/test/')
        );
    }

    /** @test */
    public function it_can_format_wildcard_operators_into_the_appropriate_regex_format(): void
    {
        $this->assertEquals(
            '/\\/test\\/.*\\/example/',
            $this->formatter->format('/test/*/example')
        );
    }

    /** @test */
    public function it_can_format_an_array_of_strings(): void
    {
        $this->assertEquals(
            [
                '/\\/test\\//',
                '/\\/test\\/.*\\/example/',
            ],
            $this->formatter->formatMany([
                '/test/',
                '/test/*/example',
            ])
        );
    }
}
