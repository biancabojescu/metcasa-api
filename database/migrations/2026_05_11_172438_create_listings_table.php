<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('deal_type'); // Vanzare | Inchiriere
            $table->string('property_type'); // Apartament | Casa | Teren | Comercial
            $table->unsignedSmallInteger('rooms')->nullable();
            $table->decimal('surface', 8, 2);
            $table->string('floor', 16)->nullable();
            $table->decimal('price', 12, 2);
            $table->json('images')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('active'); // active | inactive | sold
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deal_type', 'property_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
