/**
 * Created by smzdm-04 on 2016/1/18.
 */

$(function(){

    //登录注册
    if(zhiyou_open) {
        var config = {"redirect_url":encodeURIComponent(window.location.href)};
        zhiyou_relate.popup_login_init(config);
    } else {
        login();
        register();
    }

    var smzdmRepBoxy={
        resizeWindow:function(element){
            if (window.innerHeight)
                winHeight = window.innerHeight;
            else if ((document.body) && (document.body.clientHeight))
                winHeight = document.body.clientHeight;

            var _top = (winHeight - $(element).height())/2;


            $(element).css('top',_top);
            window.onresize = function(){
                smzdmRepBoxy.resizeWindow(element);
            }
        },

        alert:function(boxyContent,callBack,boxyConfig){
            smzdmRepBoxy.showBoxy("alert",boxyContent,callBack,boxyConfig);
        },

        confirm:function(boxyContent,callBack,boxyConfig){
            smzdmRepBoxy.showBoxy("confirm",boxyContent,callBack,boxyConfig);
        },

        tips:function(boxyContent,callBack,boxyConfig){
            smzdmRepBoxy.showBoxy("tips",boxyContent,callBack,boxyConfig);
        },

        destroy:function(){
            $(".pop_main_box").css("display","none");
        },

        showBoxy:function(boxyType,boxyContent,callBack,boxyConfig){
            smzdmRepBoxy.coverInit();
            switch (boxyType){
                case "alert":
                    smzdmRepBoxy.getAlert(boxyContent,callBack,boxyConfig);
                    smzdmRepBoxy.resizeWindow('.smzdm_boxy_alert');
                    break;
                case "confirm":
                    break;
                case "tips":
                    smzdmRepBoxy.getTips(boxyContent,callBack,boxyConfig);
                    smzdmRepBoxy.resizeWindow('.smzdm_boxy_tips');
                    break;
                default :
                    break;
            }

        },

        hideBoxy:function(obj){
            obj.click(function(){
                $("#boxyCover").hide();
                $(this).parent().hide();
            });
        },

        getAlert:function(boxyContent,callBack,boxyConfig,element){

            if(boxyContent=="destroy"){
                $(".smzdm_boxy_alert").remove();
                $("#boxyCover").remove();


                return false;
            }

            var popTitle=typeof (boxyConfig.title)=="undefined"?"":boxyConfig.title;
            var alertTemp='<div class="pop pop_main_box smzdm_boxy_alert" style="margin-left: -240px; top: 30%;display:none;">'+
                '<i class="pop-close icon-cross-lighter"><!--[if lt IE 8]>x<![endif]--></i>'+
                '<div class="pop-title">'+
                '<div class="pop_name">'+popTitle+'</div>'+
                '</div>'+
                '<!-- pop-content -->'+
                '<div class="pop-content">'+boxyContent+'</div>'+
                '<!-- pop-content end -->'+
                '</div>';
            $(".smzdm_boxy_alert").remove();
            $("body").append(alertTemp);
            $("#boxyCover").show();
            $(".smzdm_boxy_alert").css("display","block");
            callBack&&callBack();
            smzdmRepBoxy.hideBoxy($(".smzdm_boxy_alert").find(".pop-close"));
            $("#boxyCover").click(function(){
                smzdmRepBoxy.alert("destroy");
            });
        },

        getTips:function(boxyContent,callBack,boxyConfig){
            if(boxyContent=="destroy"){
                $(".smzdm_boxy_tips").remove();
                $("#boxyCover").remove();
                return false;
            }

            var typeHtml="";
            if(boxyConfig.type=="success"){
                typeHtml='<i class="icon-loginright"><!--[if lt IE 8]>√<![endif]--></i>';
            }else if(boxyConfig.type=="error"){
                typeHtml='<i class="icon-logintanhao"><!--[if lt IE 8]>404<![endif]--></i>';
            }

            var tipsTemp='<div class="pop pop_no_title  smzdm_boxy_tips" style="margin-left: -155px; top: 30%;display:none;">'+
                '<i class="pop-close icon-cross-lighter"><!--[if lt IE 8]>x<![endif]--></i>'+
                '<div class="pop-content oneLine">'+
                typeHtml+
                '<p class="pop_info">'+boxyContent+'</p>'+
                '</div>'+
                '</div>';

            $(".smzdm_boxy_tips").remove();
            $("body").append(tipsTemp);
            $("#boxyCover").show();
            $(".smzdm_boxy_tips").css("display","block");
            callBack&&callBack();
            smzdmRepBoxy.hideBoxy($(".smzdm_boxy_tips").find(".pop-close"));
            $("#boxyCover").click(function(){
                smzdmRepBoxy.tips("destroy");
            });
        },

        coverInit:function(){
            $("body").find("#boxyCover").remove();
            $("body").append('<div id="boxyCover"></div>');
            $("#boxyCover").css({
                background: "#000 none repeat scroll 0 0",
                display:"none",
                height: "100%",
                left: 0,
                opacity: 0.5,
                position: "fixed",
                top: 0,
                width: "100%",
                "z-index": 9999
            });
        }
    };

    var tool={
        getStrLength:function(str){
            var len = 0;
            if (str.match(/[^ -~]/g) == null)
            {
                len = str.length;
            }
            else
            {
                len = str.length + str.match(/[^ -~]/g).length;
            }
            return len;
        }
    };

    var reportBox={
        selectType:function () {
            $('input[name="report"]').click(function () {
                $('.textarea_jubao').attr("placeholder",$(this).attr("data-remark"));
            });
        },
        selectedType:function () {
            $('input[name="report"][data-checked="1"]').trigger("click");
        },
        insertContent:function () {
            $('.textarea_jubao').on("input",function(){
                var currentContent=$(this).val();
                var contentLength=tool.getStrLength(currentContent);
                $(".min_num").html(contentLength);
                $(".insert_notice").css("display","block");
                if(contentLength<10){
                    $(".min_num").css("color","red");
                    $(".max_num").css("color","#666666");
                    $(".length_notice").css("color","red");
                }else if(contentLength>200){
                    $(".min_num").css("color","#666666");
                    $(".max_num").css("color","red");
                    $(".length_notice").css("color","red");
                }else{
                    $(".min_num").css("color","#666666");
                    $(".max_num").css("color","#666666");
                    $(".length_notice").css("color","666666");
                }
            });
        },
        sendReport:function () {
            $(".btn_report").click(function(){
                var reportType=$('input[name="report"]:checked').val();
                var content=$('.textarea_jubao').val();
                var contentLength=tool.getStrLength(content);
                if(contentLength<10){
                    return false;
                }else if(contentLength>200){
                    return false;
                }
                $.ajax({
                    type : "post",
                    url : smzdm_post+"/ajax_report",
                    data : {article_id:$("#articleID").val(),type:reportType,content:content},
                    dataType : "json",
                    success : function(backReport){
                        smzdmRepBoxy.alert("destroy");
                        if(backReport==null){
                            smzdmRepBoxy.tips("举报提交失败<span data-type='1'></span>",function(){},{type:"error"});
                        }
                        if(typeof(backReport.error_code)=="undefined"){
                            smzdmRepBoxy.tips("举报提交失败<span data-type='2'></span>",function(){},{type:"error"});
                        }
                        if(backReport.error_code==0){
                            smzdmRepBoxy.tips("感谢您的举报，小编会尽快审核给您满意的答复…<span data-type='3'></span>",function(){},{type:"success"});
                        }else{
                            smzdmRepBoxy.tips(backReport.error_msg+"<span data-type='4'></span>",function(){},{type:"error"});
                        }
                    },
                    error : function(){
                        smzdmRepBoxy.tips("很抱歉，网络出了点小差，刷新页面之后再试一下<span data-type='5'></span>",function(){},{type:"error"});
                    }
                });
                
            });
        },
        reportBoxTitle:function () {
            return "文章举报<p style='font-size: 12px;left: 112px;position: absolute;top: 2px;'>（通过后会有<span style='color: red'>10金币</span>奖励哦）</p>";
        },
        getReportBox:function () {
            $("#report").click(function () {
                $.ajax({
                    type : "post",
                    url : smzdm_post+"/ajax_report_box",
                    data : {},
                    dataType : "json",
                    success : function(back){
                        if(back==null){
                            smzdmRepBoxy.tips("很抱歉，网络出了点小差，刷新页面之后再试一下<span data-type='1'></span>",function(){},{type:"error"});
                        }
                        if(typeof(back.error_code)=="undefined"){
                            smzdmRepBoxy.tips("很抱歉，网络出了点小差，刷新页面之后再试一下<span data-type='2'></span>",function(){},{type:"error"});
                        }
                        if(back.error_code==0){
                            smzdmRepBoxy.alert(back.data,function(){
                                reportBox.selectType();//选择举报类型
                                reportBox.selectedType();//默认选中其中一个
                                reportBox.insertContent();//输入字数验证
                                reportBox.sendReport();//提交举报
                            },{title:reportBox.reportBoxTitle()});
                        }else{
                            smzdmRepBoxy.tips("很抱歉，网络出了点小差，刷新页面之后再试一下<span data-type='3'></span>",function(){},{type:"error"});
                        }
                    },
                    error : function(){
                        smzdmRepBoxy.tips("很抱歉，网络出了点小差，刷新页面之后再试一下<span data-type='4'></span>",function(){},{type:"error"});
                    }
                });
            });
        }
    };

    reportBox.getReportBox();//默认加载举报模块
});
