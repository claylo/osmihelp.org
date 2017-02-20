<?php

namespace Statamic\Addons\Integer;

use Statamic\Extend\Fieldtype;

class IntegerFieldtype extends Fieldtype
{
    public function process($data)
    {
        return (int) $data;
    }
}
