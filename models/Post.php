<?php namespace Wiz\Blog\Models;

use Carbon\Carbon;
use Model;
use BackendAuth;
use Backend\Models\User as BackendUser;

class Post extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\Purgeable;
    use \Wiz\Blog\Traits\Searchable;

    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    protected $nullable = [
        'url',
        'lead',
        'content',
        'medium',
    ];

    protected $searchable = [
        'name',
        'lead',
        'content',
        'medium',
    ];

    protected $purgeable = [
        'default_multimedia',
        'wme_multimedia'
    ];    

    public $attributes = [
        'default_multimedia' => '',
        'wme_multimedia' => ''
    ];

    protected $guarded = [];

    public $rules = [
        'published_at' => 'required',
        'name' => 'required',
        'medium' => 'required_if:is_external,1',
        'url' => 'required_if:is_external,1',
        'lead' => 'required_if:is_external,0',
        'content' => 'required_if:is_external,0',
    ];

    public $table = 'wiz_blog_posts';

    public $belongsTo = [
        'author' => [
            'Backend\Models\User',
            'key' => 'author_id'
        ]
    ];

    public $belongsToMany = [
        'posts' => [
            'Wiz\Blog\Models\Post',
            'table' => 'wiz_blog_related_posts',
            'key' => 'post_id',
            'otherKey' => 'related_id'
        ],
        'posts_count' => [
            'Wiz\Blog\Models\Post',
            'table' => 'wiz_blog_related_posts',
            'key' => 'post_id',
            'otherKey' => 'related_id',
            'count' => true
        ]
    ];

    public $morphMany = [
        'comments' => [ 
            'Wiz\Blog\Models\Comment',
            'name' => 'commentable',
            'scope' => 'isVisible'
        ]
    ];

    public $morphToMany = [
        'tags' => [
            'Wiz\Blog\Models\Tag',
            'name' => 'taggable',
            'table' => 'wiz_blog_taggables',
            'conditions' => 'is_category=0'
        ],
        'tags_count' => [
            'Wiz\Blog\Models\Tag',
            'name' => 'taggable',
            'table' => 'wiz_blog_taggables',
            'conditions' => 'is_category=0',
            'count' => true,
        ],
        'categories' => [
            'Wiz\Blog\Models\Category',
            'name' => 'categorizable',
            'table' => 'wiz_blog_categorizables',
            'conditions' => 'is_visible=1'
        ],
        'categories_count' => [
            'Wiz\Blog\Models\Category',
            'name' => 'categorizable',
            'table' => 'wiz_blog_categorizables',
            'conditions' => 'is_visible=1',
            'count' => true
        ],
        'assets' => [
            'Wiz\Blog\Models\Asset',
            'name' => 'assetable',
            'table' => 'wiz_blog_assetables',
            'conditions' => 'is_blog=1'
        ],
    ];

    public $attachOne = [
        'featured_image' => [
            'System\Models\File',
            'delete' => true
        ],
    ];

    public function beforeCreate()
    {
        $user = BackendAuth::getUser();
        if ($user) {
            $this->author_id = $user->id;
        }
    }

    public function beforeValidate()
    {
        if(empty($this->published_at))
            $this->published_at = Carbon::now();
    }

    public function scopePublished($query)
    {
        $query->where(function($q){
            $q->where('is_visible', 1);
            $q->whereDate('published_at', '<=', Carbon::now());
        });
    }

    public function scopeInternal($query)
    {
        $query->where(function($q){
            $q->where('is_external', 0);
        });
    }

    public function scopeExternal($query)
    {
        $query->where(function($q){
            $q->where('is_external', 1);
        });
    }

    public function scopeFeatured($query)
    {
        $query->where(function($q){
            $q->where('is_featured', 1);
        });
    }

    /**
     * Apply a constraint to the query to find the nearest sibling
     *
     *     // Get the next post
     *     Post::applySibling()->first();
     *
     *     // Get the previous post
     *     Post::applySibling(-1)->first();
     *
     *     // Get the previous post, ordered by the ID attribute instead
     *     Post::applySibling(['direction' => -1, 'attribute' => 'id'])->first();
     *
     * @param       $query
     * @param array $options
     *
     * @return
     */
    public function scopeApplySibling($query, $options = [])
    {
        if (!is_array($options)) {
            $options = ['direction' => $options];
        }

        extract(array_merge([
            'direction' => 'next',
            'attribute' => 'published_at',
        ], $options));

        $isPrevious = in_array($direction, ['previous', -1]);
        $directionOrder = $isPrevious ? 'asc' : 'desc';
        $directionOperator = $isPrevious ? '>' : '<';

        return $query
            ->where('id', '<>', $this->id)
            ->whereDate($attribute, $directionOperator, $this->$attribute)
            ->orderBy($attribute, $directionOrder)
            ;
    }

    public function getAuthorOptions()
    {
        $out = [];
        $users = BackendUser::select('id', 'first_name', 'last_name')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        foreach($users as $user)
            $out[$user->id] = $user->last_name . ', ' . $user->first_name;

        return $out;
    }

    public function related($amount = 2)
    {
        $related = [];

        # Try Related
        if($this->posts->count() > 0){
            $posts = $this->posts()
                ->published()
                ->take($amount)
                ->get();
            foreach($posts as $post)
                $related[] = $post;
        }

        # Try Categories
        if (count($related) < $amount) {
            $categories = $this->categories->pluck('id');
            $posts = Post::published()
                ->where('id', '<>', $this->id)
                ->whereHas('categories', function($query) use ($categories){
                     $query->whereIn('id', $categories);
                })
                ->take($amount - count($related))
                ->orderBy('published_at', 'desc')
                ->get();
            foreach($posts as $post)
                $related[] = $post;
        }

        # Try Tags
        if (count($related) < $amount) {
            $tags = $this->tags->pluck('id');
            $posts = Post::published()
                ->where('id', '<>', $this->id)
                ->whereHas('tags', function($query) use ($tags){
                    $query->whereIn('id', $tags);
                })
                ->take($amount - count($related))
                ->orderBy('published_at', 'desc')
                ->get();
            foreach($posts as $post)
                $related[] = $post;
        }

        # Try Next
        if (count($related) < $amount) {
            $next = Post::published()
                ->applySibling()
                ->first();

            if($next)
                $related[] = $next;
        }

        # Try Prev
        if (count($related) < $amount) {
            $prev = Post::published()
                ->applySibling(-1)
                ->first();

            if($prev)
                $related[] = $prev;
        }

        return $related;
    }

    public function isExternal()
    {
        return (bool) $this->is_external;
    }

    public function afterFetch()
    {
        $this->default_multimedia = $this->insertMultimediaPost($this);
    }

    public function insertMultimediaPost($post)
    {
        $this->wme_multimedia = $post->content;
        $multimedia_span = $post->assets;
        $multimedia_default = [];
        $assets_cont = count($post->assets);
        $cont = 0;
        $matches = preg_match_all('/id="wme_/', $post->content);
        if(preg_match('/id="wme_/', $post->content)){
            if ($matches > $assets_cont)
                $top_assets = $assets_cont;
            else
                $top_assets = $matches;
            for( $i=0 ;$i<$top_assets; $i++ ){
                $cont++;
                $text = '/<p id="wme_' . $cont . '">/';
                $span_full = '<div class="post_video">' . $multimedia_span[$i]->getOutPut('100%') . '</div>';
                $this->wme_multimedia = preg_replace($text, $span_full, $this->wme_multimedia);
            }
            if($assets_cont > $matches) {
                for($i=$matches;$i<$assets_cont;$i++){
                    $multimedia_default[$i] = $multimedia_span[$i];
                }
            }
        } elseif(!is_null($post->assets)) {
            $multimedia_default = $multimedia_span;
        }
        return $multimedia_default;

    }
}