<?php namespace Wiz\bLOG\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Inicio extends Controller
{
    public $requiredPermissions = [
        'wiz.blog::access_module'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wiz.Blog', 'wiz-blog');
    }

    public function index()
    {
        $this->pageTitle = 'Entradas';
    }
}