<?php namespace Wiz\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Posts extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
    ];
    
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'wiz.blog::manage_posts'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wiz.Blog', 'wiz-blog');
    }
}