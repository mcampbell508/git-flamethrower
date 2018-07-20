<?php

declare(strict_types=1);

namespace Shopworks\Tests;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use LaravelZero\Framework\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application and returns it.
     *
     */
    public function createApplication(): ApplicationContract
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
