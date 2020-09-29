<?php namespace Wiz\Blog;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = [
        'ToughDeveloper.ImageResizer',
    ];

    public function pluginDetails()
    {
        return [
            'name' => 'Blog',
            'description' => 'Permite la gestión de entradas y categorías',
            'author' => 'Wiz Comunicaciones',
            'icon' => 'oc-icon-book',
            'iconSvg' =>  'plugins/wiz/blog/assets/images/plugin-icon.svg',
            'homepage' => 'https://github.com/wiz-comunicaciones/plugin-blog'
        ];
    }

    public function boot()
    {

    }
    
    public function registerNavigation()
    {
        return [
            'wiz-blog' => [
                'label'       => 'Blog',
                'url'         => Backend::url('wiz/blog/inicio'),
                'icon'        => 'icon-newspaper-o',
                'iconSvg'     => 'plugins/wiz/blog/assets/images/plugin-icon.svg',
                'order'       => 200,
                'permissions' => ['wiz.blog::access_module'],

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'Entradas',
                        'url'         => Backend::url('wiz/blog/posts'),
                        'icon'        => 'icon-newspaper-o',
                        'permissions' => ['wiz.blog::manage_posts']
                    ],
                    'categories' => [
                        'label'       => 'Categorías',
                        'url'         => Backend::url('wiz/blog/categories'),
                        'icon'        => 'icon-list-ul',
                        'permissions' => ['wiz.blog::manage_categories']
                    ],
                    'multimedia' => [
                        'label'       => 'Multimedia',
                        'url'         => Backend::url('wiz/blog/multimedia'),
                        'icon'        => 'icon-youtube-play',
                        'permissions' => ['wiz.blog.manage_multimedia']
                    ],
                    'comments' => [
                        'label'       => 'Comentarios',
                        'url'         => Backend::url('wiz/blog/comments'),
                        'icon'        => 'icon-comment-o',
                        'permissions' => ['wiz.blog::manage_comments']
                    ],
                ]

            ]
        ];
    }

    public function registerComponents()
    {
        return [
            \Wiz\Blog\Components\Categories::class => 'Categories',
            \Wiz\Blog\Components\Category::class => 'Category',
            \Wiz\Blog\Components\Posts::class => 'Posts',
            \Wiz\Blog\Components\Post::class => 'Post',
            \Wiz\Blog\Components\Search::class => 'Search',
            \Wiz\Blog\Components\SearchResults::class => 'SearchResults',
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'wiz.blog::mail.commentsform' => 'Send comment form submission info'
        ];
    }

    public function registerListColumnTypes()
    {
        return [
            'wiz_blog_image'           => ['Wiz\Blog\Classes\ListColumnTypes', 'imageColumnType'],
            'wiz_blog_translate_id'    => ['Wiz\Blog\Classes\ListColumnTypes', 'translateIdColumnType'],
            'wiz_blog_url'             => ['Wiz\Blog\Classes\ListColumnTypes', 'urlColumnType'],
            'wiz_blog_html'            => ['Wiz\Blog\Classes\ListColumnTypes', 'htmlColumnType'],
            'wiz_blog_attachmentcount' => ['Wiz\Blog\Classes\ListColumnTypes', 'attachmentCountColumnType'],
            'wiz_blog_number'          => ['Wiz\Blog\Classes\ListColumnTypes', 'numberColumnType'],
            'wiz_blog_translate_opts'  => ['Wiz\Blog\Classes\ListColumnTypes', 'optionsColumnType'],
        ];
    }
}
