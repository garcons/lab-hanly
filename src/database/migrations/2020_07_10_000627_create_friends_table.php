<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();$table->string('nickname', 100)->collate('utf8mb4_general_ci')->comment('ニックネーム');
            $table->string('email', 100)->unique()->comment('メールアドレス');
            $table->string('password')->comment('パスワード');
            $table->string('image_path', 200)->nullable()->comment('画像パス');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "friends COMMENT '友だち'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
}
