<?php

/*
 * This file is part of vaibhavpandeyvpz/kunfig package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Kunfig;

/**
 * Class ConfigTest
 * @package Kunfig\Tests
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $config = new Config(array(
            $key1 = 'somekey' => $value1 = 'somevalue',
            $key2 = 'otherkey' => $value2 = 'othervalue',
            $key3 = 'lastkey' => array('hi' => 'namaste'),
        ));
        $values = $config->all();
        $this->assertInternalType('array', $values);
        $this->assertArrayHasKey($key1, $values);
        $this->assertEquals($value1, $values[$key1]);
        $this->assertArrayHasKey($key2, $values);
        $this->assertEquals($value2, $values[$key2]);
        $this->assertArrayHasKey($key3, $values);
        $this->assertInternalType('array', $values[$key3]);
    }

    public function testCount()
    {
        $config = new Config(array(
            'somekey' => 'somevalue',
            'otherkey' => 'othervalue',
        ));
        $this->assertCount(2, $config);
    }

    public function testGet()
    {
        $config = new Config(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertEquals($value, $config->get($key));
    }

    public function testHas()
    {
        $config = new Config(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertTrue($config->has($key));
        $this->assertFalse($config->has('otherkey'));
    }

    public function testMix()
    {
        $config = new Config(array(
            'somekey' => 'somevalue',
            'otherkey' => 'othervalue',
            'arraykey' => array('arrayvalue'),
            'mixedkey' => array('mixedvalue', 123),
        ));
        $override = new Config(array(
            'somekey' => 'othervalue',
            'otherkey' => array('arrayvalue'),
            'arraykey' => 'somevalue',
            'mixedkey' => array('mixedvalue', 1234),
        ));
        $config->mix($override);
        $this->assertEquals('othervalue', $config->get('somekey'));
        $this->assertInstanceOf('Kunfig\\ConfigInterface', $config->get('otherkey'));
        $this->assertEquals('somevalue', $config->get('arraykey'));
        $this->assertInstanceOf('Kunfig\\ConfigInterface', $config->get('mixedkey'));
    }

    public function testSet()
    {
        $config = new Config();
        $this->assertFalse($config->has('somekey'));
        $config->set($key = 'somekey', $value = 'somevalue');
        $this->assertTrue($config->has('somekey'));
        $this->assertEquals($value, $config->get('somekey'));
    }

    public function testRemove()
    {
        $config = new Config(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertTrue($config->has($key));
        $config->remove($key);
        $this->assertFalse($config->has($key));
    }

    public function testSetState()
    {
        $config = Config::__set_state(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertTrue($config->has($key));
        $this->assertEquals($value, $config->get($key));
    }

    public function testArrayAccess()
    {
        $config = new Config(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertTrue(isset($config[$key]));
        $this->assertEquals($value, $config[$key]);
        $config[$key] = $value = 'othervalue';
        $this->assertEquals($value, $config[$key]);
        unset($config[$key]);
        $this->assertFalse(isset($config[$key]));
    }

    public function testPropertyAccess()
    {
        $config = new Config(array($key = 'somekey' => $value = 'somevalue'));
        $this->assertTrue(isset($config->{$key}));
        $this->assertEquals($value, $config->{$key});
        $config->{$key} = $value = 'othervalue';
        $this->assertEquals($value, $config->{$key});
        unset($config->{$key});
        $this->assertFalse(isset($config->{$key}));
    }
}
