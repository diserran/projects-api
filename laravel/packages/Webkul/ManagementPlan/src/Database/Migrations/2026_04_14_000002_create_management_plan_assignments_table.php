<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('management_plan_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('management_plan_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('management_plan_id')->references('id')->on('management_plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['management_plan_id', 'user_id', 'start_date'], 'mp_assignments_unique_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_plan_assignments', function (Blueprint $table) {
            $table->dropForeign(['management_plan_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('management_plan_assignments');
    }
};
