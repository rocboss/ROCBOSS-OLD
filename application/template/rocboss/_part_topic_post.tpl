<!--{if $loginInfo['uid'] > 0 }-->
    <div id="post-newtopic">
        <form id="talk-add" class="add-post">
            <fieldset>
                <div id="tagInput" class="textarea">
                    <div class="text-tag">
                        <div id="tags" class="show-tag"></div>
                        <div class="clear"></div>
                        <input type="text" class="form-tag-input" placeholder="（可选）请输入标签并回车显示，最多支持5个标签，点击标签以删除"/>
                        <input type="text" id="final" style="display: none;"/>
                    </div>
                </div>
                <div class="text-title">
                    <input type="text" name="title" id="title" class="form-input" placeholder="（可选）请输入简要标题，若无系统将自动摘要"/>
                </div>
                <div class="textarea">
                    <textarea id="subject" name="subject" rows="6" placeholder="（必选）请输入详细正文，插入图片时请注意换行"></textarea>
                </div>
                <input type="text" name="tempTid" id="tempTid" value="" style="display:none;"/>
                <input type="text" name="pictureString" id="pictureString" value="" style="display:none;"/>
                <a class="right btn btn-default" id="create" href="javascript:postNewTopic();" rel="nofollow">创建</a>
                
                <div class="upload-image">
                    <i class="icon icon-camerafill x6"></i>
                    <input type="file" name="upfile" id="post-pictures-file" accept="image/*" style="opacity: 0; left: 0;top: 0;bottom: 0;margin: 0; position: absolute; width: 35px;" />
                </div>
                <div class="clear"></div>
                <div class="showLine"></div>
            </fieldset>
        </form>
    </div>
<div class="clear"></div>
<script src="<!--{ROOT}-->application/template/rocboss/js/post.js"></script>
<!--{/if}-->