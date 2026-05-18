<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('user_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('user_packages', 'expiration_notified')) {
                $table->boolean('expiration_notified')->default(false);
            }
        });
    }
    public function down(): void {
        Schema::table('user_packages', function (Blueprint $table) {
            if (Schema::hasColumn('user_packages', 'expiration_notified')) {
                $table->dropColumn('expiration_notified');
            }
        });
    }
};
