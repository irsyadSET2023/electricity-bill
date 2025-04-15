<?php

use App\Enums\BuildingType;
use App\Enums\State;
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
        Schema::create('buildings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->enum('building_type', array_column(BuildingType::cases(), 'value'));
            $table->enum('state', array_column(State::cases(), 'value'));
            $table->foreignUlid('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
