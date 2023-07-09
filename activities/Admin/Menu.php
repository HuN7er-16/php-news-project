<?php

namespace Admin;

use database\DataBase;

class Menu extends Admin{

    public function index(){

        $db = new DataBase();
        $menues = $db->select('SELECT m1.*,m2.name AS parent_name FROM menues m1 LEFT JOIN menues m2 ON m1.parent_id = m2.id ORDER BY `id` ASC');
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
        $menu = $db->select('SELECT * FROM menues WHERE `id` = ?;', [$id])->fetch();
        $menues = $db->select('SELECT * FROM menues WHERE parent_id IS NULL;');
        require_once (BASE_PATH . '/template/admin/menues/edit.php');

    }

    public function update($request, $id){

        $db = new DataBase();
        $db->update('menues', $id, array_keys($request), $request);
        $this->redirect('admin/menu');

    }

    public function delete($id){

        $db = new DataBase();
        $db->delete('menues', $id);
        $this->redirect('admin/menu');

    }

}