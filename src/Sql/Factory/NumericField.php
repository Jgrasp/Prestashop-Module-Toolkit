<?php


namespace Jgrasp\Toolkit\Sql\Factory;

class NumericField extends Field
{
    private $size;

    private $unsigned;

    /**
     * @param array $data
     * @return NumericField
     * @throws \Exception
     */
    public static function buildFromArray(array $data): Field
    {
        $field = parent::buildFromArray($data);

        if (array_key_exists('size', $data)) {
            $field->setSize((int)$data['size']);
        }

        if (array_key_exists('unsigned', $data)) {
            $field->setUnsigned((bool)$data['size']);
        }


        return $field;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function setSize(int $size): NumericField
    {
        if ($size <= 0) {
            throw new \Exception('Field\'s size could not be negative or equals 0');
        }

        $this->size = $size;
        return $this;
    }

    public function setUnsigned(bool $unsigned): NumericField
    {
        $this->unsigned = $unsigned;
        return $this;
    }

    public function getTypeSql(): string
    {
        $type = $this->getType().'('.$this->getSize().')';

        if ($this->isUnsigned()) {
            $type .= ' UNSIGNED';
        }

        return $type;
    }
}