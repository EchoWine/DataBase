<?php

namespace CoreWine\DataBase\ORM\Field\Collection;

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

}