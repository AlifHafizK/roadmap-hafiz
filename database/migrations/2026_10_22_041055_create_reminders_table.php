<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('remind_at');
            $table->dateTime('notified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'remind_at',
                'notified_at',
            ]);
        });
    }
};
