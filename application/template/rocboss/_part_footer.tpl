<div class="footer" id="footer">
	<p>
	由 <a href="https://www.rocboss.com" target="_blank">ROCBOSS <!--{$GLOBALS['sys_config']['version']}--></a> 强力驱动，联系我们 : admin@rocboss.com
	</p>
	<!--{if isset($LinksList) && Utils::getClient() == ''}-->
	<p class="link">
	邻居：
		<a href="https://www.rocboss.com" target="_blank">ROCBOSS</a>
		<!--{loop $LinksList $v}--> 
		<span class="slant"> | </span> <a href="<!--{$v['url']}-->" target="_blank"><!--{$v['text']}--></a> 
		<!--{/loop}-->
	</p>
	<!--{/if}-->
</div>
<div class="alert-messages">
	<div class="message">
		<span class="message-text"></span>
	</div>
</div>
</body>
</html>