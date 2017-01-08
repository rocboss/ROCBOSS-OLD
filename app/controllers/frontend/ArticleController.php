<?php
namespace frontend;

use Roc;
use UserModel;
use ArticleModel;
use AttachmentModel;
class ArticleController extends BaseController
{
    public static $per = 12;

    /**
     * 文章列表
     * @method index
     * @param  integer $page [description]
     * @return [type]        [description]
     */
    public static function index($page = 1)
    {
        $page = parent::getNumVal($page, 1, true);
        $rows = ArticleModel::m()->getList(($page - 1) * self::$per, self::$per);

        foreach ($rows as &$article)  {
            $article['title'] = Roc::filter()->topicOut($article['title'], true);
            $article['content'] = Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($article['content']), 128);
            $article['post_time'] = parent::formatTime($article['post_time']);
            $article['poster'] = AttachmentModel::m()->getAttachment($article['poster_id'], $article['uid'], '90x68');
        }

        parent::renderBase(['active' => 'article']);
        Roc::render('article', [
            'data' => [
                'rows' => $rows,
                'page' => $page,
                'per' => self::$per,
                'total' => ArticleModel::m()->getTotal(['valid' => 1, 'is_open' => 1]),
            ]
        ]);
    }

    /**
     * 阅读文章详情
     * @method read
     * @param  [type]    $id [description]
     * @return [type]        [description]
     */
    public static function read($id)
    {
        $id = parent::getNumVal($id, 0, true);
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];

        $data = ArticleModel::m()->getDetail($id);

        if (!empty($data)) {
            $data['title'] = Roc::filter()->topicOut($data['title']);
            $data['content'] = Roc::filter()->topicOut($data['content']);
            $data['post_time'] = parent::formatTime($data['post_time']);
            $data['avatar'] = Roc::controller('frontend\User')->getAvatar($data['uid']);
            // 文章赞
            $data['praise'] = [
                'rows' => ArticleModel::m()->getPraiseList($data['id']),
                'hasPraise' => ArticleModel::m()->getPraiseDetail($data['id'], $uid) > 0 ? true : false
            ];

            foreach ($data['praise']['rows'] as &$praise) {
                $praise['avatar'] = Roc::controller('frontend\User')->getAvatar($praise['uid']);
            }

            // 主题是否收藏
            $data['hasCollection'] = ArticleModel::m()->getCollectionDetail($data['id'], $uid) > 0 ? true : false;
        }

        parent::renderBase([
            'asset' => 'article_read',
            'active' => 'article',
            'pageTitle' => (!empty($data) ? $data['title'] : ''),
            'keywords' =>  (!empty($data) ? $data['title'] : ''),
            'description' => (!empty($data) ? strip_tags(Roc::controller('frontend\Index')->cutSubstr(Roc::filter()->topicOut($data['content']), 128)) : ''),
        ]);

        Roc::render('article_read', [
            'data' => $data
        ]);
    }

    /**
     * 发布新文章
     * @return [type] [description]
     */
    public static function newArticle()
    {
        $uid = Roc::controller('frontend\User')->getloginInfo()['uid'];
        if (!UserModel::m()->checkIsBanned($uid)) {
            $key = static::getGuid($uid);
            $data = [
                // 头像上传Token
                'uploadToken' => Roc::qiniu()->uploadToken([
                    'scope' => Roc::get('qiniu.bucket').':article/'.$key,
                    'deadline' => time() + 3600,
                    'saveKey' => 'article/'.$key
                ]),
                'saveKey' => 'article/'.$key
            ];

            parent::renderBase(['asset' => 'new_article', 'active' => 'newArticle', 'pageTitle' => '文章投稿']);
            Roc::render('new_article', [
                'data' => $data
            ]);
        } else {
            Roc::redirect('/login');
        }
    }
}
