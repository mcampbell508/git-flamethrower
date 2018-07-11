<?php

declare(strict_types=1);

namespace Shopworks\Tests\Unit\Process;

use PHPUnit\Framework\TestCase;
use Shopworks\Git\Review\Process\Process;

class ProcessTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_process_through_simple_method(): void
    {
        $this->assertInstanceOf(Process::class, (Process::simple('simple command')));
    }
}
