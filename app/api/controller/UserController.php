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
     * 登录验证提交
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
                    $ukey = $this->getUkey($info['id']);
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
}
