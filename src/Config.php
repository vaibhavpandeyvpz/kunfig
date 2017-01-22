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
 * Class Config
 * @package Kunfig
 */
class Config extends ConfigAbstract
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * Config constructor.
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $values = array();
        foreach ($this->values as $key => $value) {
            if ($value instanceof ConfigInterface) {
                $values[$key] = $value->all();
            } else {
                $values[$key] = $value;
            }
        }
        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $fallback = null)
    {
        return $this->has($key) ? $this->values[$key] : $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function mix(ConfigInterface $config)
    {
        foreach ($config as $key => $value) {
            if ($this->has($key)) {
                $preset = $this->get($key);
                if (($preset instanceof ConfigInterface) && ($value instanceof ConfigInterface)) {
                    $preset->mix($value);
                    continue;
                }
            }
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->values[$key] = is_array($value) ? new self($value) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        unset($this->values[$key]);
    }

    // <editor-fold desc="Magic Methods">

    /**
     * @param array $data
     * @return static
     */
    public static function __set_state(array $data = array())
    {
        return new self($data);
    }

    // </editor-fold>
}
