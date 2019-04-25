<?php

namespace Artisan\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class Settings
{
    protected $defaults = [];

    protected $casts = [];

    protected $model;

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function set($attribute, $value)
    {
        $this->model->properties()->updateOrCreate([
            'key' => $attribute,
        ], [
            'value' => $value,
        ]);
    }

    public function get($attribute, $default = null)
    {
        return $this
            ->model
            ->properties()
            ->where('key', $attribute)
            ->value('value') ?? $default ?? Arr::get($this->defaults, $attribute);
    }

    public function all()
    {
        $settings = [];

        collect($this->defaults)->map(function ($value, $key) {
            return new Fluent(compact('key', 'value'));
        })->merge(
            $this->model->properties
        )->each(function ($property) use (&$settings) {
            Arr::set($settings, $property->key, $property->value);
        });

        return $settings;
    }
}