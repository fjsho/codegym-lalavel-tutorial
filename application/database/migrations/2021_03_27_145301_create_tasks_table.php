<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_resolutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('name');
            $table->foreignId('task_kind_id')->constrained('task_kinds');
            $table->foreignId('task_status_id')->constrained('task_statuses');
            $table->foreignId('created_user_id')->constrained('users');
            $table->foreignId('updated_user_id')->nullable()->constrained('users');
            $table->foreignId('assigner_id')->nullable()->constrained('users');
            $table->foreignId('task_category_id')->nullable()->constrained('task_categories');
            $table->date('due_date')->nullable();
            $table->foreignId('task_resolution_id')->nullable()->constrained('task_resolutions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_kinds');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('task_categories');
        Schema::dropIfExists('task_resolutions');
    }
}
