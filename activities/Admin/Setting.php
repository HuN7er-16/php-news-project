<?php

namespace Admin;

use database\DataBase;

class Setting extends Admin{

    public function index(){

        $db = new DataBase();
        $setting = $db->select('SELECT * FROM setting ORDER BY `id` ASC')->fetch();
        require_once (BASE_PATH . '/template/admin/setting/index.php');
    }
    
    public function edit($id){

        $db = new DataBase();
        $setting = $db->select('SELECT * FROM setting WHERE `id` = ?;', [$id])->fetch();
        require_once (BASE_PATH . '/template/admin/setting/edit.php');

    }

    public function update($request, $id){

        $db = new DataBase();
        $db->update('categories', $id, array_keys($request), $request);
        $this->redirect('admin/category');

    }

}