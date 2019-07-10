<?php

namespace MBLSolutions\InspiredDeckLaravel\Model;

use Illuminate\Pagination\LengthAwarePaginator;
use MBLSolutions\InspiredDeck\Api\ApiResource;
use MBLSolutions\InspiredDeck\InspiredDeck;
use MBLSolutions\InspiredDeckLaravel\Model\Concerns\CanFake;
use MBLSolutions\InspiredDeckLaravel\Model\Concerns\HasAttributes;
use MBLSolutions\InspiredDeckLaravel\Pagination\InspiredDeckPagination;

abstract class InspiredDeckModel
{
    use HasAttributes, CanFake;

    /**
     * The Inspired Deck API Resource for the model
     *
     * @var string
     */
    protected $resource;

    /**
     * The API Resource
     *
     * @var ApiResource
     */
    private $apiResource;

    /**
     * Create a New Inspired Deck Modal Instance
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill attributes
     *
     * @param array $attributes
     * @return InspiredDeckModel
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Set the Inspired Deck API Resource associated
     * with the model
     *
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the Inspired Deck API Resource associated
     * with the model
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource ?? 'MBLSolutions\InspiredDeck\\' . substr(strrchr(get_called_class(), "\\"), 1);
    }

    /**
     * Get the Resource Object
     *
     * @return ApiResource
     */
    public function resource(): ApiResource
    {
        if ($this->apiResource === null) {
            $namespace = $this->getResource();

            $this->apiResource = new $namespace;
        }

        // If model is faking, mock response
        if (self::isFaking()) {
            InspiredDeck::setToken('fake_token');

            $this->mockExpectedHttpResponse();
        }

        return $this->apiResource;
    }

    /**
     * Get attributes from the model
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Set attribute on the model
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Check if an attribute exists on the model
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->hasAttribute($key);
    }

    /**
     * Unset an attribute on the model
     *
     * @param $key
     * @return void
     */
    public function __unset($key)
    {
        $this->unsetAttribute($key);
    }

    /**
     * Get all Models
     *
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function all(array $options = [])
    {
        $paginator = new InspiredDeckPagination($this->resource());

        return $paginator->paginate($options);
    }

    /**
     * Call a method on the resource
     *
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $result = $this->resource()->$name(...$arguments);

        if (isset($result['data'])) {

            if (isset($result['data'][0])) {
                $collection = collect();

                foreach ($result['data'] as $data) {
                    $collection->push(new static($data));
                }

                return $collection;
            } else {
                $this->fill($result['data']);

                return $this;
            }
        }

        return $result;
    }

    /**
     * Call a Static Method on Resource
     *
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        return (new static)->$name(...$arguments);
    }

    /**
     * Convert model attributes to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributesToArray();
    }

    /**
     * Convert model attributes to Json
     *
     * @return string
     */
    public function toJson()
    {
        return $this->attributesToJson();
    }

}