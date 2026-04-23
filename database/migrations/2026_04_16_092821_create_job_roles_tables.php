<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_roles', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);
            
            // feel free to modify the name of this column, but title is supported by default (you would need to specify the name of the column Twill should consider as your "title" column in your module controller if you change it)
            $table->string('title', 200)->nullable();
            $table->unsignedBigInteger('company_id')->nullable(); 
            $table->json('department_id')->nullable(); 
            $table->integer('position')->unsigned()->nullable();
            
            // add those 2 columns to enable publication timeframe fields (you can use publish_start_date only if you don't need to provide the ability to specify an end date)
            // $table->timestamp('publish_start_date')->nullable();
            // $table->timestamp('publish_end_date')->nullable();
        });

        Schema::create('job_role_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'job_role');
        });

        Schema::create('job_role_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'job_role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_role_revisions');
        Schema::dropIfExists('job_role_slugs');
        Schema::dropIfExists('job_roles');
    }
};
