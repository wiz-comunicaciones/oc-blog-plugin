<?php namespace Wiz\Blog\Components;

use Input;
use Flash;
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;
use Wiz\Blog\Models\Post as PostModel;

class SearchResults extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Resultado de búsqueda',
            'description' => 'Resultado de búsqueda de entradas.'
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
        $this->search = $this->page['search'] = $this->loadSearch();
        $this->posts = $this->page['posts'] = $this->loadPosts();
        $this->featuredPosts = $this->page['featuredPosts'] = $this->loadFeaturedPosts();
    }

    protected function loadSearch()
    {
        $search = Input::get('q', false);
        return $search;
    }

    protected function loadPosts()
    {
        $perPage = $this->property('postsPerPage');
        $page = $this->param('page') ? : 1;
        $search = Input::get('q', false);

        $posts = PostModel::published()
            ->search($search)
            ->addSelect('wiz_blog_posts.*')
            ->with('featured_image', 'categories_count', 'categories', 'comments')
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
}