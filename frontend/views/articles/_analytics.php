<?php

/**
 * @var $this View
 */

use common\enums\Currency;
use common\enums\RatePeriod;
use frontend\assets\FilterAsset;
use frontend\components\View;
use frontend\models\Analytics;
use yii\helpers\Html;

FilterAsset::register($this);

$bestToday = Analytics::getBestToday(Currency::RUB, RatePeriod::YEAR);

$this->registerJs(<<<JS
    $(".analytics__head__period select").change(function(e) {
        updateBest();
        $("#chart-wrapper").load('/analitika/chart/?period='+$(this).val());
    });
    $(".curr__trigger_besttoday").click(function(e) {
        e.preventDefault();
        updateBest();
    });

    function updateBest()
    {
        var curr = $('.analytics__head .curr_active .curr__trigger_besttoday').data('currency');
        var period = $(".analytics__head__period select").val();
        $(".best-today").load('/analitika/best/?currency='+curr+'&period='+period);
    }
    $('.zoom .zoom-button').on('click', function(){
        var button = $(this);
        $('.zoom .zoom-button').removeClass('active');
        var range = function(max){
            var date = new Date();
            date.setTime(max);
            switch (button.attr('data-range')) {
                case 'week':
                    date.setDate(date.getDate() - 7);
                    break;
                case 'month':
                    date.setMonth(date.getMonth() - 1);
                    break;
                case 'quarter':
                    date.setMonth(date.getMonth() - 3);
                    break;
                case 'year':
                    date.setFullYear(date.getFullYear() - 1);
                    break;
                case 'all':
                    date = null;
                    break;
            }
            return date ? date.getTime() : null;
        };
        $.each(Highcharts.charts, function(){
            var self = this;
            var oldZoomOut = self.zoomOut;
            this.zoomOut = function newZoom(){
                $('.zoom .zoom-button').removeClass('active');
                ($.proxy(oldZoomOut, self))();
            };
            this.zoomOut(); //reset zoom and remove button
            var max = this.xAxis[0].max;
            var min = range(max)||this.xAxis[0].min;
            this.xAxis[0].setExtremes(min,max);
            //this.showResetZoom();
        });
        button.addClass('active');
    });
JS
)
?>
<div class="analytics">
    <div class="analytics__head">
        <label class="analytics__head__lbl analytics__head__lbl__first">Аналитика вкладов на</label>
        <div class="analytics__head__period">
            <?=Html::dropDownList('period', RatePeriod::YEAR, [0 => 'любой срок'] + RatePeriod::getList(), ['class' => 'styled'])?>
        </div><!--select time -->
        <label class="analytics__head__lbl">в валюте</label>
        <ul class="box-head__params list-currency float__clear">
            <? foreach (Currency::getValues() as $i => $currency): ?>
                <li data-currency="<?= $currency ?>"
                    class="curr no__margin <?= Currency::getClass($currency) . (($currency==Currency::RUB) ? ' curr_active' : ''); ?> ">
                    <a href="#" class="curr__trigger curr__trigger_besttoday" data-currency="<?=$currency?>"><?= Currency::getSymbol($currency); ?></a>
                </li>
            <? endforeach; ?>
        </ul><!--list-currency-->
    </div>
    <div class="analytics_best">
            <div class="box-head box-head_align_left box-head_underline">
                <h3 class="box-head__heading heading heading_lvl_2">Cтавки сегодня</h3>
            </div><!--box-head-->
        <div class="best-today">
        <?=$this->render('_bestToday', $bestToday);?>
        </div>
    </div>
    <div class="analytics-chart">
        <div class="box-head box-head_align_left box-head_underline">
            <h3 class="box-head__heading heading heading_lvl_2">Динамика средней ставки</h3>
        </div><!--box-head-->
        <div id="chart-wrapper"><?=$this->render('_chart', ['series' => Analytics::getChartData(RatePeriod::YEAR)])  ?></div>
        <div class="zoom">
            <div class="title" >Период: </div>
            <ul class="zoom-buttons">
                <li class="zoom-button" data-range="week">неделя</li>
                <li class="zoom-button" data-range="month">месяц</li>
                <li class="zoom-button" data-range="quarter">квартал</li>
                <li class="zoom-button active" data-range="all">все</li>
<!--                <li class="zoom-button" data-range="year">год</li>-->
            </ul>
        </div>

    </div>
</div>