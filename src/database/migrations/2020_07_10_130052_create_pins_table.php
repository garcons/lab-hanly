<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('friends_id')->unique()->comment('フレンドID');
            $table->double('latitude', 9, 7)->comment('緯度');
            $table->double('longitude', 10, 7)->comment('経度');
            $table->timestamps();

            $table->index('friends_id');

            $table->foreign('friends_id')
                  ->references('id')
                  ->on('friends')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE " . DB::getTablePrefix() . "pins COMMENT 'ピン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pins');
    }
}
