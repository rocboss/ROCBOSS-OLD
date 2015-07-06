<?php die('Access Denied');?>
{include('_part_header.tpl.php')}
<link rel="stylesheet" type="text/css" href="{$css}rebox.css">
<script src="{$js}rebox.js"></script>
<script src="{$js}post.js"></script>
<div id="container">
    <div class="main-outlet container">
        <div class="content left">
            <h2 class="nav-head" style="color: #017e66">
                <i class="icon icon-search x2"></i> 搜索：{$search}
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