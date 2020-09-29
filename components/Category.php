<?php namespace Wiz\Blog\Components;

use Cms\Classes\ComponentBase;
use Wiz\Blog\Models\Post as PostModel;
use Wiz\Blog\Models\Category as CategoryModel;

class Category extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Entradas por categoría',
            'description' => 'Muestra todas las entradas pertenecientes a una categoría.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Identificador de la categoría',
                'description' => 'Se buscará la categoría utilizando el identificador',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
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
        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->loadPosts();
        $this->featuredPosts = $this->page['featuredPosts'] = $this->loadFeaturedPosts();
    }

    protected function loadCategory()
    {
        $slug = $this->property('slug');
        $category = CategoryModel::where('slug', $slug)
            ->first();
        return $category;
    }

    protected function loadPosts()
    {
        $slug = $this->property('slug');
        $perPage = $this->property('postsPerPage');
        $page = $this->param('page') ? : 1;
        $posts = PostModel::published()
            ->whereHas('categories', function($query) use ($slug) {
                $query->where('slug', $slug);
            })
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
