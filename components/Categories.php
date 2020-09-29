<?php namespace Wiz\Blog\Components;

use Cms\Classes\ComponentBase;
use Wiz\Blog\Models\Category as CategoryModel;

class Categories extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Todas las categorías',
            'description' => 'Muestra todas las categorías.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->navCategories = $this->page['navCategories'] = $this->loadNavCategories();
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadNavCategories()
    {
        $navCategories = CategoryModel::published()
            ->menu()
            ->orderBy('title', 'asc')
            ->get();
        return $navCategories;
    }

    protected function loadCategories()
    {
        $categories = CategoryModel::published()
            ->orderBy('title', 'asc')
            ->get();
        return $categories;
    }

}
