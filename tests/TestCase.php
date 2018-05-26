<?php declare(strict_types=1);

namespace MCampbell508\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();
    }
}
