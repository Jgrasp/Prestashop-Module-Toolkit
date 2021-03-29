<?php

namespace Jgrasp\Toolkit\Sql;

use Jgrasp\Toolkit\Sql\Factory\FieldFactory;
use Jgrasp\Toolkit\Sql\Factory\IntegerField;
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
        return array_key_exists('multishop', $this->getDefinition()) && $this->getDefinition()['multishop'];
    }

    public function isMultiLang(): bool
    {
        return array_key_exists('multishop', $this->getDefinition()) && $this->getDefinition()['multishop'];
    }

    public function getInstallSql(): string
    {
        $queries = [];

        $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->getTable().'` (';

        $query .= (new IntegerField($this->getPrimaryKey(), true))
            ->setSize(10)
            ->setUnsigned(true)
            ->getSql();

        foreach ($this->getFields() as $name => $row) {
            $row['name'] = $name;
            $query .= (FieldFactory::getByType($row))->getSql();
        }

        $query .= 'PRIMARY KEY ( `'.$this->getPrimaryKey().'`))
                  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $queries[] = $query;

        return implode('', $queries);
    }

    public static function getUninstallSql(ObjectModel $model): string
    {
        return (new self($model));
    }

}