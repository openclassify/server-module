<?php namespace Visiosoft\ServerModule\Server;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var ServerModel
     */
    protected $model = ServerModel::class;


    /**
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [];
    }
}
