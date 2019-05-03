<?php namespace Wiz\Blog\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;
use DB;

class Migration003 extends Migration
{
    public function up()
    {
        Schema::create('wiz_blog_assets', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->smallInteger('type')->nullable()->unsigned();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('path')->nullable();
            $table->text('url')->nullable();
            $table->integer('author_id')->nullable()->unsigned();
            $table->boolean('is_visible')->default(1)->nullable();
            $table->boolean('is_testimonial')->default(0)->nullable();
            $table->boolean('is_blog')->default(0)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('wiz_blog_assetables', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('asset_id');
            $table->integer('assetable_id');
            $table->string('assetable_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wiz_blog_assetables');
        Schema::dropIfExists('wiz_blog_assets');
    }
}