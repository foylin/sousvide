<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\food\controller;

use cmf\controller\AdminBaseController;
use app\food\model\FoodCategoryModel;

class CategoryController extends AdminBaseController
{

	public function _initialize()
    {
        parent::_initialize();
        $this->model = new FoodCategoryModel();
    }

    public function index()
    {
    	$param = $this->request->param();
    	$data = $this->model->adminFoodCategoryList($param);
    	$this->assign('data', $data->items());
        $this->assign('page', $data->render());
        $this->assign('param', $param);

        return $this->fetch('index');
    }


    public function add() 
    {
    	return $this->fetch('add');
    }


    public function addPost() 
    {
    	$data = $this->request->param();

        $result = $this->validate($data, 'FoodCategory');
        if ($result !== true) {
            $this->error($result);
        }

        $result = $this->model->addEditCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('Category/index'));
    }


    public function editPost()
    {
    	$data = $this->request->param();

        $result = $this->validate($data, 'FoodCategory');
        if ($result !== true) {
            $this->error($result);
        }

        $result = $this->model->addEditCategory($data);

        if ($result === false) {
            $this->error('编辑失败!');
        }

        $this->success('编辑成功!', url('Category/index'));
    }


    public function statusUpdate()
    {
        if(parent::fieldUpdate($this->model)){
            $this->success("操作成功！",url('index'));
        }else{
            $this->success("操作失败！",url('index'));
        }
    }

}
