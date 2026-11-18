<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('specialty');
            $table->string('email')->nullable()->after('phone');
            $table->string('facebook_url')->nullable()->after('email');
            $table->string('poli')->nullable()->after('facebook_url');
        });
    }

    public function down(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'facebook_url', 'poli']);
        });
    }
};
