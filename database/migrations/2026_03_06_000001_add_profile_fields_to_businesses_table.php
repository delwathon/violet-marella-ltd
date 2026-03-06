<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('description');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->string('rc_number', 120)->nullable()->after('address');
            $table->string('website')->nullable()->after('rc_number');
            $table->string('tax_id', 120)->nullable()->after('website');
            $table->string('contact_person', 255)->nullable()->after('tax_id');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name',
                'phone',
                'email',
                'address',
                'rc_number',
                'website',
                'tax_id',
                'contact_person',
            ]);
        });
    }
};
