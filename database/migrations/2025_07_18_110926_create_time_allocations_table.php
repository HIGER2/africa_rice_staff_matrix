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
        Schema::create('time_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('employeeId');
            $table->foreign('employeeId')->references('employeeId')->on('employees')->onDelete('cascade');
            $table->integer('year')->nullable();
            $table->string('agreement')->nullable();
            $table->string('bus')->nullable();
            $table->integer('jan')->default(0);
            $table->integer('feb')->default(0);
            $table->integer('mar')->default(0);
            $table->integer('apr')->default(0);
            $table->integer('may')->default(0);
            $table->integer('jun')->default(0);
            $table->integer('jul')->default(0);
            $table->integer('aug')->default(0);
            $table->integer('sep')->default(0);
            $table->integer('oct')->default(0);
            $table->integer('nov')->default(0);
            $table->integer('dec')->default(0);
            $table->integer('total')->default(0);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_allocations');
    }
};
