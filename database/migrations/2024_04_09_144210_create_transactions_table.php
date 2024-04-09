<?php

use App\Models\Asset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 10);
            $table->dateTime('date');
            $table->foreignIdFor(Asset::class, 'source_asset_id')
                ->nullable()
                ->constrained('assets');
            $table->foreignIdFor(Asset::class, 'destination_asset_id')
                ->nullable()
                ->constrained('assets');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
