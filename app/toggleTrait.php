<?php

namespace App;



trait toggleTrait
{
    public function toggle(string $attribute = "is_active")
    {
        if (in_array($this->$attribute, [null, 0, 1])) {
            $this->update([$attribute => $this->$attribute == 1 ?  0 : 1]);
        }

        return $this;
    }
}
