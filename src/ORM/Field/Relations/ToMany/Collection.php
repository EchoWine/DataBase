<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\Component\Collection as BaseCollection;

class Collection extends BaseCollection{

    /**
     * The model field
     *
     * @var
     */
    protected $model;

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

    /**
     * Add a model
     *
     * @param ORM\Model $value
     */
    public function add($value){


        $add = $value;

        $schema = $this -> getModel() -> getSchema();


        if($collection = $schema -> getCollection()){

            $relation = $schema -> getRelation();
            $ob = new $relation();
            $ob -> {$collection} = $value;
            
            $add = $ob; 
        }  

        if(!$this -> has($value))
            $this -> getModel() -> add($add);
    }

    public function remove($value){

        $this -> getModel() -> remove($value);
    }

    public function has($value){

        $schema = $this -> getModel() -> getSchema();

        if($collection = $schema -> getCollection()){

            $relation = $schema -> getRelation();
            $ob = new $relation();
            $ob -> {$collection} = $value;
            $field = $relation::schema() -> getFieldByColumn($schema -> getReference());
            $ob -> {$field -> getName()} = $this -> getModel() -> getObjectModel();
            $value = $ob; 

            
            foreach($this as $k){
                if($k -> {$collection} -> equalTo($value -> {$collection}) && $k -> {$field -> getName()} == $value -> {$field -> getName()}){
                    return true;
                }
            }

            return false;
        }   

        foreach($this as $k){
            if($k -> equalTo($value)){
                return true;
            }
        }

        return false;
    }

    public function all(){

        $schema = $this -> getModel() -> getSchema();

        if($collection = $schema -> getCollection()){
            $return = [];
            foreach($this as $k){
                $return[] = $k -> {$collection};
            }

            return $return;
        }   


        return $this;
    }

    public function save(){
        $this -> getModel() -> save();
    }

    public function sync($values){
        $this -> getModel() -> setValue($values);
        $this -> save();
    }

    public function __toString(){
        $collection = [];

        foreach($this as $k){
            $collection[] = $k -> toArray();
        }

        return json_encode($collection);
    }
}