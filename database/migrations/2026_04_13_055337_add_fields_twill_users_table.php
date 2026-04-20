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
            $table->string('payroll_number')->after('name')->nullable();
            $table->string('username')->after('password')->nullable();
            $table->string('first_name')->after('username')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone_number')->after('last_name')->nullable();
            $table->timestamp('email_verified_at')->nullable();
          
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name')->nullable();

            $table->unsignedBigInteger('job_role_id')->nullable();
            $table->string('job_role_name')->nullable();
         
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_name')->nullable();

            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('unit_name')->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('country_name')->nullable();

            $table->integer('gender_id')->nullable();
            $table->string('profile_pic')->default(0);
            $table->string('initial_profile')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('twill_users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'first_name',
                'last_name',
                'phone_number',
                'email_verified_at',
                'is_admin',
                'is_company_admin',
                'company_id',
                'company_name',
                'department_id',
                'department_name',
                'unit_id',
                'unit_name',
                'gender_id',
                'profile_pic',
                'country_id',
                'country_name',
                'initial_profile',
                'secondary_email',
                'user_number'
            ]);
        });
    }
};
