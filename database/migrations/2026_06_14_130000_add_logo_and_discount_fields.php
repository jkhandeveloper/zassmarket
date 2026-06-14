<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_stores', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price_cents');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('vendor_stores', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
