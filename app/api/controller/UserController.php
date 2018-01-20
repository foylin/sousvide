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
use app\user\model\UserModel;
use think\Validate;
use think\Db;
class UserController extends ApiBaseController
{
    public function index()
    {
        return $this->fetch(':index');
        // return '非法访问!!!';
    }

    /**
     * 用户注册
     */
    public function register()
    {
        if ($this->request->isPost()) {
            $rules = [
                'password' => 'require|min:6|max:32',
                'mobile'    => 'require|number',
                'area'     => 'require'
            ];
            $isOpenRegistration=cmf_is_open_registration();

            if (!$isOpenRegistration) {
                $this->ajaxBack(false,'暂未开放注册',-1);
            }

            $validate = new Validate($rules);
            $validate->message([
                // 'code.require'     => '验证码不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过32个字符',
                'password.min'     => '密码不能小于6个字符',
                'mobile.require'    => '手机号不能为空',
                'mobile.number'     => '手机号格式错误',
                'area.require'     => '区号不能为空'
                // 'captcha.require'  => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                // $this->error($validate->getError());
                $this->ajaxBack(false,$validate->getError(),-1);
            }

            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
            $user['mobile'] = $data['mobile'];
            $user['area'] = $data['area'];

            $register_result = $register->registerApp($user);
            switch ($register_result['status']) {
                case 0:
                    $ukey=$this->addKey($register_result['info']['id']);
                    $info = $this->get_user($register_result['info']['id']);
                    $back_data['ukey'] = $ukey;
                    $back_data['info'] = $info;
                    $this->ajaxBack(true,'success',0,$back_data);
                    break;
                case 1:
                    // $this->error("您的账户已注册过");
                    $this->ajaxBack(false,'您的账户已注册过',-1);
                    break;
                default :
                    $this->ajaxBack(false,'未受理的请求',-1);
            }

        } else {
            $this->ajaxBack(false,'请求错误',-1);
        }

    }

    /**
     * 登录
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                // 'captcha'  => 'require',
                // 'area' => 'require',
                'mobile' => 'require',
                'password' => 'require|min:6|max:32',
            ]);
            $validate->message([
                'mobile.require' => '用户名不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过32个字符',
                'password.min'     => '密码不能小于6个字符',
                // 'area.require'  => '区号不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                // $this->error($validate->getError());
                $this->ajaxBack(false,$validate->getError(),-1);
            }

            $userModel         = new UserModel();
            $user['user_pass'] = $data['password'];
            $user['mobile'] = $data['mobile'];
            // if (Validate::is($data['mobile'], 'email')) {
            //     $user['user_email'] = $data['mobile'];
            //     $log                = $userModel->doEmail($user);
            // } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['mobile'])) {
            //     $user['mobile'] = $data['mobile'];
            //     $log            = $userModel->doMobile($user);
            // } else {
            //     $user['user_login'] = $data['mobile'];
            //     $log                = $userModel->doName($user);
            // }
            // $session_login_http_referer = session('login_http_referer');
            // $redirect                   = empty($session_login_http_referer) ? $this->request->root() : $session_login_http_referer;
            $log = $userModel->doMobile($user);
            switch ($log) {
                case 0:
                    $info = $this->get_user(0, $data['mobile']);
                    $ukey = $this->addKey($info['id']);
                    $back_data['ukey'] = $ukey;
                    $back_data['info'] = $info;
                    $this->ajaxBack(true,'success',0,$back_data);
                    // cmf_user_action('login');
                    // $this->success('登录成功', $redirect);
                    break;
                case 1:
                    // $this->error('登录密码错误');
                    $this->ajaxBack(false,'登录密码错误',-1);
                    break;
                case 2:
                    // $this->error('账户不存在');
                    $this->ajaxBack(false,'账户不存在',-1);
                    break;
                case 3:
                    // $this->error('账号被禁止访问系统');
                    $this->ajaxBack(false,'账号被禁止访问系统',-1);
                    break;
                default :
                    // $this->error('未受理的请求');
                    $this->ajaxBack(false,'未受理的请求',-1);
            }
        } else {
            $this->ajaxBack(false,'请求错误',-1);
        }
    }

    /**
     * 用户密码重置
     */
    public function passwordreset()
    {

        if ($this->request->isPost()) {
            $validate = new Validate([
                // 'captcha'           => 'require',
                // 'verification_code' => 'require',
                'password'          => 'require|min:6|max:32',
            ]);
            $validate->message([
                // 'verification_code.require' => '验证码不能为空',
                'password.require'          => '密码不能为空',
                'password.max'              => '密码不能超过32个字符',
                'password.min'              => '密码不能小于6个字符',
                // 'captcha.require'           => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                // $this->error($validate->getError());
                $this->ajaxBack(false,$validate->getError(),-1);
            }

            // if (!cmf_captcha_check($data['captcha'])) {
            //     $this->error('验证码错误');
            // }
            // $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            // if (!empty($errMsg)) {
            //     $this->error($errMsg);
            // }

            $userModel = new UserModel();
            // if ($validate::is($data['username'], 'email')) {

            //     $log = $userModel->emailPasswordReset($data['username'], $data['password']);

            // } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
            //     $user['mobile'] = $data['username'];
            //     $log            = $userModel->mobilePasswordReset($data['username'], $data['password']);
            // } else {
            //     $log = 2;
            // }

            $log = $userModel->mobilePasswordReset($data['mobile'], $data['password']);

            switch ($log) {
                case 0:
                    $this->ajaxBack(true);
                    break;
                case 1:
                    $this->ajaxBack(false,'您的账户尚未注册',-1);
                    break;
                case 2:
                    $this->ajaxBack(false,'您输入的账号格式错误',-1);
                    break;
                default :
                    // $this->error('未受理的请求');
                    $this->ajaxBack(false,'未受理的请求',-1);
            }

        } else {
            // $this->error("请求错误");
            $this->ajaxBack(false,'请求错误',-1);
        }
    }

    /**
     * 更新用户头像
     */
    public function up_avatar_base64(){
        $base64 = input('post.avatar');
        if(!$base64){
            $this->ajaxBack(false,'未知的圖片',-1);
        }
        $path1='./public/upload/avatar/'.$this->uid.'/';
        if(!is_dir('.'.$path1)){
            mkdir('.'.$path1,0777,true);
        }
        $path=$path1.$this->uid."_headimg_".time().".png";
        // $path = str_replace('/public','', $path);
        // $this->ajaxBack(false,$path,-1);

        if(strrpos($base64,',')!==false){
            $url = explode(',',$base64);
        }else{
            $url[1]=$base64;
        }
        $a = file_put_contents('.'.$path, base64_decode($url[1]));//返回的是字节数
        if($a>0){
            $data['avatar']= str_replace('./upload', '/upload', $path);
            DB::name("user")->where("id={$this->uid}")->update($data);
            // $user=$this->get_user($this->uid);
            $this->ajaxBack(true);
        }else{
            $this->ajaxBack(false,'上傳失敗'.$path,-1);
        }
    }

    /**
     * 修改密码
     */
    public function editpassword()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'old_password' => 'require|min:6|max:32',
                'password'     => 'require|min:6|max:32',
                'repassword'   => 'require|min:6|max:32',
            ]);
            $validate->message([
                'old_password.require' => '旧密码不能为空',
                'old_password.max'     => '旧密码不能超过32个字符',
                'old_password.min'     => '旧密码不能小于6个字符',
                'password.require'     => '新密码不能为空',
                'password.max'         => '新密码不能超过32个字符',
                'password.min'         => '新密码不能小于6个字符',
                'repassword.require'   => '重复密码不能为空',
                'repassword.max'       => '重复密码不能超过32个字符',
                'repassword.min'       => '重复密码不能小于6个字符',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                // $this->error($validate->getError());
                $this->ajaxBack(false,$validate->getError(),-1);
            }

            $login = new UserModel();
            $log   = $login->editPassword($data);
            switch ($log) {
                case 0:
                    $this->delKey($this->uid);
                    $this->ajaxBack(true);
                    // $this->success('修改成功');
                    break;
                case 1:
                    // $this->error('密码输入不一致');
                    $this->ajaxBack(false,'密码输入不一致',-1);
                    break;
                case 2:
                    // $this->error('原始密码不正确');
                    $this->ajaxBack(false,'原始密码不正确',-1);
                    break;
                default :
                    // $this->error('未受理的请求');
                    $this->ajaxBack(false,'未受理的请求',-1);
            }
        } else {
            // $this->error("请求错误");
            $this->ajaxBack(false,'请求错误',-1);
        }

    }

    /**
     * 编辑用户资料
     */
    public function editprofile()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'user_nickname' => 'chsDash|max:32',
                'sex'     => 'number|between:0,2',
                'birthday'   => 'dateFormat:Y-m-d|after:-88 year|before:-1 day',
                // 'user_url'   => 'url|max:64',
                // 'signature'   => 'chsDash|max:128',
            ]);
            $validate->message([
                'user_nickname.chsDash' => '昵称只能是汉字、字母、数字和下划线_及破折号-',
                'user_nickname.max' => '昵称最大长度为32个字符',
                'sex.number' => '请选择性别',
                'sex.between' => '无效的性别选项',
                'birthday.dateFormat' => '生日格式不正确',
                'birthday.after' => '出生日期也太早了吧？',
                'birthday.before' => '出生日期也太晚了吧？',
                // 'user_url.url' => '个人网址错误',
                // 'user_url.max' => '个人网址长度不得超过64个字符',
                // 'signature.chsDash' => '个性签名只能是汉字、字母、数字和下划线_及破折号-',
                // 'signature.max' => '个性签名长度不得超过128个字符',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                
                // $this->error($validate->getError());
                $this->ajaxBack(false,$validate->getError(),-1);
            }
            $editData = new UserModel();
            if ($editData->editData_byapi($data)) {
                $info = $this->get_user($this->uid);
                    // $ukey = $this->addKey($info['id']);
                    // $back_data['ukey'] = $ukey;
                    // $back_data['info'] = $info;
                $this->ajaxBack(true,'success',0,$info);
                // $this->success("保存成功！", "user/profile/center");

            } else {
                // $this->error("没有新的修改信息！");
                $this->ajaxBack(false,'没有做新的修改！',-1);
            }
        } else {
            // $this->error("请求错误");
            $this->ajaxBack(false,'请求错误',-1);
        }
    }

    
}
