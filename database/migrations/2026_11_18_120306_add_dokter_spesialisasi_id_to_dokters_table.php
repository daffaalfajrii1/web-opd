<?php

use App\Models\Dokter;
use App\Models\DokterSpesialisasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->foreignId('dokter_spesialisasi_id')
                ->nullable()
                ->after('slug')
                ->constrained('dokter_spesialisasis')
                ->nullOnDelete();
        });

        if (Schema::hasTable('dokters')) {
            Dokter::query()->orderBy('id')->each(function (Dokter $dokter) {
                $name = trim((string) $dokter->specialty);
                if ($name === '') {
                    return;
                }

                $spesialisasi = DokterSpesialisasi::query()->firstOrCreate(
                    ['slug' => Str::slug($name) ?: 'spesialisasi-'.$dokter->id],
                    [
                        'name' => $name,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]
                );

                $dokter->forceFill([
                    'dokter_spesialisasi_id' => $spesialisasi->id,
                    'specialty' => $spesialisasi->name,
                ])->saveQuietly();
            });
        }
    }

    public function down(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dokter_spesialisasi_id');
        });
    }
};
