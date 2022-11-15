<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('due_date');
            $table->enum('status', Task::STATUS)->default(Task::STATUS_PENDING);
            $table->timestamps();

            $table->unsignedBigInteger('parent_id')->nullable()->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('tasks');

            // index for searching
            $table->index(['title', 'due_date']);
            $table->index(['title']);
            $table->index(['due_date']);
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
    }
};
