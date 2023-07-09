<?php

namespace Admin;

use database\DataBase;

class Menu extends Admin{

    public function index(){

        $db = new DataBase();
        $menues = $db->select('SELECT * FROM menues ORDER BY `id` ASC');
        require_once (BASE_PATH . '/template/admin/menues/index.php');

    }

    public function create(){

        $db = new DataBase();
        $menues = $db->select('SELECT * FROM menues WHERE parent_id IS NULL ORDER BY `id` ASC');
        require_once (BASE_PATH . '/template/admin/menues/create.php');

    }

    public function store($request){

        $db = new DataBase();
        $db->insert('menues', array_keys(array_filter($request)), array_filter($request));
        $this->redirect('admin/menu');

    }

    public function edit($id){

        $db = new DataBase();
        $category = $db->select('SELECT * FROM categories WHERE `id` = ?;', [$id])->fetch();
        require_once (BASE_PATH . '/template/admin/categories/edit.php');

    }

    public function update($request, $id){

        $db = new DataBase();
        $db->update('categories', $id, array_keys($request), $request);
        $this->redirect('admin/category');

    }

    public function delete($id){

        $db = new DataBase();
        $db->delete('categories', $id);
        $this->redirect('admin/category');

    }

}