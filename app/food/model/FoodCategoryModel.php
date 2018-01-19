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
namespace app\food\model;

use app\admin\model\RouteModel;
use think\Model;
use tree\Tree;

class FoodCategoryModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 生成分类 select树形结构
     * @param int $selectId 需要选中的分类 id
     * @param int $currentCid 需要隐藏的分类 id
     * @return string
     */
    public function adminCategoryTree($selectId = 0, $currentCid = 0)
    {
        $where = ['delete_time' => 0];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";

            array_push($newCategories, $item);
        }

        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);

        return $treeStr;
    }

    /**
     * @return array
     */
    public function adminFoodCategoryList($filter)
    {
        $w = [];

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $w['g.name'] = ['like','%'.$keyword.'%'];
        }

        $list = $this->alias('g')
            ->where($w)
            ->order("g.list_order DESC")
            ->paginate(20);

        return $list;
    }

    /**
     * 添加菜谱分类
     * @param $data
     * @return bool
     */
    public function addEditCategory($data)
    {
        $result = true;
        self::startTrans();
        try {

            if (empty($data['id'])) {
                $data['add_time'] = time();
                $this->allowField(true)->save($data);
            } else {
                $id = intval($data['id']);
                $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }

        return $result;
    }

}