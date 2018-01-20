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

use think\Db;

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

        $result = $this->model->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('Category/index'));
    }


    public function edit() 
    {
        $id = $this->request->param('id', 0, 'intval');
        $data     = $this->model->get($id)->toArray();
        $this->assign($data);
        return $this->fetch();
    }


    public function editPost()
    {
    	$data = $this->request->param();

        $result = $this->validate($data, 'FoodCategory');
        if ($result !== true) {
            $this->error($result);
        }

        $result = $this->model->editCategory($data);

        if ($result === false) {
            $this->error('编辑失败!');
        }

        $this->success('编辑成功!', url('Category/index'));
    }


    public function delete() 
    {
        $id = $this->request->param('id');
        $cate = $this->model->get($id)->toArray();
        if (empty($cate)) {
            $this->error('分类不存在!');
        }

        //判断此分类下是否有商品
        $c1 = Db::table('rc_food')->where('cat_id',$id)->count('1');
        if ($c1) {
            $this->error('此分类下有菜谱,不得删除!');
        }
        //删除分类
        $c2 = Db::table('rc_food_category')->where('id',$id)->delete();
        if ($c2) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
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
