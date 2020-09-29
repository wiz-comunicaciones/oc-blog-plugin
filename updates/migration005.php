<?php namespace Wiz\TrabajaGlobal\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class migration205 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumns('wiz_blog_posts', ['is_featured']))
            Schema::table('wiz_blog_posts', function($table) {
                $table->boolean('is_featured')->nullable();
            });
    }    
    public function down()
    {
        if (Schema::hasColumns('wiz_blog_posts', ['is_featured']))
            Schema::table('wiz_blog_posts', function($table) {
                $table->dropColumn('is_featured');
            });
    }
}