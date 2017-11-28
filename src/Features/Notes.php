<?php

namespace Direct808\Vk\Features;

trait Notes
{
    public function notesAdd($parameters)
    {
        $result = $this->callMethod('notes.add', $parameters);
        return $result;
    }
}
