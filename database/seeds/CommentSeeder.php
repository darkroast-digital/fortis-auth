<?php

use Phinx\Seed\AbstractSeed;

class CommentSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];

        for ($i = 0; $i <= 1000; $i++) {
            $data[] = [
                'post_id' => $faker->numberBetween(1, 25),
                'user_id' => $faker->numberBetween(1, 10),
                'body' => $faker->text,
            ];
        }

        $this->insert('comments', $data);

    }
}
