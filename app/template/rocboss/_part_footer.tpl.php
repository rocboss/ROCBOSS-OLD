<?php die('Access Denied');?>
<div class="footer" id="footer">
	<div class="main-outlet">
		<div class="col">
			<p>由 <a href="https://www.rocboss.com" target="_blank">ROCBOSS v2.1.0</a> 强力驱动</p>
			<p>联系我们 : admin@rocboss.com</p>
		</div>
		{if isset($hotTags)}
		<div class="col">
			<p class="link">
			热门标签：
				{loop $hotTags $tag}
					<a href="{$root}tag/{$tag.tagname}">{$tag.tagname}</a>
				{/loop}
			</p>
		</div>
		{/if}
		{if isset($LinksList)}
		<div class="col">
			<p class="link">
			邻居：
				{loop $LinksList $v} 
					<a href="{$v.url}" title="{$v.text}" target="_blank">{$v.text}</a>
				{/loop}
			</p>
		</div>
		{/if}
	</div>
	<div class="clear"></div>
</div>
</body>
</html>