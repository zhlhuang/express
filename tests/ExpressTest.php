<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/10/6
 * Time: 11:24.
 */

namespace Zhlhuang\Express\Tests;

use PHPUnit\Framework\TestCase;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Express;

class ExpressTest extends TestCase
{
    public function testQueryExpressWithInvalidCode()
    {
        $express = new Express();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid express code abc');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithRequiredPostId()
    {
        $express = new Express();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Post ID is required');
        $express->query('shunfeng', '');
        $this->fail('Faild to assert');
    }
}
