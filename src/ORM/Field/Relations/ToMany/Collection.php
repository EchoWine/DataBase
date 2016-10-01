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
        $this -> getModel() -> add($value);
    }

    public function remove($value){
        $this -> getModel() -> remove($value);
    }

    public function save(){
        $this -> getModel() -> save();
    }

    public function sync($values){
        $this -> getModel() -> setValue($values);
        $this -> save();
    }
}