<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendsRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends_relationships', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('own_friends_id')->comment('自分のフレンドID');
            $table->unsignedBigInteger('other_friends_id')->comment('友だちのフレンドID');
            $table->timestamps();

            $table->unique(['own_friends_id', 'other_friends_id']);
            $table->index('own_friends_id');

            $table->foreign('own_friends_id')
                  ->references('id')
                  ->on('friends')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('other_friends_id')
                  ->references('id')
                  ->on('friends')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "friends_relationships COMMENT '友だち関係'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends_relationships');
    }
}
