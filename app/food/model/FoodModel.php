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

class FoodModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];

    /**
     * description 自动转化
     * @param $value
     * @return string
     */
    public function getDescriptionAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setDescriptionAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }

    /**
     * @return array
     */
    public function adminFoodList($filter, $isPage = false)
    {
        $where = [
            'a.add_time' => ['>=', 0],
            'a.delete_time' => 0
        ];

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $where['a.cat_id'] = ['eq', $category];
            array_push($join, [
                '__FOOD_CATEGORY__ b', 'a.cat_id = b.id'
            ]);
            $field = 'a.*,b.name AS cat_name,u.user_login,u.user_nickname,u.user_email';
        } else {
            array_push($join, [
                '__FOOD_CATEGORY__ b', 'a.cat_id = b.id'
            ]);
            $field = 'a.*,b.name AS cat_name,u.user_login,u.user_nickname,u.user_email';
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.name'] = ['like', "%$keyword%"];
        }

        // $portalPostModel = new PortalPostModel();
        $foods        = $this->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('a.list_order', 'DESC')
            ->paginate(10);

        return $foods;

    }

    /**
     * 菜谱分类详情
     * @param $id
     * @return bool
     */
    public function food_cate_detail($id) 
    {   
        $where = [
            'a.add_time' => ['>=', 0],
            'a.delete_time' => 0,
            'a.status' => 1,
            'a.cat_id' => $id
        ];

        $field = 'a.id, a.name, a.description, a.more';

        $foods        = $this->alias('a')->field($field)
            ->where($where)
            ->order('a.list_order', 'DESC')
            ->select()->toArray();

        return $foods;
    }

    /**
     * 菜谱详情
     * @param $id
     * @return bool
     */
    public function food_detail($id) 
    {   
        $where = [
            'a.add_time' => ['>=', 0],
            'a.delete_time' => 0,
            'a.status' => 1,
            'a.id' => $id
        ];

        $join = [
            ['__FOOD_CATEGORY__ b', 'a.cat_id = b.id']
        ];

        $field = 'a.name, a.wendu, a.time, a.description, b.name AS cat_name, a.more';

        $foods        = $this->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('a.list_order', 'DESC')
            ->find();

        return $foods;
    }

    /**
     * 添加菜谱
     * @param $data
     * @return bool
     */
    public function adminAddFood($data, $categories) 
    {
        $data['user_id'] = cmf_get_current_admin_id();

        if (!empty($data['more']['thumbnail'])) {
            $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
        }
        $data['add_time'] = time();
        $this->allowField(true)->data($data, true)->isUpdate(false)->save();

        return $this;
    }


    /**
     * 编辑菜谱
     * @param array $data 文章数据
     * @param array|string $categories 文章分类 id
     * @return $this
     */
    public function adminEditFood($data, $categories)
    {

        unset($data['user_id']);

        if (!empty($data['more']['thumbnail'])) {
            $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
        }

        $this->allowField(true)->isUpdate(true)->data($data, true)->save();

        return $this;

    }

}