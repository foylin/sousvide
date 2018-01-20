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
namespace app\api\controller;

use cmf\controller\HomeBaseController;
use app\food\model\FoodModel;
use app\food\model\FoodCategoryModel;
use think\Validate;
use think\Db;

class FoodController extends ApiBaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new FoodModel();
        $this->categoryModel = new FoodCategoryModel();
    }


    public function index()
    {
        return $this->fetch(':index');
        // return '非法访问!!!';
    }


    /**
     * 菜谱分类
     */
    public function get_catelist()
    {
        $where['delete_time'] = 0;
        $where['is_show'] = 1;
        $data = Db::name('food_category')->where($where)->field('id,name')->select();
        $this->ajaxBack(true,'success',0,$data);
    }


    /**
     * 菜谱分类
     */
    public function food_cate_detail()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('catid', 0, 'intval');
            if ($id) {
                $data = $this->model->food_cate_detail($id);
                $this->ajaxBack(true,'success',0,$data);
            } else {
                $this->ajaxBack(false,'请求错误',-1);
            }
        } else {
            $this->ajaxBack(false,'请求错误',-1);
        }
    }

    /**
     * 菜谱详情
     */
    public function food_detail()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id', 0, 'intval');
            if ($id) {
                $data = $this->model->food_detail($id);
                $this->ajaxBack(true,'success',0,$data);
            } else {
                $this->ajaxBack(false,'请求错误',-1);
            }
        } else {
            $this->ajaxBack(false,'请求错误',-1);
        }
    }


    
}
