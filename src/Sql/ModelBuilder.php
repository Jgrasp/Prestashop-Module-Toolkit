<?php

namespace Jgrasp\Toolkit\Sql;

use Jgrasp\Toolkit\Sql\Factory\FieldFactory;
use Jgrasp\Toolkit\Sql\Factory\IntegerField;
use Jgrasp\Toolkit\Sql\Factory\PrimaryField;
use ObjectModel;

class ModelBuilder
{
    private $model;

    public function __construct(string $classname)
    {
        $this->model = new \ReflectionClass($classname);

        if (!$this->model->isSubclassOf(ObjectModel::class)) {
            throw new \Exception($classname.' should be an instance of ObjectModel');
        }

        $definition = $this->getDefinition();

        if (!array_key_exists('table', $definition) || !is_string($definition['table'])) {
            throw new \Exception("Field 'table' is not valid in ObjectModel ".$classname);
        }


        if (!array_key_exists('fields', $definition) || !is_array($definition['fields'])) {
            throw new \Exception("Field 'fields' is not valid in ObjectModel ".$classname);
        }
    }

    public function getDefinition(): array
    {
        return $this->model->getStaticPropertyValue('definition');
    }

    public function getTable(): string
    {
        if (!array_key_exists('table', $this->getDefinition())) {
            throw new \Exception("Field 'table' does not exist in ".get_class($this->model));
        }

        return $this->getDefinition()['table'];
    }

    public function getPrestashopTable(): string
    {
        return _DB_PREFIX_.$this->getTable();
    }

    public function getPrimaryKey(): string
    {
        if (!array_key_exists('primary', $this->getDefinition())) {
            throw new \Exception("Field 'primary' does not exist in ".get_class($this->model));
        }

        return $this->getDefinition()['primary'];
    }

    public function getFields(): array
    {
        if (!array_key_exists('fields', $this->getDefinition())) {
            throw new \Exception("Field 'fields' does not exist in ".get_class($this->model));
        }

        return $this->getDefinition()['fields'];
    }

    public function isMultiShop(): bool
    {
        return array_key_exists('multilang_shop', $this->getDefinition()) && $this->getDefinition()['multilang_shop'];
    }

    public function isMultiLang(): bool
    {
        return array_key_exists('multilang', $this->getDefinition()) && $this->getDefinition()['multilang'];
    }

    public function getInstallSql(): string
    {
        $queries = [];

        //Main table

        $query = 'CREATE TABLE IF NOT EXISTS `'.$this->getPrestashopTable().'` (';

        $query .= (new PrimaryField($this->getPrimaryKey()))->getSql();

        $fields = $this->getFields();

        if ($this->isMultiLang()) {
            $fields = array_filter($fields, function ($field) {
                return !isset($field['lang']) || $field['lang'] !== true;
            });
        }

        foreach ($fields as $name => $row) {
            $row['name'] = $name;
            $query .= (FieldFactory::getByType($row))->getSql();
        }

        $query .= 'PRIMARY KEY ( `'.$this->getPrimaryKey().'`))
                  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $queries[] = $query;

        //Lang table

        if ($this->isMultiLang()) {
            $query = 'CREATE TABLE IF NOT EXISTS `'.$this->getPrestashopTable().'_lang` (';

            $query .= (new IntegerField($this->getPrimaryKey(), true))
                ->setSize(10)
                ->setUnsigned(true)
                ->getSql();

            $query .= (new IntegerField('id_lang', true))
                ->setSize(10)
                ->setUnsigned(true)
                ->getSql();

            $query .= (new IntegerField('id_shop', true))
                ->setSize(10)
                ->setUnsigned(true)
                ->getSql();

            $fields = array_filter($this->getFields(), function ($field) {
                return isset($field['lang']) && $field['lang'] == true;
            });

            foreach ($fields as $name => $row) {
                $row['name'] = $name;
                $query .= (FieldFactory::getByType($row))->getSql();
            }

            $query .= 'PRIMARY KEY ( `'.$this->getPrimaryKey().'`,`id_lang`, `id_shop`))
                  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

            $queries[] = $query;
        }

        if ($this->isMultiShop()) {
            $query = 'CREATE TABLE IF NOT EXISTS `'.$this->getPrestashopTable().'_shop` (';

            $query .= (new IntegerField($this->getPrimaryKey(), true))
                ->setSize(10)
                ->setUnsigned(true)
                ->getSql();

            $query .= (new IntegerField('id_shop', true))
                ->setSize(10)
                ->setUnsigned(true)
                ->getSql();

            $fields = array_filter($this->getFields(), function ($field) {
                return isset($field['shop']) && $field['shop'] == true;
            });

            foreach ($fields as $name => $row) {
                $row['name'] = $name;
                $query .= (FieldFactory::getByType($row))->getSql();
            }

            $query .= 'PRIMARY KEY ( `'.$this->getPrimaryKey().'`, `id_shop`))
                  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

            $queries[] = $query;
        }

        return implode('', $queries);
    }

    public function getUninstallSql(): string
    {
        $queries = [];

        $queries[] = 'DROP TABLE '.$this->getPrestashopTable().';';
        $queries[] = 'DROP TABLE '.$this->getPrestashopTable().'_lang;';
        $queries[] = 'DROP TABLE '.$this->getPrestashopTable().'_shop;';


        return implode('', $queries);
    }

}