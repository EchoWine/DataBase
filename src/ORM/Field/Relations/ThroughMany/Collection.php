<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ThroughMany;

use CoreWine\Component\Collection as BaseCollection;

class Collection extends BaseCollection{

    /**
     * The model field
     *
     * @var
     */
    protected $model;

    protected $persist_stack;

    public function __construct($array){
        parent::__construct($array);
        $this -> persist_stack = new BaseCollection();
        $this -> persist_stack['save'] = new BaseCollection();
        $this -> persist_stack['delete'] = new BaseCollection();
    }

    /**
     * Set model field
     *
     * @param $model
     */
    public function setModel($model){
        $this -> model = $model;
    }

    /**
     * Get the model field
     *
     * @return Model
     */
    public function getModel(){
        return $this -> model;
    }


    public function addPersistStack($operation,$model){
        $this -> persist_stack[$operation][] = $model;
    }

    public function removePersistStack($operation,$model){
        $this -> persist_stack[$operation] -> remove($model);
    }

    public function persistStack($operation){

        foreach($this -> persist_stack[$operation] as $k){
            $k -> {$operation}();
        }
    }

    public function checkInstanceValueClass($value){
        $this -> getModel() -> checkInstanceValueClass($value);
    }

    /**
     * Retrieve index
     *
     * @param ORM\Model $value
     *
     * @return int
     */
    public function index($value){
        $this -> checkInstanceValueClass($value);

        foreach($this -> items as $n => $k){
            if($k -> equalTo($value)){
                return $n;
            }
        }

        return false;
    }

    /**
     * Has a model ?
     *
     * @param ORM\Model $value
     *
     * @return boolean
     */
    public function has($value){
        $this -> checkInstanceValueClass($value);

        return $this -> index($value) !== false ? true : false;
    }


    /**
     * Add a model
     *
     * @param ORM\Model $value
     */
    public function add($value){

        $this -> checkInstanceValueClass($value);

        $model = $this -> getModel();
        $resolver = $model -> getSchema() -> getResolver();

        $this[] = $value;


        if(!$value -> pivot){

            $m = $resolver -> mid -> model;
            $pivot = new $m();

            $value -> pivot = $pivot;
        }

        $value -> pivot -> {$resolver -> mid -> field_to_start -> getName()} = $model -> getObjectModel();

        $value -> pivot -> {$resolver -> mid -> field_to_end -> getName()} = $value;

        $this -> addPersistStack('save',$value -> pivot);
        $this -> removePersistStack('delete',$value -> pivot);
    }
    

    /**
     * Remove a model
     *
     * @param ORM\Model $value
     */
    public function remove($value){

        $this -> checkInstanceValueClass($value);

        $index = $this -> index($value);

        if($index !== false){

            $this -> unset($index);

            $this -> addPersistStack('delete',$value -> pivot);
            $this -> removePersistStack('save',$value -> pivot);
        }else{

            throw new \Exception("Not found: ".$value);
        }


    }

    /**
     * Return all models
     *
     * @return this
     */
    public function all(){
        return $this;
    }

    /**
     * Save all
     */
    public function save(){
        $this -> persistStack('delete');
        $this -> persistStack('save');
    }

    /**
     * Sync
     */
    public function sync($values){
        
        foreach($values as $value){
            $this -> add($value);
        }

        $this -> save();
    }

    public function __toString(){
        $collection = [];

        foreach($this -> items as $k){
            $collection[] = $k -> toArray();
        }

        return json_encode($collection);
    }
}