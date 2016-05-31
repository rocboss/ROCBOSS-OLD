{$headerLayout}
    <div class="row wrapper border-bottom navy-bg page-heading">
        <div class="col-lg-12">
            <ol class="breadcrumb">
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="ibox-content m-b-sm border-bottom">
            <div class="p-xs text-danger">
                <i class="fa fa-search"></i> {if !empty($error)}{$error}{else}{$data.q}{/if}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div id="topic-list" class="ibox-content" style="border-top: none;">
                        <div class="feed-activity-list" v-lazyload="topics">

                            {loop $data['rows'] $topic}
                                <div class="feed-element">
                                    <a href="/user/{$topic.uid}" class="pull-left">
                                        <img class="topic-avatar" src="{$topic.avatar}">
                                    </a>
                                    <div class="media-body ">
                                        <div class="topic-status pull-right">
                                            <div class="c-ico">
                                                <i class="fa fa-commenting"></i>
                                            </div>
                                            <div class="c-num">
                                                {$topic.comment_num}
                                            </div>
                                        </div>
                                        <strong>
                                            <a href="/read/{$topic.tid}" class="topic-title">
                                                {:str_replace($data['q'], '<span class="text-danger">'.$data['q'].'</span>', $topic['title'])}
                                            </a>
                                        </strong>
                                        <p class="topic-info">
                                            <small class="text-muted">
                                                <i v-if="topic.imageCount > 0" class="fa fa-camera"></i>
                                                <span class="topic-username">{$topic.username}</span>
                                                {$topic.post_time}
                                            </small>
                                            {if $topic['location'] != ''}
                                                <small class="text-muted" style="margin-left: 10px;">
                                                    <i class="fa fa-map-marker"></i>
                                                    {$topic.location}
                                                </small>
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                            {/loop}

                            <div id="pagination" class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$footerLayout}

    <script type="text/javascript">
        seajs.use("index", function(index) {
            index.search({$data.page}, {$data.per}, {$data.total}, '{$data.q}');
        });
    </script>
</body>
</html>
