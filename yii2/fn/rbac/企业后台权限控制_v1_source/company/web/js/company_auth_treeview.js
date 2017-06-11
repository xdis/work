var htmlStart = '<ul class="checkbox-tree none-list-tree cd-accordion-menu">';
$(document).ready(function () {
    var postId = $('#update_id').val();
    $.ajax({
        url: 'load-auth-date',
        type: 'post',
        data:{id:postId},
        success: function (data) {
            endlesslyTree(data);
            htmlStart += '</ul>';
            $("#auth-tree").append(htmlStart);
            afterAjaxCallback();
        },
        error: function (err) {
            console.log(err);
        }
    });
});


//接下来实现无限极的逻辑,重点就是自己调用自己
function endlesslyTree(items) {
    var index = 0;
    for (index; index < items.length; index++) {
        var hadCheck = items[index].checked == 1;
        if (items[index].children) {
            htmlStart += '<li class="has-children"><input data-state="expanded" value=" -" type="button" id="mycheckbox' + items[index].id + '"><label><input type="checkbox" value="' + items[index].id + '"/>' + items[index].text + '</label>';
            htmlStart += '<ul class="none-list-tree">';
            //有下级,自己调自己
            endlesslyTree(items[index]);
            console.log(endlesslyTree(items[index].children));
            console.log(items[index]);
            htmlStart += '</ul>';
            htmlStart += '</li>';
        } else {
            //无下级,直接渲染
            htmlStart += templateData(items[index],hadCheck);
        }
    }
}

function templateData(data,hadCheck) {
    var templateString = '';
    if(hadCheck){
        templateString = '<li><label><input checked type="checkbox" value="' + data.id + '" data-type="' + data.type + '"/>' + data.text + '</label></li>'
    }else{
        templateString = '<li><label><input type="checkbox" value="' + data.id + '" data-type="' + data.type + '"/>' + data.text + '</label></li>'
    }
    return templateString;
}


function afterAjaxCallback() {
    $('.checkbox-tree').on('click', 'input[type=checkbox]', function () {
        $(this).closest('li').children('ul').find('input').prop('checked', $(this).prop('checked'));
        $(this).parentsUntil('ul.checkbox-tree', 'ul').each(function () {
            var parent = $(this).prev('label').children('input'),
                siblings = $(this).children('li').children('label').children('input');
            updateParentBasedOnSiblings(parent, siblings);
        });
    });

    //关于收起展开的,我只需要控制一级下级就可以了,不需要变更上级.
    //一开始是默认收起的
    var accordionsMenu = $('.cd-accordion-menu');
    if (accordionsMenu.length > 0) {
        accordionsMenu.each(function () {
            var accordion = $(this);
            accordion.on('click', 'input[type=button]', function () {
                var button = $(this);
                // console.log(button.attr('data-state'));
                // ( checkbox.prop('checked') ) ? checkbox.siblings('ul').attr('style', 'display:none;').slideDown(300) : checkbox.siblings('ul').attr('style', 'display:block;').slideUp(300);
                // (button.attr('data-state')) ? button.siblings('ul').attr('style','display:none;').slideDown(300) : button.siblings('ul').attr('style','display:block;').slideUp(300);
                if (button.attr('data-state') == 'expanded') {
                    button.siblings('ul').attr('style', 'display:none;');
                    button.attr('data-state', 'collapse');
                    button.val(" -")
                } else {
                    button.siblings('ul').attr('style', 'display:block;');
                    button.attr('data-state', 'expanded');
                    button.val('+');
                }
            });
        });
    }

    function updateParentBasedOnSiblings(parent, siblings) {
        var numberOfInputs = siblings.length,
            numberOfInputsChecked = siblings.filter(function () {
                return $(this).prop('checked') || $(this).prop('indeterminate');
            }).length;
        parent.prop('indeterminate', numberOfInputsChecked > 0 && numberOfInputsChecked < numberOfInputs);
        parent.prop('checked', numberOfInputsChecked === numberOfInputs);
    }


    // $("#allcheck").on('click', function () {
    //     var ajaxArray = [];
    //     $(':checkbox:checked').each(function (i) {
    //         if($(this).attr('data-type')!==4){
    //             ajaxArray.push($(this).val());
    //         }
    //     });
    //     console.log(ajaxArray);
    // });

    var $RegisterForm = $(".auth-form");
    var objtip = $("#show-tips");
    var RegisterForm = $RegisterForm.Validform({
        tiptype: function (msg, o, cssctl) {
            cssctl(objtip, o.type);
            objtip.css('visibility', 'visible');
            objtip.text(msg);
        },
        beforeSubmit: function () {
            return false
        }
    });

    $("#auth-now").on('click', function () {
        // console.log($('input[type="checkbox"]:checked').length);
        if($("#role_name").val()==''){
            layer.msg('请输入角色名称');
            return;
        }
        if (($('input[type="checkbox"]:checked').length) < 1) {
            layer.msg('请选择权限');
            return;
        }

        //获取数据传值
        var ajaxArray = [];
        $(':checkbox:checked').each(function (i) {
            if ($(this).attr('data-type') !== 4) {
                ajaxArray.push($(this).val());
            }
        });
        var AuthItem = {};
        AuthItem['name'] = $("#role_name").val();
        AuthItem['description'] = $("#role_desc").val();
        AuthItem['type'] = "1";
        AuthItem['purview'] = ajaxArray.join(',').toString();
        console.log(JSON.stringify(AuthItem));
        $.ajax({
            url:"create",
            type:'post',
            data:{AuthItem},
            success:function(data){
                console.log(data);
                window.location.href = data.url;
            },
            error:function(err){
                layer.msg("服务器错误");
            }
        });
        ajaxArray = [];
    });

    $("#update-auth").on('click', function () {
        // console.log($('input[type="checkbox"]:checked').length);

        if($("#role_name").val()==''){
            layer.msg('请输入角色名称');
            return;
        }

        if (($('input[type="checkbox"]:checked').length) < 1) {
            layer.msg('请选择权限');
            return;
        }

        //获取数据传值
        var ajaxArray = [];
        $(':checkbox:checked').each(function (i) {
            if ($(this).attr('data-type') !== 4) {
                ajaxArray.push($(this).val());
            }
        });
        var AuthItem = {};
        AuthItem['name'] = $("#role_name").val();
        AuthItem['description'] = $("#role_desc").val();
        AuthItem['type'] = "1";
        AuthItem['purview'] = ajaxArray.join(',').toString();
        var id = $("#update_id").val();
//            console.log(JSON.stringify(AuthItem));

        $.ajax({
            url:"update?id="+id,
            type:'post',
            data:{AuthItem},
            success:function(data){
                console.log(data);
                window.location.href = data.url;
            },
            error:function(err){
                layer.msg("服务器错误");
            }
        });
        ajaxArray = [];
    });
}

