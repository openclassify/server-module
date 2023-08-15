<?php namespace Visiosoft\ServerModule\Server\Form;

use Anomaly\Streams\Platform\Support\Str;

class ServerFormHandler
{
    public function handle(ServerFormBuilder $builder)
    {
        if (!$builder->canSave()) {
            return;
        }

        $builder->saveForm();
        $entry = $builder->getFormEntry();

        $entry->setAttribute('server_id', Str::uuid()); // Auto Generated
        $entry->setAttribute('password', Str::random(24)); // Auto Generated
        $entry->setAttribute('database', Str::random(24)); // Auto Generated
        $entry->setAttribute('cron', ' '); // Auto Generated
        $entry->save();
    }
}
