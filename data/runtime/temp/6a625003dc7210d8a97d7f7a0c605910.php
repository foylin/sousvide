<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:49:"themes/admin_simpleboot3/food\category\index.html";i:1516186843;s:43:"themes/admin_simpleboot3/public\header.html";i:1516186562;}*/ ?>
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
        <li class="active"><a href="javascript:;">所有分类</a></li>
        <li><a href="<?php echo url('add'); ?>">添加分类</a></li>
    </ul>
    <form class="well form-inline margin-top-20" method="post" action="<?php echo url('index'); ?>">
        关键字:
        <input type="text" class="form-control" name="keyword" style="width: 200px;"
               value="<?php echo (isset($keyword) && ($keyword !== '')?$keyword:''); ?>" placeholder="请输入关键字...">
        <input type="submit" class="btn btn-primary" value="搜索"/>
        <a class="btn btn-danger" href="<?php echo url('index'); ?>">清空</a>
    </form>
    <form class="js-ajax-form" action="" method="post">
        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="50"><?php echo lang('SORT'); ?></th>
                <th width="50">ID</th>
                <th>分类名称</th>
                <th width="70">状态</th>
                <th width="90">操作</th>
            </tr>
            </thead>
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): if( count($data)==0 ) : echo "" ;else: foreach($data as $key=>$vo): ?>
                <tr>
                    <td>
                                <input name='list_orders[<?php echo $vo['id']; ?>]' type='text' size='3' value='<?php echo $vo['list_order']; ?>' class='input-order'>
                            </td>
                    <td><b><?php echo $vo['id']; ?></b></td>
                    <td><?php echo $vo['name']; ?></td>
                    <td>
                        <?php if($vo['status'] == '1'): ?>
                            <a class="js-ajax-dialog-btn btn-on" data-msg="您确定要停用?" href="<?php echo url('statusUpdate',['id'=>$vo['id'] , 'field'=>'status' , 'status'=>'0']); ?>"><?php echo config('cmf_iconfint_check'); ?> 是 </a>
                        <?php else: ?>
                            <a class="js-ajax-dialog-btn btn-off" data-msg="您确定要启用?" href="<?php echo url('statusUpdate',['id'=>$vo['id'] , 'field'=>'status' , 'status'=>'1']); ?>"><?php echo config('cmf_iconfint_ban'); ?> 否 </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo url('AdminArticle/edit',array('id'=>$vo['id'])); ?>"><?php echo lang('EDIT'); ?></a>
                        <a href="<?php echo url('AdminArticle/delete',array('id'=>$vo['id'])); ?>" class="js-ajax-delete"><?php echo lang('DELETE'); ?></a>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            <tfoot>
            <tr>
                <th width="50"><?php echo lang('SORT'); ?></th>
                <th width="50">ID</th>
                <th>分类名称</th>
                <th width="70">状态</th>
                <th width="90">操作</th>
            </tr>
            </tfoot>
        </table>
        
        <ul class="pagination"><?php echo (isset($page) && ($page !== '')?$page:''); ?></ul>
    </form>
</div>
<script src="__STATIC__/js/admin.js?v=<?php echo $dtFlag; ?>"></script>
<script>

    function reloadPage(win) {
        win.location.reload();
    }

    $(function () {
        setCookie("refersh_time", 0);
        Wind.use('ajaxForm', 'artDialog', 'iframeTools', function () {
            //批量复制
            $('.js-articles-copy').click(function (e) {
                var ids = [];
                $("input[name='ids[]']").each(function () {
                    if ($(this).is(':checked')) {
                        ids.push($(this).val());
                    }
                });

                if (ids.length == 0) {
                    art.dialog.through({
                        id: 'error',
                        icon: 'error',
                        content: '您没有勾选信息，无法进行操作！',
                        cancelVal: '关闭',
                        cancel: true
                    });
                    return false;
                }

                ids = ids.join(',');
                art.dialog.open("__ROOT__/index.php?g=portal&m=AdminArticle&a=copy&ids=" + ids, {
                    title: "批量复制",
                    width: "300px"
                });
            });
            //批量移动
            $('.js-articles-move').click(function (e) {
                var ids = [];
                $("input[name='ids[]']").each(function () {
                    if ($(this).is(':checked')) {
                        ids.push($(this).val());
                    }
                });

                if (ids.length == 0) {
                    art.dialog.through({
                        id: 'error',
                        icon: 'error',
                        content: '您没有勾选信息，无法进行操作！',
                        cancelVal: '关闭',
                        cancel: true
                    });
                    return false;
                }

                ids = ids.join(',');
                art.dialog.open("__ROOT__/index.php?g=portal&m=AdminArticle&a=move&old_term_id=<?php echo (isset($term['term_id']) && ($term['term_id'] !== '')?$term['term_id']:0); ?>&ids=" + ids, {
                    title: "批量移动",
                    width: "300px"
                });
            });
        });
    });
</script>
</body>
</html>