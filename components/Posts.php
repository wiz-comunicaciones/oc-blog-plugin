<?php namespace Wiz\Blog\Components;

use Cms\Classes\ComponentBase;
use Wiz\Blog\Models\Post as PostModel;

class Posts extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Todas las entradas',
            'description' => 'Muestra todas las entradas.'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsPerPage' => [
                'title'             => 'Cantidad de entradas por página',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'default'           => '6',
            ]
        ];
    }

    public function onRun()
    {
        $this->posts = $this->page['posts'] = $this->loadPosts();
        $this->featuredPosts = $this->page['featuredPosts'] = $this->loadFeaturedPosts();
        $this->footerPosts = $this->page['footerPosts'] = $this->loadFooterPosts();
    }

    protected function loadPosts()
    {
        $perPage = $this->property('postsPerPage');
        $page = $this->param('page') ? : 1;
        $posts = PostModel::published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, $page);
        return $posts;
    }

    protected function loadFeaturedPosts()
    {
        $featuredPosts = PostModel::published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();
        return $featuredPosts;
    }

    protected function loadFooterPosts()
    {
        $footerPosts = PostModel::orderBy('published_at', 'desc')
            ->take(4)
            ->get();
        return $footerPosts;
    }

}
