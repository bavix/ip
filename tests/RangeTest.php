<?php

namespace Bavix\Test;

use Bavix\IP\RangeUtil;

class RangeTest extends TestCase
{

    /**
     * @return void
     */
    public function testLocalhost(): void
    {
        $subnet = '127.0.0.0/8';
        $range = RangeUtil::sharedInstance();

        $this->assertTrue($range->check('127.0.0.0', $subnet));

        $this->assertTrue($range->check('127.0.0.1', $subnet));
        $this->assertTrue($range->check('127.0.0.255', $subnet));

        $this->assertTrue($range->check('127.0.1.0', $subnet));
        $this->assertTrue($range->check('127.0.1.1', $subnet));
        $this->assertTrue($range->check('127.0.1.255', $subnet));
        $this->assertTrue($range->check('127.0.255.0', $subnet));
        $this->assertTrue($range->check('127.0.255.255', $subnet));

        $this->assertTrue($range->check('127.1.1.1', $subnet));
        $this->assertTrue($range->check('127.1.1.255', $subnet));
        $this->assertTrue($range->check('127.1.255.255', $subnet));
        $this->assertTrue($range->check('127.255.255.255', $subnet));

        $this->assertFalse($range->check('0.0.0.0', $subnet));
        $this->assertFalse($range->check('126.255.255.255', $subnet));
        $this->assertFalse($range->check('128.0.0.1', $subnet));
    }

}
