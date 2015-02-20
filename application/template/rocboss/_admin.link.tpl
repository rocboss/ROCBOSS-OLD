<section>
    <div class="body">
        <!--{if $type == 'link'}-->
        <h2>链接管理</h2>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
            <th class="text-center" width="80">排序</th>
            <th class="text-center">链接名称</td>
            <th class="text-center">链接地址</td>
            <th class="text-center" width="80">操作</td>
            </tr>
            </thead>
            <tbody>
            <!--{loop $LinksList $link}-->
                <tr>
                    <td align="center"><!--{$link['position']}--></td>
                    <td align="center">
                        <!--{$link['text']}-->
                    </td>
                    <td align="center">
                        <!--{$link['url']}-->
                    </td>
                    <td align="center">
                        <a href="<!--{ROOT}-->admin/index/type/edit_link/position/<!--{$link['position']}-->/" title="">    编辑
                        </a>
                        <a href="<!--{ROOT}-->manage/del_link/position/<!--{$link['position']}-->/" title="">
                            删除
                        </a>
                    </td>
                </tr>
            <!--{/loop}-->
            </tbody>
            <tfoot>
                <tr>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center"></th>
                <th class="text-center">
                    <a class="btn btn-primary" href="<!--{ROOT}-->admin/index/type/add_link/">
                        添加链接
                    </a>
                </th>
                </tr>
            </tfoot>
        </table>
        <!--{/if}-->
        <!--{if $type == 'edit_link'}-->
        <h2>链接编辑 - <!--{$link['text']}--></h2>
        <form action="<!--{ROOT}-->manage/edit_link/" method="post">
        <table width="100%" class="form-table">
            <tr>
            <td class="input-name">链接排序：</td>
            <td><input type="text" size="50" name="position" value="<!--{$link['position']}-->" class="input"/> 正整数，切勿重复</td>
            </tr>
            <tr>
            <td class="input-name">链接名称：</td>
            <td><input type="text" size="50" name="text" value="<!--{$link['text']}-->" class="input"/></td>
            </tr>
            <tr>
            <td class="input-name">链接地址：</td>
            <td><input type="text" size="50" name="url" value="<!--{$link['url']}-->" class="input"/> 注意加http://</td>
            </tr>
            <tr>
            <td align="right" height="40">&nbsp;</td>
            <td>
            <input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
            </td>
            </tr>
        </table>
        </form>
        <!--{/if}-->
        <!--{if $type == 'add_link'}-->
        <h2>添加链接</h2>
        <form action="<!--{ROOT}-->manage/edit_link/" method="post">
        <table width="100%" class="form-table">
            <tr>
            <td class="input-name">链接排序：</td>
            <td><input type="text" size="50" name="position" value="" class="input"/> 正整数，切勿重复</td>
            </tr>
            <tr>
            <td class="input-name">链接名称：</td>
            <td><input type="text" size="50" name="text" value="" class="input"/></td>
            </tr>
            <tr>
            <td class="input-name">链接地址：</td>
            <td><input type="text" size="50" name="url" value="" class="input"/> 注意加http://</td>
            </tr>
            <tr>
            <td align="right" height="40">&nbsp;</td>
            <td>
            <input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
            </td>
            </tr>
        </table>
        </form>
        <!--{/if}-->
    </div>
</section>