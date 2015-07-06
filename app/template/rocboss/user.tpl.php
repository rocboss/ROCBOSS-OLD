<?php die('Access Denied');?>
{include('_part_header.tpl.php')}

<div id="container">
    <div class="main-outlet container">
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="active">会员中心</li>
        </ol>
        {include('_part_user_side.tpl.php')}
        <div class="content">
            {include('_part_user_topic.tpl.php')}

            {include('_part_user_reply.tpl.php')}

            {include('_part_user_follow.tpl.php')}

            {include('_part_user_fans.tpl.php')}

            {include('_part_user_favorite.tpl.php')}

            {include('_part_user_score.tpl.php')}
        </div>
        <div class="clear"></div>
    </div>
</div>
{include('_part_footer.tpl.php')}