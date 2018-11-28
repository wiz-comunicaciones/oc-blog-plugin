<?php namespace Wiz\Blog\Models;

use Model;
use BackendAuth;
use Config;
use Wiz\Blog\Classes\AssetBlog as Blog;
use Wiz\Blog\Classes\AssetFacebookVideo as FbVideo;
use Wiz\Blog\Classes\AssetYouTube as YouTube;
use Wiz\Blog\Classes\AssetVimeo as Vimeo;
use ToughDeveloper\ImageResizer\Classes\Image;

class Tag extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;

    protected $dates = [
        'deleted_at',
    ];

    protected $guarded = [];

    public $rules = [
        'type'  => 'required|integer|min:1|max:6',
        'title' => 'required|string',
        'path'  => 'required_if:type,1',
        'url'   => 'required_if:type,3|url',
    ];

    public $attributeNames = [
        'type'  => 'Tipo',
        'title' => 'Título',
        'path'  => 'Descripción',
        'url'   => 'Enlace',
    ];

    public $table = 'wiz_blog_tags';

    public $belongsTo =[
        'author' => [
            'Backend\Models\User',
            'key' => 'author_id',
        ],
    ];

    public $morphTo = [
        'taggable' => []
    ];

    public $morphedByMany = [
        'posts' => [
            'Wiz\Blog\Models\Post',
            'name' => 'taggable',
            'table' => 'wiz_blog_taggables'
        ],
    ];

    public $attachOne = [
        'file' => [
            'System\Models\File',
            'public' => false,
            'delete' => true,
        ],
        'thumb' => [
            'System\Models\File',
            'delete' => true,
        ]
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

    public function scopeCategoriesOnly($query)
    {
        $query->where('is_category', 1);
    }

    public function scopeNoCategories($query)
    {
        $query->where('is_category', 0);
    }
}