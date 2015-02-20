  <!--{if $loginInfo['uid'] > 0 }-->
  <dl class="user-box">
    <dl class="m_thumb">
      <a href="<!--{ROOT}-->user/index/uid/<!--{$loginInfo['uid']}-->">
      <img src="<!--{$loginInfo['avatar']}-->" alt="<!--{$loginInfo['username']}-->" id="myAvatar">
      </a>
    </dl>
    <dd class="user_info">
      <p class="name">
        <a href="<!--{ROOT}-->user/index/uid/<!--{$loginInfo['uid']}-->">
          <!--{$loginInfo['username']}-->
        </a>
      </p>
      <p class="ver" id="today-sign">
        <!--{if $signStatus == true}-->
          <i class="icon icon-selectionfill x2"></i> 今日已签到
        <!--{else}-->
          <a href="javascript:doSign();">
            <i class="icon icon-squarecheck x2"></i> 今日签到
          </a>
        <!--{/if}-->
      </p>
      <div class="static_num">
        <dl>
          <dt><a href="<!--{ROOT}-->user/notification/"><!--{$mine['notification']}--></a></dt>
          <dd>提醒</dd>
        </dl>
        <dl>
          <dt><a href="<!--{ROOT}-->user/whisper/"><!--{$mine['whisper']}--></a></dt>
          <dd>私信</dd>
        </dl>
        <dl>
          <dt><a href="<!--{ROOT}-->user/score/" id="mine-score"><!--{$mine['scores']}--></a></dt>
          <dd>积分</dd>
        </dl>
        <div class="clear"></div>
      </div>  
    </dd>
  </dl>
  <!--{/if}-->
  <div class="box">
    <h3 class="list-title"><i class="icon icon-tag x2"></i> 热门标签</h3>
    <ul class="hot-tags">
    <!--{loop $hotTags $tag}-->
      <li><a href="<!--{ROOT}-->home/tag/name/<!--{$tag['tagname']}-->/"><!--{$tag['tagname']}--></a></li>
    <!--{/loop}-->
    </ul>
    <div class="clear"></div>
  </div>

  <div class="box">
    <h3 class="list-title"><i class="icon icon-comment x2"></i> 热门主题</h3>
    <ul class="hot-topics">
      <!--{loop $hotTopics $topic}-->
      <li>
        <a href="<!--{ROOT}-->home/read/<!--{$topic['tid']}-->/"><!--{$topic['title']}--></a>
      </li>
      <!--{/loop}-->
    </ul>
    <div class="clear"></div>
  </div>

  <!--{if isset($signList)}-->
  <div class="box">
    <h3 class="list-title">
      <i class="icon icon-selectionfill x2"></i> 今日签到榜
    </h3>
    <div style="padding: 10px 2px;">
      <!--{loop $signList $s}-->
        <a href="<!--{ROOT}-->user/t/<!--{$s['username']}-->" class="user-ava">
          <img src="<!--{Image::getAvatarURL($s['uid'])}-->" alt="<!--{$s['username']}-->" title="<!--{$s['username']}-->@<!--{Utils::formatTime($s['time'])}-->签到 <!--{$s['changed']}--> 积分">
        </a>
      <!--{/loop}-->
      <div class="clear"></div>
    </div>
  </div>
  <!--{/if}-->

  <div class="box">
    <h3 class="list-title"><i class="icon icon-share x2"></i> 广告</h3>
    <!--{Filter::out($GLOBALS['sys_config']['ad'])}-->
  </div>