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
use think\Request;
use think\Db;
use think\Validate;
class ApiBaseController extends HomeBaseController
{
    public function _initialize(){
        parent::_initialize();

        $ext_action=array('register', 'login');
        $isukey=input('?post.ukey');
        // dump($isukey);
        

        $ukey = input('post.ukey');

        // if(request()->isGet()){
        //     $this->inputData=input('get.');
        // }
        if(!in_array(strtolower(request()->action()),$ext_action)){
            if(!$isukey){
                $this->ajaxBack(false,'请登录',-1);
            }
            $this->uid=$this->checkUkeyNoTime($ukey);
            if(!$this->uid){
                $this->ajaxBack(false,'请登录',-1);
            }
            
        }
    }

    protected function ajaxBack($status=true,$msg='success',$code=0,$arr=array(), $isok = 0){
        $data=array(
            'status'=>$status,
            'code'=>$code,
            'msg'=>$msg,
        );
        if($arr){
            $data['info']=$arr;
        }
        
        $data['isok'] = $isok;
        
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
        // return json($data);
    }

    /**
     * 添加ukey
     *
     * @param array $data
     * @return boolean
     */
    protected function addKey($uid){
        if($uid){
            $ukey=$this->getUkey($uid);
            if($ukey){
                return $ukey;
            }
        }
        $data['uid']=$uid;
        $data['ukey']=$this->newKey();
        $data['create_time']=time();
        $id=DB::name('ukey')->insert($data);
        if(!$id){
            $this->ajaxBack(false,'系统错误',-1);
            return false;
        }
        $this->delKey($data['uid']);
        return $data['ukey'];
    }
    /**
     * 删除ukey
     *
     * @param int $uid 用户uid
     */
    protected function delKey($uid){
        $map=array(
            'uid'=>$uid,
            'create_time'=>array('lt',(time()-10))
        );
        DB::name('ukey')->where($map)->delete();
    }
    /**
     * 获取ukey
     *
     * @param int $uid 用id
     * @return mixed
     */
    protected function getUkey($uid){
        if(!$uid || $uid<=0){
            return false;
        }
        $map=array(
            'uid'=>$uid,
            'create_time'=>array('gt',(time()-60*60*24*30))
        );
        $ukey=DB::name('ukey')->where($map)->value('id');
        return $ukey;
    }
    /**
     * 检测ukey
     *
     * @param string $ukey ukey
     * @return boolean
     */
    protected function checkUkeyNoTime($ukey){
        $map=array(
            'ukey'=>$ukey,
        );
        $uid=DB::name('ukey')->where($map)->value('id');
        return $uid;
    }
    /**
     * 生成ukey
     *
     * @return string
     */
    protected function newKey(){
        $ukey=sp_random_string_key(32);
        if($this->checkUkeyNoTime($ukey)){
            $this->newKey();
        }
        return $ukey;
    }
    /**
     * 获取用户信息
     * @param int $uid 用户id
     */
    protected function get_user($uid=0,$mobile='',$find_fields = null){
        if($uid>0){
            $where['id']=$uid;
        }elseif($mobile){
            $where['mobile']=$mobile;
        }else{
            return false;
        }
        $where['user_status']=1;
        if($find_fields){
            // $fields=array('user_id','truename','getuicid','tel','is_kefu','did','user_name','addtime','logintime','dian','pic', 'logins');
            $fields = $find_fields;
        }else{
            $fields=array('id', 'user_login', 'user_nickname', 'avatar', 'mobile', 'area', 'last_login_time', 
            'sex', 'birthday', 'create_time');
        }
        
        $info=DB::name('user')->field($fields)->where($where)->find();
        if($info){
            $info['create_time']=date("Y-m-d H:i:s",$info['create_time']);
            $info['last_login_time']=date("Y-m-d H:i:s",$info['last_login_time']);

            // if($info['pic']){
            //     $arr=explode(",",$info['pic']);
            //     $arr[0] = !empty($arr[0]) ? $arr[0] : '/img/default.jpg';
            //     $info['pic']=$this->get_host_url().$arr[0];
            // }else{
            //     $info['pic']=$this->get_host_url().$arr[0];
            // }
        }
        return $info;
    }
}
