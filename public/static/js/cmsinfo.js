!function(a) {
    //a标签post提交
    $('.a-post').click(function(){
        var msg =$(this).attr('post-msg');

        if(msg) {
            var url =$(this).attr('post-url');
            layer.confirm(msg, {icon: 3, title:'提示', offset: '33%'}, function(index){
                $.ajax(
                    {
                        url : url,
                        type : 'post',
                        dataType : 'json',
                        success : function (json)
                        {
                            if(json.code == 200 || json.code == 1){
                                if (json.code === 1) {
                                    json.url = '/publics/customer/group'
                                }

                                layer.msg(json.msg, { time: 1000, icon: 6, offset: '33%' }, function() {
                                    window.location.href=json.url
                                })
                            }else if(json.code == 0){
                                layer.msg(json.msg, { icon: 5, offset: '33%' })

                            }
                            setTimeout(function() {
                                $('.close').click();
                            },1e3);
                        },
                        error:function(xhr){          //上传失败

                            layer.msg(json.msg, { time: 1500, icon: 4, offset: '33%' })

                        }
                    });
                layer.close(index)
            })
        }
    });

    //a标签post提交
    $('.a-post-no').click(function(){
        var url =$(this).attr('post-url');
        $.get(url, function(res) {
            if (res.code === 200) {
                window.location.href = res.url
            } else {
                layer.msg(json.msg, { icon: 5, offset: '33%' })
            }
        })
    });


    //form表达提交
    $(".ajax-post").click(function(){

        var data,ajaxCallUrl,postUrl;

        d = $(this).parents('.form-horizontal')
        postUrl = $(this).attr('post-url')

        //按钮上的url优先
        ajaxCallUrl = postUrl ? postUrl : d.attr('action');

        $.ajax({
            url : ajaxCallUrl,
            type : 'post',
            dataType : 'json',
            data : d.serialize(),
            success: function(json) {
                if(json.code == 200  || json.code == 1){
                    if (json.code === 1) {
                        json.url = '/publics/customer/group'
                    }
                    layer.msg(json.msg, { time: 1000, icon: 6, offset: '33%' }, function () {
                        layer.confirm('是否离开此页', {icon: 3, title:'提示', offset: '33%'}, function(index){
                            window.location.href=json.url
                            layer.close(index)
                        })
                    })

                }else if(json.code == 0){
                    layer.msg(json.msg, { time: 1500, icon: 5, offset: '33%' })

                }
                setTimeout(function() {
                    $('.close').click();
                },3e3);
            },
            error:function(xhr){          //上传失败
                layer.msg(xhr.responseText, { time: 1000, icon: 4, offset: '33%' })
            }
        });
    });


    //按钮禁止
    a(".ajax-post").on("click",
        function() {
            var b = a(this);
            b.button("loading"),
                setTimeout(function() {
                    b.button("reset");
                },1e3)
        });

    //form表达提交，不提示
    $(".ajax-post-no").click(function(){

        var data,ajaxCallUrl,postUrl;

        d = $(this).parents('.form-horizontal');
        postUrl = $(this).attr('post-url');

        //按钮上的url优先
        ajaxCallUrl = postUrl ? postUrl : d.attr('action');

        $.ajax({
            url : ajaxCallUrl,
            type : 'post',
            dataType : 'json',
            data : d.serialize(),
            success: function(json) {
                if(json.code == 200  || json.code == 1){
                    if (json.code === 1) {
                        json.url = '/publics/customer/group'
                    }
                    layer.msg(json.msg, { time: 1000, icon: 6, offset: '33%' })

                }else if(json.code == 0){
                    layer.msg(json.msg, { time: 1500, icon: 5, offset: '33%' })

                }
                setTimeout(function() {
                    $('.close').click();
                },3e3);
            },
            error:function(xhr){          //上传失败
                layer.msg(xhr.responseText, { time: 1000, icon: 4, offset: '33%' })
            }
        });
    });

    //按钮禁止
    a(".ajax-post-no").on("click",
        function() {
            var b = a(this);
            b.button("loading"),
                setTimeout(function() {
                    b.button("reset");
                },1e3)
        });
} (jQuery);


