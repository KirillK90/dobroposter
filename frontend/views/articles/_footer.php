<?php
/**
 * @var $this View
 * @var $model Article
 */

use common\enums\Block;
use common\models\Article;
use frontend\components\View;
use yii\web\JqueryAsset;

if ($model->likes_enabled) {
    $this->registerJsFile('@static/js/social-likes.min.js', ['depends' => [JqueryAsset::className()]]);
}

$vkAppId = Yii::$app->params['vk.api_id'];
$fbAppId = Yii::$app->params['fb.api_id'];

if ($vkAppId && $model->comments_enabled) {
    $this->registerJsFile('//vk.com/js/api/openapi.js?117',['position'=>View::POS_HEAD]);
    $this->registerJs('VK.init({apiId: '.$vkAppId.', onlyWidgets: true});',View::POS_HEAD);
}
?>
<? if ($model->comments_enabled && $fbAppId): ?>
<? $this->beginBlock(Block::BODY_BEGIN) ?>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?=$fbAppId?>',
            xfbml      : true,
            version    : 'v2.4'
        });
    };
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.4&appId=<?=$fbAppId?>";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<? $this->endBlock() ?>
<? endif; ?>
<div class="post__footer">
    <div class="post__share">
        <div class="social-share social-share_theme_solid social-share_left social-share_layout_hor"><!--SHARE block -->
            <? if ($model->likes_enabled): ?>
                <div class="social-likes">
                    <div class="facebook" title="Поделиться ссылкой на Фейсбуке"></div>
                    <div class="vkontakte" title="Поделиться ссылкой во Вконтакте"></div>
                    <div class="twitter" title="Поделиться ссылкой в Твиттере"></div>
                </div>
            <? endif; ?>
            <a href="#" class="share share_service_print share_size_m">
                <i class="icon icon_share_print share__icon"></i>
            </a><!--share-->
        </div><!--social-share-->
        <? if ($model->comments_enabled && ($vkAppId || $fbAppId)): ?>
        <div class="social-share social-share_theme_solid social-share_right">
            <div class="share share_comments comments-count" data-comment-block-id="social__comment_block_0">
                <i class="post-counter__icon icon comments-count__placeholder icon_post_comments icon_cs_yellow icon_size_s"></i>
                <div id="comments-count" class="comments-count__text">
                    <span class="post-comments__count comments-count__count"></span>
                    <span>Комментарии</span>
                </div>
                <i class="icon icon_arrow_down icon_cs_white icon_size_s comments-count__icon"></i>
            </div><!--comments-count-->
        </div>
        <? endif; ?>
    </div>
</div><!--post__footer-->
<? if ($model->comments_enabled && ($vkAppId || $fbAppId)): ?>
<div class="social__tabs" id="social__comment_block_0">
    <ul>
        <? if ($vkAppId): ?>
            <li>Вконтакте</li>
        <? endif; ?>
        <? if ($fbAppId): ?>
            <li>Facebook</li>
        <? endif; ?>
    </ul>
    <div>
        <? if ($vkAppId): ?>
            <div>
                <div id="vk_comments"></div>
                <script type="text/javascript">
                    VK.Widgets.Comments("vk_comments", {limit: 10, width: "770", attach: "*"});
                    VK.Api.call('widgets.getComments',
                        {widget_api_id: <?=$vkAppId?>, url: "<?=Yii::$app->request->getAbsoluteUrl()?>"},
                        function(obj) {
                            updateCommentsAmount('vk',obj.response.count);
                        });
                    VK.Observer.subscribe('widgets.comments.new_comment',function(amount,comment,datetime,sign){
                        updateCommentsAmount('vk',amount);
                    });
                    VK.Observer.subscribe('widgets.comments.delete_comment',function(a,b,c,d){
                        updateCommentsAmount('vk',amount);
                    });
                </script>
            </div>
        <? endif; ?>
        <? if ($fbAppId): ?>
        <div>
            <div class="fb-comments" data-href="<?=Yii::$app->request->getAbsoluteUrl()?>" data-width="770" data-numposts="10"></div>
        </div>
        <? endif; ?>
    </div>
</div>
<? endif; ?>