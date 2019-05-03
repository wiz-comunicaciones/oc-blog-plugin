<?php namespace Wiz\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Multimedia extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'wiz.blog.manage_multimedia'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wiz.Blog', 'blog', 'multimedia');
    }
}