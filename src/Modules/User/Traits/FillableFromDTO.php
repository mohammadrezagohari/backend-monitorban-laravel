<?php
trait FillableFromDTO
{
    public function setFillableFromDTO(object $dto): void
    {
        $this->fillable(array_keys(get_object_vars($dto)));
    }
}
