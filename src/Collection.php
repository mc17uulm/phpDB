<?php

namespace phpDB;

class Collection
{

    /**
     * @var array
     */
    private array $list;

    /**
     * Collection constructor.
     * @param array $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @param mixed $value
     * @param string | int $key
     * @return Collection
     */
    public function add($value, $key = null) : Collection
    {
        if(is_null($key)) {
            array_push($this->list, $value);
        } else {
            $this->list[$key] = $value;
        }
        return $this;
    }

    /**
     * @param string | int $key
     * @return mixed
     * @throws CollectionException
     */
    public function get($key) {
        if(!isset($this->list[$key])) {
            throw new CollectionException("Key ($key) not in collection");
        }
        return $this->list[$key];
    }

    /**
     * @param string | int $key
     * @return Collection
     * @throws CollectionException
     */
    public function remove($key) : Collection
    {
        if(!isset($this->list[$key])) {
            throw new CollectionException("Key ($key) is not in collection");
        }
        unset($this->list[$key]);
        return $this;
    }

    /**
     * @param string | int $key
     * @return bool
     */
    public function exists($key) : bool
    {
        return isset($this->list[$key]);
    }

    /**
     * @return array<string | int>
     */
    public function keys() : array
    {
        return array_keys($this->list);
    }

    /**
     * @return int
     */
    public function length() : int
    {
        return count($this->list);
    }

    /**
     * @param callable $callable
     * @return Collection
     */
    public function map(callable $callable) : Collection
    {
        return new Collection(array_map($callable, $this->list));
    }

    /**
     * @param callable $callable
     * @return Collection
     */
    public function filter(callable $callable) : Collection
    {
        return new Collection(array_filter($this->list, $callable));
    }

    /**
     * @param array $array
     * @return array
     */
    private static function array_flatten(array $array) : array {
        $return = array();
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $return = array_merge($return, self::array_flatten($value));
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    /**
     * @return Collection
     */
    public function flatten() : Collection
    {
        return new Collection(self::array_flatten($this->list));
    }

    /**
     * @param callable $callable
     * @return Collection
     */
    public function flat_map(callable $callable) : Collection
    {
        return $this->map($callable)->flatten();
    }

    /**
     * @return array
     */
    public function to_array() : array
    {
        return $this->list;
    }

    /**
     * @param callable $callable
     * @param bool $stop
     * @return Collection | mixed
     */
    public function search(callable $callable, bool $stop = false) : Collection
    {
        $result = new Collection();
        foreach($this->list as $key => $item)
        {
            if($callable($key, $item))  {
                if($stop) {
                    return $item;
                }
                $result->add($item, $key);
            }
        }
        return $result;
    }

    /**
     * @param callable $callable
     * @return mixed
     */
    public function search_first(callable $callable)
    {
        return $this->search($callable, true);
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function merge(Collection $collection) : Collection
    {
        return new Collection(array_merge($this->list, $collection->to_array()));
    }

    public function walk(callable $callable) : void
    {

    }

}