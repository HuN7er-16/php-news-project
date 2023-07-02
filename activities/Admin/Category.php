<?php

namespace Admin;

use database\DataBase;

class Category extends Admin{

    public function index(){

        $db = new DataBase();
        $categories = $db->select('SELECT * FROM categories ORDER BY `id` ASC');
        require_once (BASE_PATH . '/template/admin/categories/index.php');

    }

    public function create(){

        require_once (BASE_PATH . '/template/admin/categories/create.php');

    }

    public function store($request){

        $db = new DataBase();
        $db->insert('categories', array_keys($request), $request);
        $this->redirect('admin/category');

    }
}