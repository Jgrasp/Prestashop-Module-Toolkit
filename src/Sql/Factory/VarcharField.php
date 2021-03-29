<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class VarcharField extends StringField
{
    public function getType(): string
    {
        return 'VARCHAR';
    }
}