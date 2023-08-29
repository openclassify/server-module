<?php namespace Visiosoft\ServerModule\Server\Form;

class ServerFormFields
{
    public function handle(ServerFormBuilder $builder)
    {
        $builder->setFields([
            'name' => [
                'placeholder' => 'e.g. Superserver',
                'required' => true,
            ],
            'ip' => [
                'placeholder' => 'e.g. 123.45.67.89',
                'required' => true,
            ],
            'provider' => [
                'placeholder' => 'e.g. Digital Ocean',
                'required' => true,
            ],
            'location' => [
                'placeholder' => 'e.g. London',
                'required' => true,
            ],
            'domain' => [
                'required' => true,
                'placeholder' => 'e.g. domain.co',
                'label' => 'module::field.domain.name',
                'type' => 'anomaly.field_type.text'
            ],
        ]);

    }
}
