<?php

class App_Model {
    /**
     * @var HashMap with all the fields and their datatype.
     */

    public static $FIELD_TYPES = [
        "id"=>"INT",
        "image" => "VARCHAR(245)",
        "name" => "VARCHAR(245)", //Same as text only displayed in lists
        "text" => "VARCHAR(455)",
        "number" => "INT", // Same as int only displayed in lists
        "int" => "INT",
        "switch" => "TINYINT", // Same as bool only displayed in lists
        "bool" => "TINYINT",
        "date" => "DATE",
        "textarea" => "TEXT",
        "wysiwyg" => "TEXT"
    ];

    public $table;
    public $fields;
    public $values;
    public $defaultValues;
    public $names;
    public $title;
    public $dir;

    private $log;

    function App_Model() {
        $this->log = Logger::getLogger("com.dalisra.model");
    }

}