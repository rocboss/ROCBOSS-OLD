<?php die('Access Denied');?>
{include('_admin.header.tpl.php')}

{if $type == 'system'}
    {include('_admin.system.tpl.php')}
{/if}

{if $type == 'common'}
    {include('_admin.common.tpl.php')}
{/if}

{if $type == 'user'}
    {include('_admin.user.tpl.php')}
{/if}

{if $type == 'topic'}
    {include('_admin.topic.tpl.php')}
{/if}

{if $type == 'reply'}
    {include('_admin.reply.tpl.php')}
{/if}

{if $type == 'tag'}
    {include('_admin.tag.tpl.php')}
{/if}

{if $type == 'link' || $type == 'edit_link' || $type == 'add_link'}
    {include('_admin.link.tpl.php')}
{/if}

<footer>&copy; ROCBOSS v2.1.0 后台管理中心</footer>
