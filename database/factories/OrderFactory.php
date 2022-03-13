<?php


namespace Rubik\NotificationManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'user_id' => User::factory()->create()->id,
            'approved' => $this->faker->boolean,
        ];
    }
}

