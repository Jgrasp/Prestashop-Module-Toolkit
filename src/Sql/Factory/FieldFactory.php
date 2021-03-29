<?php


namespace Jgrasp\Toolkit\Sql\Factory;


use ObjectModel;
use PHPUnit\Runner\Exception;

class FieldFactory
{
    public static function getByType(array $data): Field
    {
        if (!array_key_exists('type', $data) || empty($data['type'])) {
            throw new \Exception('Type value is missing');
        }

        switch ($data['type']) {
            case ObjectModel::TYPE_INT:
                return IntegerField::buildFromArray($data);

            case ObjectModel::TYPE_BOOL:
                return BooleanField::buildFromArray($data);

            case ObjectModel::TYPE_STRING:
                return VarcharField::buildFromArray($data);

            case ObjectModel::TYPE_FLOAT:
                return FloatField::buildFromArray($data);

            case ObjectModel::TYPE_DATE:
                return DateTimeField::buildFromArray($data);

            case ObjectModel::TYPE_HTML:
                return HtmlField::buildFromArray($data);
            default:
                throw new Exception('Type '.$data['type'].' is not supported.');
        }
    }
}