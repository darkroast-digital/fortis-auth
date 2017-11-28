<?php

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];

        for ($i = 0; $i <= 10; $i++) {
            $data[] = [
                'name' => $faker->sentence($nb = 5, $variableNbWords = true),
                'user_id' => $faker->numberBetween(1, 10),
                'slug' => $faker->slug,
                'body' => $faker->text,
            ];
        }

        $this->insert('posts', $data);

    }
}
