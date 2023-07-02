<?php

namespace Admin;

use database\DataBase;

class Post extends Admin{

    public function index(){

        $db = new DataBase();
        $posts = $db->select('SELECT * FROM posts ORDER BY `id` ASC');
        require_once (BASE_PATH . '/template/admin/posts/index.php');

    }

    public function create(){

        require_once (BASE_PATH . '/template/admin/categories/create.php');

    }

    public function store($request){

        $db = new DataBase();
        $db->insert('categories', array_keys($request), $request);
        $this->redirect('admin/post');

    }

    public function edit($id){

        $db = new DataBase();
        $Post = $db->select('SELECT * FROM categories WHERE `id` = ?;', [$id])->fetch();
        require_once (BASE_PATH . '/template/admin/categories/edit.php');

    }

    public function update($request, $id){

        $db = new DataBase();
        $db->update('categories', $id, array_keys($request), $request);
        $this->redirect('admin/post');

    }

    public function delete($id){

        $db = new DataBase();
        $db->delete('categories', $id);
        $this->redirect('admin/post');

    }

}