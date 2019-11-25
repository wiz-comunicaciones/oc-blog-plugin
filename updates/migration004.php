<?php namespace Wiz\Blog\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;
use DB;

class Migration004 extends Migration
{
    public function up()
    {
        Schema::create('wiz_blog_categories', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
            $table->integer('author_id')->nullable()->unsigned();
            $table->boolean('is_visible')->default(1)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('wiz_blog_categorizables', function($table) {
            $table->engine = 'InnoDB';
            $table->integer('category_id');
            $table->integer('categorizable_id');
            $table->string('categorizable_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wiz_blog_categories');
        Schema::dropIfExists('wiz_blog_categorizables');
    }
}