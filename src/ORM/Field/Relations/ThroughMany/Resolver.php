<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ThroughMany;

class Resolver{

    public $start;
    public $mid;
    public $end;

    public function __construct($start,$mid,$end){


        # Set start
        $this -> start = new ResolverAbstract($start);
        
        # Mid
        $this -> mid = new ResolverAbstract($mid);

        # End
        $this -> end = new ResolverAbstract($end);
    }
}