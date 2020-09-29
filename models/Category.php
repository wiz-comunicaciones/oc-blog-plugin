<?php namespace Wiz\Blog\Models;

use Model;
use BackendAuth;
use Config;

class Category extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;

    protected $dates = [
        'deleted_at',
    ];

    protected $guarded = [];

    public $rules = [
        'title' => 'required|string',
    ];

    public $attributeNames = [
        'title' => 'Título',
    ];

    public $table = 'wiz_blog_categories';

    public $belongsTo =[
        'author' => [
            'Backend\Models\User',
            'key' => 'author_id',
        ],
    ];

    public $morphTo = [
        'categorizable' => []
    ];

    public $morphedByMany = [
        'posts' => [
            'Wiz\Blog\Models\Post',
            'name' => 'categorizable',
            'table' => 'wiz_blog_categorizables'
        ],
    ];

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();

        if ($user)
            $this->author_id = $user->id;
    }

    public function scopePublished($query)
    {
        $query->where('is_visible', 1);
    }

    public function scopeMenu($query)
    {
        $query->where('is_menu', 1);
    }
}