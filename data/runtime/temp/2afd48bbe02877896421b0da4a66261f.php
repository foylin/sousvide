<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"themes/admin_simpleboot3/admin\hook\index.html";i:1506480813;s:43:"themes/admin_simpleboot3/public\header.html";i:1516186562;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="__TMPL__/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="__TMPL__/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="__STATIC__/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }


        /***************************************************************/
        .btn-on,
        .btn-off{
            cursor: default;
            text-decoration:none !important;
        }
        .btn-on{
            color: #1BBC9D;
        }
        .btn-off{
            color: #9ea3a7;
        }
        .alert-warning{
            padding: 12px 15px;
            margin-top:10px;
            margin-bottom:10px;
        }
        .alert-warning .close{
            font-size: 28px;
            float: left;
        }
        .alert-dismissable .close, .alert-dismissible .close{
            left: 8px;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "__ROOT__/",
            WEB_ROOT: "__WEB_ROOT__/",
            JS_ROOT: "static/js/",
            APP: '<?php echo \think\Request::instance()->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="__TMPL__/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="__STATIC__/js/wind.js"></script>
    <script src="__TMPL__/public/assets/js/bootstrap.min.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip();
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a>所有钩子</a></li>
        <li><a href="<?php echo url('Hook/sync'); ?>">同步钩子</a></li>
    </ul>
    <form action="" method="post" class="margin-top-20">
        <?php 
            $types = ["1"=>'系统钩子','2'=>'应用钩子','3'=>'模板钩子','4'=>'后台模板钩子'];
         ?>
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th width="40">ID</th>
                <th>名称</th>
                <th>类型</th>
                <th>描述</th>
                <th width="150"><?php echo lang('ACTIONS'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($hooks) || $hooks instanceof \think\Collection || $hooks instanceof \think\Paginator): if( count($hooks)==0 ) : echo "" ;else: foreach($hooks as $key=>$vo): ?>
                <tr>
                    <td><?php echo $vo['id']; ?></td>
                    <td><?php echo $vo['name']; ?>:<?php echo $vo['hook']; ?></td>
                    <td><?php echo $types[$vo['type']]; ?></td>
                    <td><?php echo $vo['description']; ?></td>
                    <td>
                        <a href="javascript:parent.openIframeLayer('<?php echo url('Hook/plugins',['hook'=>$vo['hook']]); ?>','钩子<?php echo $vo['name']; ?>插件管理',{});">管理插件</a>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </form>
</div>
<script src="__STATIC__/js/admin.js"></script>
</body>
</html>