<?php

namespace MBLSolutions\InspiredDeckLaravel\Model\Concerns;

trait HasAttributes
{
    /**
     * Model Attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Get Attribute
     *
     * @param $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set attribute
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Check if attribute exists
     *
     * @param $key
     * @return bool
     */
    public function hasAttribute($key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Unset attribute
     *
     * @param $key
     */
    public function unsetAttribute($key)
    {
        if ($this->hasAttribute($key)) {
            unset($this->attributes[$key]);
        }
    }

    /**
     * Convert Attributes to Array
     *
     * @return array
     */
    protected function attributesToArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert Attributes to JSON
     *
     * @return string
     */
    protected function attributesToJson(): string
    {
        return json_encode($this->attributesToArray());
    }

}