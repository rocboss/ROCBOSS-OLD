<?php die('Access Denied');?>
{include('_part_header.tpl.php')}
<style type="text/css">
#add-new-product {
    padding-top: 18px;
    height: 60px;
    border-top: #ececec solid 1px;
    background-color: #f2f2f2;
    display: block;
}

#add-new-product input {
    /*width: 100%;*/
    height: 30px;
    line-height: 20px;
}

#add-new-product .input-group {
    width: 100%;
}

#add-new-product .input-group-btn {
    position: absolute;
    top: 3px;
    right: 1px;
    width: 80px;
    height: 32px;
}
.input-group {
  position: relative;
  display: table;
  border-collapse: separate;
}
.form-control {
  display: block;
  width: 96%;
  height: 34px;
  padding: 3px 2%;
  font-size: 14px;
  line-height: 1.42857;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
  transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
#add-new-product .input-group-btn button {
    z-index: 9;
    display: block;
    width: 100%;
    height: 100%;
    line-height: 18px;
    border: none 0;
    border-radius: 2px;
    background: #38BE5E;
    color: #fff;
    font-size: 14px;
}
.col-sm-search {
  width: 50%;
  margin-left: 25%;
}
</style>

<div id="container">
    <section id="add-new-product">
<div class="container-search">
    <div class="row">
        <div class="col-sm-search">
            <div class="input-group">
                <input type="text" id="searchWord" class="form-control" placeholder="请输入关键字搜索主题" onkeypress="javascript:if(event.keyCode==13) $('#searchWord_submit').click();">
                <div class="input-group-btn">
                    <button id="searchWord_submit" type="button" onclick="javascript:search();" class="btn btn-default dropdown-toggle"><i class="icon icon-search x2"></i> 搜索</button>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
    <div class="main-outlet container">
        <div class="content left">
            <h2 class="nav-head">
            {if $loginInfo['uid'] > 0 }
                {if $signStatus == false}
                    <a href="javascript:doSign();" class="btn-circle right ml10" tip-title="今日签到"><i class="icon icon-squarecheck x2"></i></a>
                {/if}
                <a href="javascript:showTopicForm();" class="btn-circle right" tip-title="发布帖子"><i class="icon icon-edit x2"></i></a>
            {/if}
            {if isset($_COOKIE['type']) && $_COOKIE['type'] == 'lasttime'}
                <a href="{$root}do/posttime/" class="btn-circle" tip-title="按最新发表排序显示"><i class="icon icon-order x2"></i></a>
            {/if} 
            {if isset($_COOKIE['type']) && $_COOKIE['type'] != 'lasttime'}
                <a href="{$root}do/lasttime/" class="btn-circle" tip-title="按最后回复排序显示"><i class="icon icon-order x2"></i></a>
            {/if}
            </h2>
            {include('_part_topic_list.tpl.php')}
        </div>
        <div class="side">
            {include('_part_side.tpl.php')}
        </div>
        <div class="clear"></div>
    </div>
</div>
{include('_part_footer.tpl.php')}