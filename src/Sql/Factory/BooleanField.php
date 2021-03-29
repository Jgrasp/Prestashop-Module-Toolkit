<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class BooleanField extends NumericField
{
    public function getType(): string
    {
        return 'TINYINT';
    }

    public function getSize(): ?int
    {
        return 1;
    }

    public function getTypeSql(): string
    {
        return $this->getType().'('.$this->getSize().')';
    }
}