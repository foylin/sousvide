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
use app\food\model\FoodModel;
use app\food\model\FoodCategoryModel;

use think\Db;

class IndexController extends AdminBaseController
{
	public function _initialize()
    {
        parent::_initialize();
        $this->model = new FoodModel();
        $this->categoryModel = new FoodCategoryModel();
    }

    public function index()
    {
    	$param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');

        $data        = $this->model->adminFoodList($param);
        /*dump($this->model->getLastSql());
        dump($data->items());exit;*/
        $data->appends($param);

        $categoryTree        = $this->categoryModel->adminCategoryTree($categoryId);

        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('foods', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());

        return $this->fetch();
    }


    public function add()
    {
    	$cates = $this->categoryModel->adminCategoryTree(0,0);
    	$this->assign('cates', $cates);

    	return $this->fetch('add');
    }


    public function addPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'Food');
            if ($result !== true) {
                $this->error($result);
            }

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            $this->model->adminAddFood($data['post'], $data['post']['cat_id']);

            $data['post']['id'] = $this->model->id;
            $hookParam          = [
                'is_add'  => true,
                'food' => $data['post']
            ];
            hook('food_admin_after_save_foods', $hookParam);

            $this->success('添加成功!', url('edit', ['id' => $this->model->id]));
        }
    }


    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $post            = $this->model->where('id', $id)->find();
        $cates = $this->categoryModel->adminCategoryTree($post['cat_id'],0);
        $this->assign('cates', $cates);
        $this->assign('post', $post);

        return $this->fetch();
    }


    public function editPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'Food');
            if ($result !== true) {
                $this->error($result);
            }

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            $this->model->adminEditFood($data['post'], $data['post']['cat_id']);

            $hookParam = [
                'is_add'  => false,
                'food' => $data['post']
            ];
            hook('food_admin_after_save_foods', $hookParam);

            $this->success('保存成功!');

        }
    }


    public function delete() 
    {
        $id = $this->request->param('id');
        $cate = $this->model->get($id)->toArray();
        if (empty($cate)) {
            $this->error('菜谱不存在!');
        }

        //删除
        $c2 = Db::table('rc_food')->where('id',$id)->delete();
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
