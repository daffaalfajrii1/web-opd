<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('nav_menu_id')
                ->nullable()
                ->after('menu_group')
                ->constrained('nav_menus')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nav_menu_id');
        });
    }
};
