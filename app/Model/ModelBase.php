<?php

namespace Model;

abstract class ModelBase
{
    public function toObj() : object
    {
        return (object) this;
    }

    public abstract static function FromArray(array $obj);
}

?>