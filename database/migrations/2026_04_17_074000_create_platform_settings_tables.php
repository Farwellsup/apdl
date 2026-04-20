<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            // this will create an id, a "published" column, and soft delete and timestamps columns
            createDefaultTableFields($table);
            
            // feel free to modify the name of this column, but title is supported by default (you would need to specify the name of the column Twill should consider as your "title" column in your module controller if you change it)
            $table->string('title', 200)->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('third_color')->nullable();
            $table->string('company_code')->nullable();
            $table->string('primary_text_color')->nullable();
            $table->string('secondary_text_color')->nullable();
            $table->string('third_text_color')->nullable();
            $table->string('primary_heading_color')->nullable();
            $table->string('secondary_heading_color')->nullable();
            $table->string('third_heading_color')->nullable();
            $table->string('menu_color')->nullable();
            $table->string('menu_active_color')->nullable();
            $table->string('button_primary_color')->nullable();
            $table->string('button_secondary_color')->nullable();
            $table->string('button_third_color')->nullable();
            $table->string('footer_color')->nullable();
            $table->string('theme_css_path')->nullable();
            $table->timestamp('theme_updated_at')->nullable();
            $table->integer('position')->unsigned()->nullable();

            
            // add those 2 columns to enable publication timeframe fields (you can use publish_start_date only if you don't need to provide the ability to specify an end date)
            // $table->timestamp('publish_start_date')->nullable();
            // $table->timestamp('publish_end_date')->nullable();
        });

        Schema::create('platform_setting_slugs', function (Blueprint $table) {
            createDefaultSlugsTableFields($table, 'platform_setting');
        });

        Schema::create('platform_setting_revisions', function (Blueprint $table) {
            createDefaultRevisionsTableFields($table, 'platform_setting');
        });
    }

    public function down()
    {
        Schema::dropIfExists('platform_setting_revisions');
        Schema::dropIfExists('platform_setting_slugs');
        Schema::dropIfExists('platform_settings');
    }
};
