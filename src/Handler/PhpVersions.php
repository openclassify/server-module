<?php

namespace Visiosoft\ServerModule\Handler;

use Anomaly\SelectFieldType\SelectFieldType;

class PhpVersions
{
    public function handle(SelectFieldType $fieldType)
    {
        $fieldType->setOptions([
            'php7.4',
            'php8.0',
            'php8.1',
            'php8.2',
        ]);
    }
}
