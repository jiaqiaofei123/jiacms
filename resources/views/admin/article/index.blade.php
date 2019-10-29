@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                @can('home.article.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('home.article.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.article.create') }}">添 加</a>
                @endcan
                <button class="layui-btn layui-btn-sm" id="searchBtn">搜 索</button>
            </div>
            <div class="layui-form" >
                <div class="layui-input-inline">
                    <select name="category_id" lay-verify="required" id="category_id">
                        <option value="">请选择分类</option>
                        @foreach($categorys as $category)
                            <option value="{{ $category->id }}" >{{ $category->name }}</option>
                            @if(isset($category->allChilds)&&!$category->allChilds->isEmpty())
                                @foreach($category->allChilds as $child)
                                    <option value="{{ $child->id }}" >&nbsp;&nbsp;&nbsp;┗━━{{ $child->name }}</option>
                                    @if(isset($child->allChilds)&&!$child->allChilds->isEmpty())
                                        @foreach($child->allChilds as $third)
                                            <option value="{{ $third->id }}" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{ $third->name }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="title" id="title" placeholder="请输入文章标题" class="layui-input">
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('home.article.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('home.article.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
            <script type="text/html" id="thumb">
                @{{#  if(d.thumb){ }}
                <a href="javascript:void(0);" lay-event="img_big" title="@{{d.thumb}}"><img src="@{{d.thumb}}" alt="" width="50" height="28"></a>
                @{{# }else{ }}
                <a href="javascript:void(0);" lay-event="img_big" title="{{asset('/images/noimage.jpeg')}}"><img src="{{asset('/images/noimage.jpeg')}}" alt="" width="50" height="28"></a>
                @{{# }; }}
                <img alt="" style="display:none;" id="displayImg" src="" height="500" width="500"/>
            </script>
            <script type="text/html" id="tags">
                @{{#  layui.each(d.tags, function(index, item){ }}
                <button type="button" class="layui-btn layui-btn-sm">@{{ item.name }}</button>
                @{{# }); }}
            </script>
            <script type="text/html" id="category">
                @{{ d.category.name }}
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('home.article')
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
                    ,url: "{{ route('admin.article.data') }}" //数据接口
                    ,page: true //开启分页
                    ,cols: [[ //表头
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'category', width: 88,title: '分类',toolbar:'#category'}
                        ,{field: 'title',width: 160, title: '标题'}
                        ,{field: 'thumb', title: '封面图片',toolbar:'#thumb',width:90}
                        ,{field: 'keywords', title: '关键词'}
                        ,{field: 'tags', title: '标签',toolbar:'#tags',width:'16%'}
                        ,{field: 'click', title: '点击量'}
                        ,{field: 'created_at', title: '创建时间',width:180}
                        ,{fixed: 'right', width:'12%', align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        ,layEvent = obj.event; //获得 lay-event 对应的值
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.article.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg)
                            });
                        });
                    } else if(layEvent === 'edit'){
                        location.href = '/admin/article/'+data.id+'/edit';
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
                            area: ['500px', '500px'], //宽高
                            resize:false,//不可拖拽缩放
                            skin: 'myskin',//大图背景色定义类
                            content: "<img height='500px;'width='500px;'alt=" + name + " title=" + name + " src=" + url + ">"
                        });
                    }
                });

                @can('home.article.edit')
                //监听是否显示
                form.on('switch(isShow)', function(obj){
                    var index = layer.load();
                    var url = $(obj.elem).attr('url')
                    var data = {
                        "is_show" : obj.elem.checked==true?1:0,
                        "_method" : "put"
                    }
                    $.post(url,data,function (res) {
                        layer.close(index)
                        layer.msg(res.msg)
                    },'json');
                });
                @endcan

                //按钮批量删除
                $("#listDelete").click(function () {
                    var ids = []
                    var hasCheck = table.checkStatus('dataTable')
                    var hasCheckData = hasCheck.data
                    if (hasCheckData.length>0){
                        $.each(hasCheckData,function (index,element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length>0){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.article.destroy') }}",{_method:'delete',ids:ids},function (result) {
                                if (result.code==0){
                                    dataTable.reload()
                                }
                                layer.close(index);
                                layer.msg(result.msg)
                            });
                        })
                    }else {
                        layer.msg('请选择删除项')
                    }
                })

                //搜索
                $("#searchBtn").click(function () {
                    var catId = $("#category_id").val()
                    var title = $("#title").val();
                    dataTable.reload({
                        where:{category_id:catId,title:title},
                        page:{curr:1}
                    })
                })
            })
        </script>
    @endcan
@endsection