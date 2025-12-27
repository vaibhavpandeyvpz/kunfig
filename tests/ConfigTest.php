<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/kunfig package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Kunfig;

use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * Test suite for the Config class and ConfigInterface implementation.
 * Covers all functionality including basic operations, nested configurations,
 * array access, property access, iteration, and edge cases.
 *
 * @author  Vaibhav Pandey <contact@vaibhavpandey.com>
 */
class ConfigTest extends TestCase
{
    public function test_all(): void
    {
        $key1 = 'somekey';
        $value1 = 'somevalue';
        $key2 = 'otherkey';
        $value2 = 'othervalue';
        $key3 = 'lastkey';

        $config = new Config([
            $key1 => $value1,
            $key2 => $value2,
            $key3 => ['hi' => 'namaste'],
        ]);
        $values = $config->all();
        $this->assertIsArray($values);
        $this->assertArrayHasKey($key1, $values);
        $this->assertEquals($value1, $values[$key1]);
        $this->assertArrayHasKey($key2, $values);
        $this->assertEquals($value2, $values[$key2]);
        $this->assertArrayHasKey($key3, $values);
        $this->assertIsArray($values[$key3]);
    }

    public function test_count(): void
    {
        $config = new Config([
            'somekey' => 'somevalue',
            'otherkey' => 'othervalue',
        ]);
        $this->assertCount(2, $config);
    }

    public function test_get(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = new Config([$key => $value]);
        $this->assertEquals($value, $config->get($key));
    }

    public function test_has(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = new Config([$key => $value]);
        $this->assertTrue($config->has($key));
        $this->assertFalse($config->has('otherkey'));
    }

    public function test_mix(): void
    {
        $config = new Config([
            'somekey' => 'somevalue',
            'otherkey' => 'othervalue',
            'arraykey' => ['arrayvalue'],
            'mixedkey' => ['mixedvalue', 123],
        ]);
        $override = new Config([
            'somekey' => 'othervalue',
            'otherkey' => ['arrayvalue'],
            'arraykey' => 'somevalue',
            'mixedkey' => ['mixedvalue', 1234],
        ]);
        $config->mix($override);
        $this->assertEquals('othervalue', $config->get('somekey'));
        $this->assertInstanceOf(ConfigInterface::class, $config->get('otherkey'));
        $this->assertEquals('somevalue', $config->get('arraykey'));
        $this->assertInstanceOf(ConfigInterface::class, $config->get('mixedkey'));
    }

    public function test_set(): void
    {
        $config = new Config;
        $this->assertFalse($config->has('somekey'));
        $key = 'somekey';
        $value = 'somevalue';
        $config->set($key, $value);
        $this->assertTrue($config->has('somekey'));
        $this->assertEquals($value, $config->get('somekey'));
    }

    public function test_remove(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = new Config([$key => $value]);
        $this->assertTrue($config->has($key));
        $config->remove($key);
        $this->assertFalse($config->has($key));
    }

    public function test_set_state(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = Config::__set_state([$key => $value]);
        $this->assertTrue($config->has($key));
        $this->assertEquals($value, $config->get($key));
    }

    public function test_array_access(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = new Config([$key => $value]);
        $this->assertTrue(isset($config[$key]));
        $this->assertEquals($value, $config[$key]);
        $value = 'othervalue';
        $config[$key] = $value;
        $this->assertEquals($value, $config[$key]);
        unset($config[$key]);
        $this->assertFalse(isset($config[$key]));
    }

    public function test_property_access(): void
    {
        $key = 'somekey';
        $value = 'somevalue';
        $config = new Config([$key => $value]);
        $this->assertTrue(isset($config->{$key}));
        $this->assertEquals($value, $config->{$key});
        $value = 'othervalue';
        $config->{$key} = $value;
        $this->assertEquals($value, $config->{$key});
        unset($config->{$key});
        $this->assertFalse(isset($config->{$key}));
    }

    public function test_empty_config(): void
    {
        $config = new Config;
        $this->assertCount(0, $config);
        $this->assertEmpty($config->all());
        $this->assertFalse($config->has('anykey'));
    }

    public function test_get_with_fallback(): void
    {
        $config = new Config(['existing' => 'value']);
        $this->assertEquals('value', $config->get('existing', 'fallback'));
        $this->assertEquals('fallback', $config->get('nonexistent', 'fallback'));
        $this->assertNull($config->get('nonexistent'));
    }

    public function test_get_non_existent_key(): void
    {
        $config = new Config;
        $this->assertNull($config->get('nonexistent'));
        $this->assertFalse($config->get('nonexistent', false));
        $this->assertEquals([], $config->get('nonexistent', []));
    }

    public function test_nested_config_access(): void
    {
        $config = new Config([
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'credentials' => [
                    'user' => 'admin',
                    'password' => 'secret',
                ],
            ],
        ]);

        $this->assertInstanceOf(ConfigInterface::class, $config->get('database'));
        $this->assertEquals('localhost', $config->database->host);
        $this->assertEquals(3306, $config->database->port);
        $this->assertInstanceOf(ConfigInterface::class, $config->database->credentials);
        $this->assertEquals('admin', $config->database->credentials->user);
        $this->assertEquals('secret', $config->database->credentials->password);
    }

    public function test_nested_config_array_access(): void
    {
        $config = new Config([
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
            ],
        ]);

        $this->assertInstanceOf(ConfigInterface::class, $config['app']);
        $this->assertEquals('MyApp', $config['app']['name']);
        $this->assertEquals('1.0.0', $config['app']['version']);
    }

    public function test_deeply_nested_configs(): void
    {
        $config = new Config([
            'level1' => [
                'level2' => [
                    'level3' => [
                        'value' => 'deep',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('deep', $config->level1->level2->level3->value);
        $this->assertEquals('deep', $config['level1']['level2']['level3']['value']);
    }

    public function test_mix_with_empty_config(): void
    {
        $config = new Config(['key' => 'value']);
        $empty = new Config;
        $config->mix($empty);
        $this->assertEquals('value', $config->get('key'));
        $this->assertCount(1, $config);
    }

    public function test_mix_nested_configs_recursive(): void
    {
        $config = new Config([
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
                'settings' => [
                    'debug' => false,
                    'cache' => true,
                ],
            ],
        ]);

        $override = new Config([
            'app' => [
                'version' => '2.0.0',
                'settings' => [
                    'debug' => true,
                ],
            ],
        ]);

        $config->mix($override);
        $this->assertEquals('MyApp', $config->app->name);
        $this->assertEquals('2.0.0', $config->app->version);
        $this->assertTrue($config->app->settings->debug);
        $this->assertTrue($config->app->settings->cache);
    }

    public function test_mix_overwrites_non_config_values(): void
    {
        $config = new Config([
            'key1' => 'value1',
            'key2' => ['nested' => 'value'],
        ]);

        $override = new Config([
            'key1' => ['new' => 'structure'],
            'key2' => 'simple_value',
        ]);

        $config->mix($override);
        $this->assertInstanceOf(ConfigInterface::class, $config->get('key1'));
        $this->assertEquals('simple_value', $config->get('key2'));
    }

    public function test_set_with_different_types(): void
    {
        $config = new Config;

        $config->set('string', 'text');
        $config->set('integer', 42);
        $config->set('float', 3.14);
        $config->set('boolean', true);
        $config->set('null_value', null);
        $config->set('array', ['a' => 'b']);

        $this->assertEquals('text', $config->get('string'));
        $this->assertIsString($config->get('string'));
        $this->assertEquals(42, $config->get('integer'));
        $this->assertIsInt($config->get('integer'));
        $this->assertEquals(3.14, $config->get('float'));
        $this->assertIsFloat($config->get('float'));
        $this->assertTrue($config->get('boolean'));
        $this->assertIsBool($config->get('boolean'));
        $this->assertNull($config->get('null_value'));
        $this->assertInstanceOf(ConfigInterface::class, $config->get('array'));
    }

    public function test_set_overwrites_existing_value(): void
    {
        $config = new Config(['key' => 'old']);
        $config->set('key', 'new');
        $this->assertEquals('new', $config->get('key'));
    }

    public function test_set_with_nested_arrays(): void
    {
        $config = new Config;
        $config->set('nested', [
            'level1' => [
                'level2' => 'value',
            ],
        ]);

        $nested = $config->get('nested');
        $this->assertInstanceOf(ConfigInterface::class, $nested);
        $this->assertInstanceOf(ConfigInterface::class, $nested->level1);
        $this->assertEquals('value', $nested->level1->level2);
    }

    public function test_remove_non_existent_key(): void
    {
        $config = new Config;
        $config->remove('nonexistent');
        $this->assertFalse($config->has('nonexistent'));
        $this->assertCount(0, $config);
    }

    public function test_count_after_operations(): void
    {
        $config = new Config;
        $this->assertCount(0, $config);

        $config->set('key1', 'value1');
        $this->assertCount(1, $config);

        $config->set('key2', 'value2');
        $this->assertCount(2, $config);

        $config->remove('key1');
        $this->assertCount(1, $config);

        $config->remove('key2');
        $this->assertCount(0, $config);
    }

    public function test_all_with_deeply_nested_configs(): void
    {
        $config = new Config([
            'level1' => [
                'level2' => [
                    'level3' => 'value',
                ],
            ],
        ]);

        $all = $config->all();
        $this->assertIsArray($all);
        $this->assertIsArray($all['level1']);
        $this->assertIsArray($all['level1']['level2']);
        $this->assertEquals('value', $all['level1']['level2']['level3']);
    }

    public function test_iterator_functionality(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];
        $config = new Config($data);

        $iterated = [];
        foreach ($config as $key => $value) {
            $iterated[$key] = $value;
        }

        $this->assertEquals($data, $iterated);
    }

    public function test_iterator_aggregate(): void
    {
        $config = new Config(['a' => 1, 'b' => 2, 'c' => 3]);
        $iterator = $config->getIterator();

        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertCount(3, $iterator);
    }

    public function test_array_access_with_non_string_offset(): void
    {
        $config = new Config(['key' => 'value']);

        $this->assertFalse(isset($config[123]));
        $this->assertFalse(isset($config[null]));
        $this->assertNull($config[123]);
        $this->assertNull($config[null]);

        $config[123] = 'test';
        $this->assertFalse($config->has('123'));
        $this->assertFalse(isset($config[123]));

        unset($config[123]);
        $this->assertTrue($config->has('key'));
    }

    public function test_array_access_offset_set_without_offset(): void
    {
        $config = new Config;
        $config[] = 'value';
        $this->assertCount(0, $config);
    }

    public function test_property_access_with_nested_properties(): void
    {
        $config = new Config([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->assertEquals('localhost', $config->database->host);
        $config->database->host = 'remote';
        $this->assertEquals('remote', $config->database->host);
    }

    public function test_all_returns_proper_nested_structure(): void
    {
        $config = new Config([
            'simple' => 'value',
            'nested' => [
                'key' => 'nested_value',
                'deep' => [
                    'key' => 'deep_value',
                ],
            ],
        ]);

        $all = $config->all();
        $this->assertEquals('value', $all['simple']);
        $this->assertIsArray($all['nested']);
        $this->assertEquals('nested_value', $all['nested']['key']);
        $this->assertIsArray($all['nested']['deep']);
        $this->assertEquals('deep_value', $all['nested']['deep']['key']);
    }

    public function test_mix_preserves_existing_nested_configs(): void
    {
        $config = new Config([
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
            ],
        ]);

        $override = new Config([
            'app' => [
                'version' => '2.0.0',
            ],
        ]);

        $config->mix($override);
        $this->assertEquals('MyApp', $config->app->name);
        $this->assertEquals('2.0.0', $config->app->version);
    }

    public function test_empty_string_key(): void
    {
        $config = new Config(['' => 'empty_key_value']);
        $this->assertTrue($config->has(''));
        $this->assertEquals('empty_key_value', $config->get(''));
        $this->assertEquals('empty_key_value', $config['']);
        $this->assertEquals('empty_key_value', $config->{''});
    }

    public function test_numeric_string_keys(): void
    {
        $config = new Config([
            '0' => 'zero',
            '1' => 'one',
            '42' => 'forty-two',
        ]);

        $this->assertEquals('zero', $config->get('0'));
        $this->assertEquals('one', $config->get('1'));
        $this->assertEquals('forty-two', $config->get('42'));
    }

    public function test_special_characters_in_keys(): void
    {
        $config = new Config([
            'key-with-dash' => 'dash',
            'key_with_underscore' => 'underscore',
            'key.with.dot' => 'dot',
            'key with space' => 'space',
        ]);

        $this->assertEquals('dash', $config->get('key-with-dash'));
        $this->assertEquals('underscore', $config->get('key_with_underscore'));
        $this->assertEquals('dot', $config->get('key.with.dot'));
        $this->assertEquals('space', $config->get('key with space'));
    }

    public function test_null_values(): void
    {
        $config = new Config([
            'null_key' => null,
            'other_key' => 'value',
        ]);

        $this->assertTrue($config->has('null_key'));
        $this->assertNull($config->get('null_key'));
        $this->assertNull($config['null_key']);
        $this->assertNull($config->null_key);
    }

    public function test_boolean_values(): void
    {
        $config = new Config([
            'true_value' => true,
            'false_value' => false,
        ]);

        $this->assertTrue($config->get('true_value'));
        $this->assertFalse($config->get('false_value'));
        $this->assertIsBool($config->get('true_value'));
        $this->assertIsBool($config->get('false_value'));
    }

    public function test_integer_values(): void
    {
        $config = new Config([
            'positive' => 42,
            'negative' => -42,
            'zero' => 0,
        ]);

        $this->assertEquals(42, $config->get('positive'));
        $this->assertEquals(-42, $config->get('negative'));
        $this->assertEquals(0, $config->get('zero'));
        $this->assertIsInt($config->get('positive'));
    }

    public function test_float_values(): void
    {
        $config = new Config([
            'pi' => 3.14159,
            'negative' => -1.5,
            'zero' => 0.0,
        ]);

        $this->assertEquals(3.14159, $config->get('pi'));
        $this->assertEquals(-1.5, $config->get('negative'));
        $this->assertEquals(0.0, $config->get('zero'));
        $this->assertIsFloat($config->get('pi'));
    }

    public function test_empty_array_value(): void
    {
        $config = new Config(['empty_array' => []]);
        $empty = $config->get('empty_array');
        $this->assertInstanceOf(ConfigInterface::class, $empty);
        $this->assertCount(0, $empty);
        $this->assertEmpty($empty->all());
    }

    public function test_mixed_type_values(): void
    {
        $config = new Config([
            'string' => 'text',
            'integer' => 123,
            'float' => 45.67,
            'boolean' => true,
            'null' => null,
            'array' => ['nested' => 'value'],
        ]);

        $this->assertIsString($config->get('string'));
        $this->assertIsInt($config->get('integer'));
        $this->assertIsFloat($config->get('float'));
        $this->assertIsBool($config->get('boolean'));
        $this->assertNull($config->get('null'));
        $this->assertInstanceOf(ConfigInterface::class, $config->get('array'));
    }

    public function test_set_state_with_nested_arrays(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => [
                'nested' => 'nested_value',
            ],
        ];
        $config = Config::__set_state($data);

        $this->assertEquals('value1', $config->get('key1'));
        $this->assertInstanceOf(ConfigInterface::class, $config->get('key2'));
        $this->assertEquals('nested_value', $config->key2->nested);
    }

    public function test_set_state_with_empty_array(): void
    {
        $config = Config::__set_state([]);
        $this->assertCount(0, $config);
        $this->assertEmpty($config->all());
    }

    public function test_constructor_with_empty_array(): void
    {
        $config = new Config([]);
        $this->assertCount(0, $config);
        $this->assertEmpty($config->all());
    }

    public function test_constructor_with_no_arguments(): void
    {
        $config = new Config;
        $this->assertCount(0, $config);
        $this->assertEmpty($config->all());
    }
}
