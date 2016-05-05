<?php

/**
 * Created by PhpStorm.
 * User: vytautas
 * Date: 04.05.2016
 * Time: 21:51
 */
class APP_Page
{
    var $isConfig;
    var $isList;

    var $belongsTo = false;

    var $table;
    var $url;
    var $name;
    var $title;
    var $dir;
    var $orderby = "id";
    
    var $languages;
    
    var $listFields = array();
    var $configFields = array();
}

class APP_Field{

    static $SYSTEM_FIELDS = ['id', 'created', 'updated', 'createdby', 'updatedby'];

    static $TYPE_INT = "int";
    static $TYPE_TEXT = "text";
    static $TYPE_PASSWORD = "password";
    static $TYPE_TEXTAREA = "textarea";
    static $TYPE_WYSIWYG = "wysiwyg";
    static $TYPE_BOOLEAN = "boolean";
    static $TYPE_DATETIME = "datetime";
    static $TYPE_DATE = "date";
    static $TYPE_IMAGE = "image";

    var $name; //same as db name
    var $type;
    var $title;
    var $description;

    var $visibleInList;
    var $visibleInEdit;

    var $defaultValue;

    var $languages;

    static function generateName($name = "name", $title="Name", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_TEXT;
        $field->title = $title;
        $field->description = "Navn";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateText($name = "text", $title="Text", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_TEXT;
        $field->title = $title;
        $field->description = "Text";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateInt($name = "int", $title="Int", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_INT;
        $field->title = $title;
        $field->description = "Int";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateBoolean($name = "boolean", $title="Boolean", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_BOOLEAN;
        $field->title = $title;
        $field->description = "Boolean";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateTextarea($name = "textarea", $title="Textarea", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_TEXTAREA;
        $field->title = $title;
        $field->description = "Textarea";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateDatetime($name = "datetime", $title="Datetime", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_DATETIME;
        $field->title = $title;
        $field->description = "Datetime";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }

    static function generateImage($name = "image", $title="Image", $isVisibleInList = true, $isVisibleInEdit = true){
        $field = new APP_Field();
        $field->name = $name;
        $field->type = APP_Field::$TYPE_IMAGE;
        $field->title = $title;
        $field->description = "Image";
        $field->visibleInList = $isVisibleInList;
        $field->visibleInEdit = $isVisibleInEdit;
        return $field;
    }
}