<?php $__env->startSection('content'); ?>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home.nav.create')): ?>
                    <a class="layui-btn layui-btn-sm" href="<?php echo e(route('admin.nav.create')); ?>">添 加</a>
                <?php endif; ?>
                    <button class="layui-btn layui-btn-sm" id="returnParent" pid="0">返回上级</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>

            <script type="text/html" id="image">
                {{#  if(d.image){ }}
                <a href="javascript:void(0);" lay-event="img_big" title="{{d.image}}"><img src="{{d.image}}" alt="" width="50" height="28"></a>
                {{# }else{ }}
                <a href="javascript:void(0);" lay-event="img_big" title="<?php echo e(asset('/images/noimage.jpeg')); ?>"><img src="<?php echo e(asset('/images/noimage.jpeg')); ?>" alt="" width="50" height="28"></a>
                {{# }; }}
                <img alt="" style="display:none;" id="displayImg" src="" height="500" width="500"/>
            </script>

            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home.nav')): ?>
                        <a class="layui-btn layui-btn-sm" lay-event="children">子分类</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home.nav.edit')): ?>
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home.nav.destroy')): ?>
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    <?php endif; ?>
                </div>
            </script>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home.nav')): ?>
        <style type="text/css">
            /*调整图片透明度*/
            .myskin{
                background-color:transparent;/*透明（可根据需求自己调整）*/
                opacity: 0.9;/*透明度*/}
            .demo-class{border-top:1px solid #E9E7E7}
        </style>

        <script>
            layui.use(['layer','table','form'],function () {
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,height: 500
                    ,url: "<?php echo e(route('admin.nav.data')); ?>" //数据接口
                    ,page: true //开启分页
                    ,cols: [[ //表头
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'name', title: '分类名称'}
                        ,{field: 'url', title: 'URL'}
                        ,{field: 'sort', width: '8%',title: '排序',sort: true}
                        ,{field: 'image', title: '封面图片',toolbar:'#image',width:90}
                        ,{field: 'created_at', width: '15%', title: '创建时间'}
                        ,{fixed: 'right', width: 320, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("<?php echo e(route('admin.nav.destroy')); ?>",{_method:'delete',ids:data.id},function (result) {
                                if (result.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg)
                            });
                        });
                    } else if(layEvent === 'edit'){
                        location.href = '/admin/nav/'+data.id+'/edit';
                    } else if (layEvent === 'children'){
                        var pid = $("#returnParent").attr("pid");
                        if (data.parent_id!=0){
                            $("#returnParent").attr("pid",pid+'_'+data.parent_id);
                        }
                        dataTable.reload({
                            where:{model:"permission",parent_id:data.id},
                            page:{curr:1}
                        })
                    }

                    //图片放大效果
                    if (layEvent === 'img_big') {
                        var url = $(this).attr('title');
                        $("#displayImg").attr("src", url);
                        var height = $("#displayImg").height();
                        var width = $("#displayImg").width();
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: 0,//隐藏关闭按钮
                            shade: [0.3, '#000'],//黑色背景（0.3代表颜色深度）
                            shadeClose: true,//点击遮罩关闭大图
                            area: ['800px', '300px'], //宽高
                            resize:false,//不可拖拽缩放
                            skin: 'myskin',//大图背景色定义类
                            content: "<img height='300px;'width='800px;'alt=" + name + " title=" + name + " src=" + url + ">"
                        });
                    }
                });

                //返回上一级
                $("#returnParent").click(function () {
                    var pid = $(this).attr("pid");
                    if (pid!='0'){
                        ids = pid.split('_');
                        parent_id = ids.pop();
                        $(this).attr("pid",ids.join('_'));
                    }else {
                        parent_id=pid;
                    }
                    dataTable.reload({
                        where:{model:"permission",parent_id:parent_id},
                        page:{curr:1}
                    })
                })
            })
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>