<?php namespace Wiz\TrabajaGlobal\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class migration006 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumns('wiz_blog_categories', ['is_menu']))
            Schema::table('wiz_blog_categories', function($table) {
                $table->boolean('is_menu')->nullable();
            });
    }    
    public function down()
    {
        if (Schema::hasColumns('wiz_blog_categories', ['is_menu']))
            Schema::table('wiz_blog_categories', function($table) {
                $table->dropColumn('is_menu');
            });
    }
}