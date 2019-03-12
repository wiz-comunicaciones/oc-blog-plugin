<?php namespace Wiz\Blog\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;
use DB;

class Migration002 extends Migration
{
    public function up()
    {
        Schema::create('wiz_blog_comments', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('name')->nullable();
            $table->text('email')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_visible')->nullable()->default(0);
            $table->integer('commentable_id')->unsigned();
            $table->text('commentable_type')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wiz_blog_comments');
    }

}