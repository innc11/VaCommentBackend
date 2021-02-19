<?php

namespace Model;

class UserModel extends ModelBase
{
    public $name; // string
    public $mail; // string
    public $website; // string

    public function __construct(string $name, string $mail='', string $website='')
    {
        $this->name = $name;
        $this->mail = $mail;
        $this->website = $website;
    }

    public static function FromArray(array $obj)
    {
        return new UserModel($obj['name'], $obj['mail'], $obj['website']);
    }
}

?>