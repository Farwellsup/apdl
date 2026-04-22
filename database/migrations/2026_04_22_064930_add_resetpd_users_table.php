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
        Schema::table('twill_users', function (Blueprint $table) {

            $table->string('reset_pd')->default(0);
            $table->string('policy_agree')->default(0);
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('twill_users', function (Blueprint $table) {
            //

            $table->dropColumn('reset_pd');
             $table->dropColumn('policy_agree');
        });
    }
};
