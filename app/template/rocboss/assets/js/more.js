(function( $ ){          
    var target = null;
    var template = null;
    var lock = false;
    var variables = {
        'last' : 30       
    } 
    var settings = {
        'tid'         :   '0',
        'amount'      :   '10',          
        'address'     :   'home',
        'format'      :   'json',
        'template'    :   '.reply-list',
        'trigger'     :   '.get_more',
        'scroll'      :   'false',
        'offset'      :   '100',
        'spinner_code':   ''
    }
    
    var methods = {
        init  :   function(options){
            return this.each(function(){
              
                if(options){
                    $.extend(settings, options);
                }
                template = $(this).children(settings.template).wrap('<div/>').parent();
                template.css('display','none')
                $(this).append('<div class="more_loader_spinner">'+settings.spinner_code+'</div>')
                $(this).children(settings.template).remove()   
                target = $(this);
                if(settings.scroll == 'false'){                    
                    $(this).find(settings.trigger).bind('click.more',methods.get_data);
                    $(this).more('get_data');
                }                
                else{
                    if($(this).height() <= $(this).attr('scrollHeight')){
                        target.more('get_data',settings.amount*2);
                    }
                    $(this).bind('scroll.more',methods.check_scroll);
                }
            })
        },
        check_scroll : function(){
            if((target.scrollTop()+target.height()+parseInt(settings.offset)) >= target.attr('scrollHeight') && lock == false){
                target.more('get_data');
            }
        },
        debug :   function(){
            var debug_string = '';
            $.each(variables, function(k,v){
                debug_string += k+' : '+v+'\n';
            })
            alert(debug_string);
        },     
        remove        : function(){            
            target.children(settings.trigger).unbind('.more');
            target.unbind('.more')
            target.children(settings.trigger).remove();
        },
        add_elements  : function(data){
            var root = target ;  
            var counter = 0;
            if(data){
                $(data).each(function(){
                    counter++;
                    var t = template;
                    var pid = 0; 
                    var uid = 0;
                    $.each(this, function(key, value){                     
                        if(t.find('.'+key)){
                            if(key == 'avatar'){
                                t.find('.'+key).attr('src', value);
                            }else if(key == 'pid') {
                                t.find('.'+key).attr('id', 'reply-'+value);
                                t.find('.reply-list').attr('id','d-reply-'+value);
                                t.find('.floor-'+key).attr('id', value);
                                t.find('.floor').attr('id', 'floor-more-'+value);
                                pid = value;
                            }else if(key == 'uid') {
                                t.find('.'+key).attr('href', t.find('.reply-list').attr('root-user-data')+value);
                                uid = value;
                            }else if(key == 'username') {
                                $('#reply-'+pid).attr('data-username', value);
                                t.find('.avatar').attr('alt', value);
                                t.find('.showFloorReply').attr('data-username',value);
                                t.find('.'+key).html(value);
                                t.find('.showFloorReply').attr('href', 'javascript:showFloorReply('+pid+',\"@'+value+' \");');
                            }else if(key == 'floor') {
                                if(value[0] != null) {
                                    var i = 0;
                                    t.find('.'+key).html('');
                                    for (i; i < value.length; i++) {
                                        t.find('.'+key).append('<div id=\"floor-list-'+value[i].floorId+'\" class=\"floor-list\"></div>');
                                        t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-avatar\"><a href=\"'+$('.reply-list').attr('root-user-data')+value[i].floorUid+'/\"><img src=\"'+value[i].avatar+'\"></a></span>');
                                        t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-username\"><a href=\"'+$('.reply-list').attr('root-user-data')+value[i].floorUid+'/\">'+value[i].floorUser+'</a></span>');
                                        if (login_groupid == 9) {
                                            if (login_uid != value[i].floorUid) {
                                                t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-admin right\"><a href=\"javascript:showFloorReply('+pid+',\'@'+value[i].floorUser+' \');\" title=\"回复TA\"><i class=\"icon icon-forward x1\"></i>回复</a><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[i].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                                            } else {
                                                t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-admin right\"><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[i].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                                            }
                                        } else {
                                            if (login_uid != value[i].floorUid && login_uid != 0) {
                                                t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-admin right\"><a href=\"javascript:showFloorReply('+pid+',\'@'+value[i].floorUser+' \');\" title=\"回复TA\"><i class=\"icon icon-forward x1\"></i>回复</a></span>');
                                            } else if(login_uid == value[i].floorUid && login_uid != 0) {
                                                t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-admin right\"><a class=\"delete-btn\" href=\"javascript:deleteFloor('+value[i].floorId+');\"><i class=\"icon icon-delete x1\"></i>删除</a></span>');
                                            }
                                        }
                                        t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-time right\">'+value[i].floorTime+'</span><div class=\"clear\"></div>');
                                        t.find('.'+key+' #floor-list-'+value[i].floorId).append('<span class=\"floor-content\">'+value[i].floorContent+'</span>');
                                    }
                                    if (i >= 5) {
                                        t.find('.'+key).append('<div class=\"floor-more\"><a href=\"javascript:getMoreFloor('+pid+', 1);\"><i class=\"icon icon-unfold x1\"></i> 点击加载更多评论</a></div>');
                                    } else {
                                        t.find('.'+key).append('<div class=\"floor-more\">已加载全部评论</div>');
                                    };
                                } else {
                                    t.find('.'+key).html('');
                                }
                            }else if(key == 'client') {
                                if(value != "") {
                                    t.find('.'+key).html('<i class=\"icon icon-location\"></i> '+value);
                                }
                            }else if(key == 'posttime') {
                                t.find('.'+key).html('<i class=\"icon icon-time\"></i> '+value);
                            }else{
                                t.find('.'+key).html(value);
                                t.find('.reply-admin').html('');
                                if (uid == login_uid || login_groupid == 9) {
                                    t.find('.reply-admin').html('<a class=\"deleteReply\"><i class=\"icon icon-delete x1\"></i>删除</a>');
                                    t.find('.deleteReply').attr('href', 'javascript:deleteReply('+pid+');');
                                }
                                
                            }
                        }
                    })         

                    if(settings.scroll == 'true'){
                        root.children('.more_loader_spinner').before(t.html())  
                    }else{
                          root.children(settings.trigger).before(t.html())  

                    }
                    variables.last++;                 
                })
                
                
            }            
            else  methods.remove()
            target.children('.more_loader_spinner').css('display','none');
            if(counter < settings.amount) methods.remove()  
            var h = window.location.hash;
            if (h.substring(0, 7) == '#reply-') {
                if($('#d-reply-'+h.substr(7)).length>0) {
                    $('html,body').animate({
                        scrollTop: $('#d-reply-'+h.substr(7)).offset().top - 58
                    }, 1000)
                } else {
                    $('.get_more').trigger('click'); 
                }
            }
            if (h.substring(0, 7) == '#floor-') {
                var tmpStr = h.substr(7);
                var strArr = tmpStr.split('-');
                if($('#d-reply-'+strArr[0]).length>0) {
                    if ($('#d-reply-'+strArr[0]+' #floor-list-'+strArr[1]).length>0) {
                        $('html,body').animate({
                            scrollTop: $('#floor-list-'+strArr[1]).offset().top - 58
                        }, 1000);
                    } else {
                        $('html,body').animate({
                            scrollTop: $('#d-reply-'+strArr[0]).offset().top - 58
                        }, 1000);
                        alertMessage('请手动点击加载更多评论~');
                    }
                } else {
                    $('.get_more').trigger('click'); 
                }
            }
        },
        get_data      : function(){   
            var ile;
            lock = true;
            target.children(".more_loader_spinner").css('display','block');
            $(settings.trigger).css('display','none');
            if(typeof(arguments[0]) == 'number') ile=arguments[0];
            else {
                ile = settings.amount;              
            }
            
            $.post(settings.address, {
                tid  : settings.tid,
                last : variables.last, 
                amount : ile                
            }, function(data){            
                $(settings.trigger).css('display','block')
                methods.add_elements(data)
                lock = false;
            }, settings.format)
            
        }
    };
    $.fn.more = function(method){
        if(methods[method]) 
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        else if(typeof method == 'object' || !method) 
            return methods.init.apply(this, arguments);
        else $.error('Method ' + method +' does not exist!');

    }    
})(jQuery)