<?php namespace Wiz\Blog\Models;

use Carbon\Carbon;
use Model;
use BackendAuth;
use Backend\Models\User as BackendUser;

class Comment extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Nullable;
    use \Wiz\Blog\Traits\Searchable;

    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    protected $nullable = [
        'email',
    ];

    protected $guarded = [];
    
    protected $searchable = [
        'name',
        'email',
        'content',
    ];

    public $rules = [
        'content' => 'required',
    ];

    public $morphTo = [
        'commentable' => []
    ];

    public $morphMany = [
        'comments' => [ 
            'Wiz\Blog\Models\Comment',
            'name' => 'commentable'
        ]
    ];

    public $belongsTo = [
        'author' => [
            'Backend\Models\User',
            'key' => 'id'
        ]
    ];

    public $table = 'wiz_blog_comments';

    public function beforeSave()
    {
        if ($this->name == null && $this->email == null){
            $user = BackendAuth::getUser();
            if ($user) {
                $this->name = $user->full_name;
                $this->email = $user->email;
            }
        }
    }

    public function scopeIsVisible ($query)
    {
        $query->where(function($q){
            $q->where('is_visible', 1);
        });        
    }

}