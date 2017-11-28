<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];

        for ($i = 0; $i <= 10; $i++) {
            $data[] = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => password_hash($faker->password, PASSWORD_DEFAULT),
            ];
        }

        $this->insert('users', $data);

    }
}
