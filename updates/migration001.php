<?php namespace Wiz\Blog\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;
use DB;

class Migration001 extends Migration
{
    public function up()
    {
        Schema::create('wiz_blog_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('name')->nullable();
            $table->text('slug')->nullable();
            $table->text('url')->nullable();
            $table->text('lead')->nullable();
            $table->string('medium')->nullable();
            $table->text('content')->nullable();
            $table->integer('author_id')->nullable()->unsigned();
            $table->boolean('is_visible')->nullable()->default(0);
            $table->boolean('is_external')->nullable()->default(0);
            $table->integer('user_id')->nullable()->unsigned();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::statement('ALTER TABLE `wiz_blog_posts` ADD FULLTEXT `ft_index` (`name`, `lead`, `content`, `medium`)');

        Schema::create('wiz_blog_related_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('related_id')->unsigned();
            $table->unique(['post_id', 'related_id']);
        });

        Schema::create('wiz_blog_tags', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_visible')->default(1)->nullable();
            $table->boolean('is_category')->default(0)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('wiz_blog_taggables', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('tag_id');
            $table->integer('taggable_id');
            $table->string('taggable_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wiz_blog_taggables');
        Schema::dropIfExists('wiz_blog_tags');
        Schema::dropIfExists('wiz_blog_related_posts');
        Schema::dropIfExists('wiz_blog_posts');
    }
}