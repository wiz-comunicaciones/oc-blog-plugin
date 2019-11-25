<?php namespace Wiz\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Categories extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];
    
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'wiz.blog::manage_categories'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wiz.Blog', 'wiz-blog', 'categories');
    }
}