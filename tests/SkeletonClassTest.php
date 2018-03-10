<?php declare(strict_types=1);

namespace MCampbell508\Tests;

use MCampbell508\Git\Flamethrower\SkeletonClass;
use PHPUnit\Framework\TestCase;

class SkeletonClassTest extends TestCase
{
    public function testEchoPhrase(): void
    {
        $myObj = new SkeletonClass();

        $res = $myObj->echoPhrase('foo');
        $this->assertEquals($res, 'foo');
    }
}
