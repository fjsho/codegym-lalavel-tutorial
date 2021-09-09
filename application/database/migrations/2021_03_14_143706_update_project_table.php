<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->boolean('exist')->nullable()
            ->storedAs('CASE WHEN deleted_at IS NULL THEN 1 ELSE null END');
            $table->unique(['key', 'exist']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['key', 'exist']);
            $table->dropColumn('exist');
            $table->unique(['key']);
        });
    }
}
