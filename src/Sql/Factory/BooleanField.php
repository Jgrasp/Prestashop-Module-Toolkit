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
}