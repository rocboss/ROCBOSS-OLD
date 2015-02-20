<!--{include _part_header.tpl}-->
<link rel="stylesheet" type="text/css" href="<!--{ROOT}-->application/template/rocboss/css/rebox.css">
<script src="<!--{ROOT}-->application/template/rocboss/js/rebox.js"></script>
<div id="container">
    <div class="main-outlet container">
    <div class="content left">
        <div class="nav-head">私信</div>
        <ul>
            <!--{loop $whisperList $t}-->
            <li class="topic-list" id="whisper-<!--{$t['id']}-->">
                <div class="topic">
                    <div class="topic-head">
                        <a href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->/" class="topic-avatar">
                            <img src="<!--{if $whisperStatus == 2}--><!--{$loginInfo['avatar']}--><!--{else}--><!--{$t['avatar']}--><!--{/if}-->" alt="<!--{if $whisperStatus == 2}--><!--{$loginInfo['username']}--><!--{else}--><!--{$t['username']}--><!--{/if}-->">
                        </a>
                        <!--{if $whisperStatus == 2}-->
                        发给 
                        <a class="nickname" href="<!--{ROOT}-->user/index/uid/<!--{$t['atuid']}-->/">
                            <img src="<!--{$t['avatar']}-->" class="talk-avatar-tiny"><!--{$t['username']}-->
                        </a>
                        <!--{if $t['isread'] == 0}-->[未读]<!--{else}-->[已读]<!--{/if}-->
                        <!--{else}-->
                        <a class="nickname" href="<!--{ROOT}-->user/index/uid/<!--{$t['uid']}-->/">
                            <!--{$t['username']}-->
                        </a>
                        <!--{/if}-->
                        <span class="time">
                            <!--{$t['posttime']}-->
                        </span>
                    </div>
                    <span class="topic-content">
                        <!--{$t['content']}-->
                    </span>
                    <div class="clear"></div>
                </div>
            </li>
            <!--{/loop}-->
        </ul>

        <div id="pager">
          <!--{if $whisperList == array() }--> 
            暂无私信
          <!--{else}--> 
            <!--{$page}--> 
          <!--{/if}-->
        </div>
    </div>

    <div class="side left">
        <div class="box">
          <ul class="list-topic">
            <li<!--{if $whisperStatus == '0'}--> class="active"<!--{/if}-->><a href="<!--{ROOT}-->user/whisper/status/0/">未读私信</a></li>
            <li<!--{if $whisperStatus == '1'}--> class="active"<!--{/if}-->><a href="<!--{ROOT}-->user/whisper/status/1/">已读私信</a></li>
            <li<!--{if $whisperStatus == '2'}--> class="active"<!--{/if}-->><a href="<!--{ROOT}-->user/whisper/status/2/">已发私信</a></li>
          </ul>
        </div>
    </div>
    <div class="clear"></div>
    </div>
</div>
<!--{include _part_footer.tpl}-->