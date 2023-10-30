<?php

namespace Visiosoft\ServerModule\Handler;

use Anomaly\SelectFieldType\SelectFieldType;

class PhpVersions
{
    public function handle(SelectFieldType $fieldType)
    {
        $fieldType->setOptions([
            '7.4' => 'PHP 7.4',
            '8.0' => 'PHP 8.0',
            '8.1' => 'PHP 8.1',
            '8.2' => 'PHP 8.2',
        ]);
    }
}
