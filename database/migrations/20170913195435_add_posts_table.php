<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPostsTable extends Migration
{
    public function up()
    {
        $this->schema->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->string('slug');
            $table->longText('body');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('posts');
    }
}
