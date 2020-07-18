<?php

namespace Strappberry\EcwidApi\Classes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;

class UnderlayingDataArray implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'set')) {
            $propertyName = Str::camel(Str::substr($name, 3));
            return $this->setProperty($propertyName, $arguments[0]);
        }
    }

    public function __get($name)
    {
        return Arr::get($this->data(), $name);
    }

    protected function setProperty($name, $value)
    {
        Arr::set($this->data, $name, $value);

        return $this;
    }

    protected function addPropertyToArray($name, $value)
    {
        $current_values = Arr::get($this->data, $name, []);
        $current_values[] = $value;
        Arr::set($this->data, $name, $current_values);

        return $this;
    }

    public function toArray()
    {
        return $this->data();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function data()
    {
        return $this->data;
    }
}