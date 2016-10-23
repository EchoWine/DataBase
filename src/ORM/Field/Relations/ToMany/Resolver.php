<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

class Resolver{

    public $start;
    public $end;

    public function __construct($start,$end){


        # Set start
        $this -> start = new ResolverAbstract($start);

        # End
        $this -> end = new ResolverAbstract($end);
    }
}