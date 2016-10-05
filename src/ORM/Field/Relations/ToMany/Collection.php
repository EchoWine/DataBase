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

    public function add($value){


        $schema = $this -> getModel() -> getSchema();

        if($collection = $schema -> getCollection()){

            $relation = $schema -> getRelation();
            $ob = new $relation();
            $ob -> {$collection} = $value;
            
            $value = $ob; 
        }   

        $this -> getModel() -> add($value);
    }

    public function remove($value){


        $schema = $this -> getModel() -> getSchema();

        if($collection = $schema -> getCollection()){

            $relation = $schema -> getRelation();
            $ob = new $relation();
            $ob -> {$collection} = $value;

            $field = $relation::schema() -> getFieldByColumn($schema -> getReference());
            $ob -> {$field -> getName()} = $this -> getModel() -> getModel();
            $value = $ob; 
            $this -> getModel() -> delete($value);
            return true;
        }   

        $this -> getModel() -> remove($value);
    }

    public function has($value){
        return parent::has($value);
    }

    public function save(){
        $this -> getModel() -> save();
    }

    public function sync($values){
        $this -> getModel() -> setValue($values);
        $this -> save();
    }
}