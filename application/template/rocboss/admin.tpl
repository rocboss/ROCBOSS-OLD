<!--{include _admin.header.tpl}-->

<!--{if $type == 'system'}-->
    <!--{include _admin.system.tpl}-->
<!--{/if}-->

<!--{if $type == 'common'}-->
    <!--{include _admin.common.tpl}-->
<!--{/if}-->

<!--{if $type == 'user'}-->
    <!--{include _admin.user.tpl}-->
<!--{/if}-->

<!--{if $type == 'topic'}-->
    <!--{include _admin.topic.tpl}-->
<!--{/if}-->

<!--{if $type == 'reply'}-->
    <!--{include _admin.reply.tpl}-->
<!--{/if}-->

<!--{if $type == 'tag'}-->
    <!--{include _admin.tag.tpl}-->
<!--{/if}-->

<!--{if $type == 'link' || $type == 'edit_link' || $type == 'add_link'}-->
    <!--{include _admin.link.tpl}-->
<!--{/if}-->

<!--{if $type == 'clear'}-->
    <!--{include _admin.clear.tpl}-->
<!--{/if}-->
