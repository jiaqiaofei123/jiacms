<?php $__env->startSection('content'); ?>

    <header id="container2"
            style="background: #226039 url(<?php echo e(asset('/home/images/bg-girl.jpg')); ?>) center 0 scroll no-repeat;">
        <div class="popover left" data-easein="cardInLeft" data-easeout="cardOutLeft" id="pop7">
            <div class="arrow"></div>
            <div class="popover-inner">
                <div class="popover-content">
                    <p>一个人旅行，不理会繁杂的琐事，自由自在地，去体验一个城市，一段故事，留下一片欢笑。</p>
                </div>
            </div>
        </div>
        <div class="popover right" data-easein="cardInRight" data-easeout="cardOutRight" id="pop6">
            <div class="arrow"></div>
            <div class="popover-inner">
                <div class="popover-content">
                    <p>欠自己的旅行，终有一天，要还给自己。</p>
                </div>
            </div>
        </div>
        <?php echo $__env->make('home/nav', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </header>
    <style type="text/css">
        body {
            background: url(<?php echo e(asset('/home/images/lmlblog.jpg')); ?>) no-repeat center top fixed;
            background-size: cover;
        }

        h1.t_nav span {
            color: #FFF;
        }

        h1.t_nav {
            border-bottom: #FFFFFF 1px solid;
        }

        .post-like, .post-red {
            top: -35px;
            right: 10px;
            margin-right: 20px;
        }
    </style>
    <h1 class="t_nav"><span>奔忙中，你来我往，遇见的都是缘分。茫茫人海，一生当中的擦肩相遇少之可怜，更何况相识与相知。</span><a href="/" class="n2">碎言碎语</a></h1>
    <div class="bloglist">
        <?php $__currentLoopData = $statements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <ul class="arrow_box">
                <div class="sy">
                    <?php if($statement['image']): ?>
                        <img src="<?php echo e(asset($statement['image'])); ?>" alt="美丽的风景">
                    <?php endif; ?>
                    <p>　<?php echo e($statement['content']); ?></p>
                </div>
                
                <span class="dateview"><?php echo e($statement['created_at']); ?></span>
            </ul>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="more">

        </div>
        <div class="post-nav-right"><p><a href="javascript:void(0);" id="clickmore" data-id="0">加载更多</a></p></div>
    </div>
    <div class="blank"></div>

    <script type="text/javascript">
        //点击更多选项
        $("#clickmore").click(function () {
            var id = $("#clickmore").attr('data-id');
            $("#clickmore").attr('data-id', ++id);
            $.post("/index/more", {'id': id, '_token': '<?php echo e(csrf_token()); ?>'},
                function (data) {
                    //为空则判断数据到顶
                    if (data == 'null' || data == '') {
                        $("#clickmore").text('我是有底线的💫');
                        //移除点击事件
                        $("#clickmore").unbind();
                    }
                    var content = '';
                    $.each(data, function (key, val) {
                        var show = val['image']?'block':'none';
                        console.log(show);
                        content +=' <ul class="arrow_box">' +
                            '                <div class="sy">' +
                            '                        <img src="'+val['image']+'" style="display:'+show+'" alt="美丽的风景">' +
                            '                    <p>'+val['content']+'</p>' +
                            '                </div>' +
                            '                <span class="dateview">'+val['created_at']+'</span>' +
                            '            </ul>';
                    });
                    $(".more").append(content);
                });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('home.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>