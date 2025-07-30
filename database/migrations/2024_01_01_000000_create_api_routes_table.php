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
        Schema::create('api_routes', function (Blueprint $table) {
            $table->id();
            $table->string('middleware');
            $table->string('service_group');
            $table->string('route_name');
            $table->string('controller_name');
            $table->string('method_name');
            $table->enum('http_method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->default('POST');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_routes');
    }
};
