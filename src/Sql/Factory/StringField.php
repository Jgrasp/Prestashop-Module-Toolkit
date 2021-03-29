<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class StringField extends Field
{
    private $size = 255;

    /**
     * @param array $data
     * @return StringField
     * @throws \Exception
     */
    public static function buildFromArray(array $data): Field
    {
        $field = parent::buildFromArray($data);

        if (array_key_exists('size', $data)) {
            $field->setSize((int)$data['size']);
        }

        return $field;
    }


    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): int
    {
        $this->size = $size;

        return $this;
    }

    public function getTypeSql(): string
    {
        return $this->getType().'('.$this->getSize().')';
    }

}