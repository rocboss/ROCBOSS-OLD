<?php
namespace backend;

use Roc;
use ArticleModel;
use AttachmentModel;

class ArticleController extends BaseController
{
    public static $per = 12;

    /**
     * 文章
     * @method articles
     * @param  [type]   $page [description]
     * @return [type]         [description]
     */
    public static function articles($page)
    {
        parent::__checkManagePrivate(true);

        $page = parent::getNumVal($page, 1, true);

        $articles = ArticleModel::m()->getList(($page - 1) * self::$per, self::$per, 0, true);
        foreach ($articles as &$article) {
            $article['title'] = Roc::filter()->topicOut($article['title'], true);
            $article['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($article['content']), 128);
            $article['post_time'] = parent::formatTime($article['post_time']);
            $article['poster'] = AttachmentModel::m()->getAttachment($article['poster_id'], $article['uid'], '90x68');
        }

        $total = ArticleModel::m()->getTotal(['valid' => 1]);

        parent::renderBase(['active' => 'articles']);
        Roc::render('admin/articles', [
            'articles' => [
                'rows' => $articles,
                'per' => self::$per,
                'page' => $page,
                'total' => $total,
            ],
            'count' => $total
        ]);
    }

    /**
     * 预览文章详情
     * @method preview
     * @param  [type]  $id [description]
     * @return [type]      [description]
     */
    public static function preview($id)
    {
        $article = ArticleModel::m()->getDetail($id);

        if (!empty($article)) {
            echo json_encode(['status' => 'success', 'data' => Roc::filter()->topicOut($article['content'])]);
        }
    }

    /**
     * 审核文章
     * @method doReview
     * @return [type]   [description]
     */
    public static function doReview()
    {
        parent::csrfCheck();

        $id = intval(Roc::request()->data->id);
        $isOpen = intval(Roc::request()->data->is_open);

        $ret = ArticleModel::m()->updateArticle($id, [
            'is_open' => $isOpen
        ]);

        if ($ret > 0) {
            echo json_encode(['status' => 'success', 'data' => '操作成功']);
        } else {
            echo json_encode(['status' => 'error', 'data' => '操作失败']);
        }
    }

    /**
     * 删除文章
     * @method delete
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function delete($id)
    {
        parent::csrfCheck();

        $ret = ArticleModel::m()->updateArticle(intval($id), [
            'valid' => 0
        ]);

        if ($ret > 0) {
            echo json_encode(['status' => 'success', 'data' => '删除成功']);
        } else {
            echo json_encode(['status' => 'error', 'data' => '删除失败']);
        }
    }
}
