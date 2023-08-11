<?php namespace Visiosoft\ServerModule\Server\Form;

class ServerFormFields
{
    public function handle(ServerFormBuilder $builder)
    {
        $builder->setFields([
            'name' => [
                'placeholder' => 'e.g. Superserver',
            ],
            'ip' => [
                'placeholder' => 'e.g. 123.45.67.89'
            ],
            'provider' => [
                'placeholder' => 'e.g. Digital Ocean',
            ],
            'location' => [
                'placeholder' => 'e.g. London'
            ],
        ]);

    }
}
