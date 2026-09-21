<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('db-translations.table', 'translations'), function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->default('*')->index();
            $table->text('key');
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('db-translations.table', 'translations'));
    }
};
