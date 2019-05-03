<?php namespace Wiz\Blog\Models;

use Carbon\Carbon;
use Model;
use BackendAuth;
use Config;
use Wiz\Blog\Classes\AssetAnchorFm as AnchorFm;
use Wiz\Blog\Classes\AssetWebsite as Website;
use Wiz\Blog\Classes\AssetFacebookVideo as FbVideo;
use Wiz\Blog\Classes\AssetYouTube as YouTube;
use Wiz\Blog\Classes\AssetVimeo as Vimeo;
use ToughDeveloper\ImageResizer\Classes\Image;

class Asset extends Model
{
    use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;

    protected $nullable = [
        'description',
        'path',
        'url',
    ];

    protected $guarded = [];

    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    public $rules = [
        'type'  => 'required|integer|min:1|max:7',
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

    public $table = 'wiz_blog_assets';

    public $belongsTo =[
        'author' => [
            'Backend\Models\User',
            'key' => 'author_id',
        ],
    ];

    public $morphToMany = [
        'tags' => [
            'Wiz\Blog\Models\Tag',
            'name' => 'taggable'
        ],
    ];

    public $morphedByMany = [
        'posts'  => [
            'Wiz\Blog\Models\Post',
            'name' => 'assetable',
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

    public function scopeTestimonial($query)
    {
        $query->where(function($q){
            $q->where('is_visible', 1);
            $q->where('is_testimonial', 1);
            $q->whereDate('published_at', '<=', Carbon::now());
        });
    }

    public static function getTypeOptions()
    {
        return [
            4 => 'Video en YouTube',
            5 => 'Video en Vimeo',
            6 => 'Video en Facebook',
            3 => 'Enlace (sitio web)',
            1 => 'Archivo público',
            2 => 'Archivo privado',
            7 => 'Podcast en Anchor.fm',
        ];
    }

    public function getTypeOptionName()
    {
        $options = $this->getTypeOptions();
        return $options[$this->type];
    }

    public function getLink()
    {
        switch($this->type){
            case 1:
                $link = url(Config::get('cms.storage.media.path', 'media') . $this->path);
                break;
            case 2:
                $link = $this->file->getPath();
                break;
            default:
                $link = $this->url;
                break;
        }
        return $link;
    }

    public function getBackgroundImageUrl($width = 600)
    {
        if(!is_null($this->thumb)){
            $image = new Image($this->thumb->getPath());
            $img = $image->resize($width, false, []);
        } else {
            switch($this->type){
                case 3:
                    $img = Website::preview($this->url, ['width' => $width], [], true);
                    break;
                case 4:
                    $img = Youtube::preview($this->url, ['width' => $width], true);
                    break;
                case 5:
                    $img = Vimeo::preview($this->url, ['width' => $width], true);
                    break;
                case 6:
                    $img = FbVideo::preview($this->url);
                    break;
                case 7:
                    $img = AnchorFm::preview($this->url);
                    break;
                case 1:
                    $image = new Image(url(Config::get('cms.storage.media.path')) . $this->path);
                    $img = $image->resize($width, false, []);
                    break;
                case 2:
                    $image = new Image($this->file->getPath());
                    $img = $image->resize($width, false, []);
                    break;
                default:
                    return false;
            }
        }
        return $img;
    }

    public function getOutput($width = 568)
    {
        switch($this->type){
            case 3:
                $out = Website::preview($this->url, ['width' => $width], [], true);
                break;
            case 4:
                $out = YouTube::get($this->url, ['enablejsapi' => 1], ['width' => $width, 'height' => round(($width == '100%' ? 720 : $width)*.5625)]);
                break;
            case 5:
                $out = Vimeo::get($this->url, [], ['width' => $width]);
                break;
            case 6:
                $out = FbVideo::get($this->url);
                break;
            case 7:
                $out = AnchorFm::get($this->url, ['width' => $width]);
                break;
            case 1:
                $image = new Image($this->path);
                $out = $image->resize($width, false, []);
                break;
            case 2:
                $image = new Image($this->file->getPath());
                $out = $image->resize($width, false, []);
                break;
            default:
                return false;
        }
        return $out;
    }
}