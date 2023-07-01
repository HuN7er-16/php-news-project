<?php

namespace Admin;

class Category extends Admin{

    public function index(){

        require_once (BASE_PATH . '/template/admin/categories/index.php');

    }

}