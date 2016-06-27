<?php

/**
 * @var $this View
 */

use common\components\helpers\HStrings;
use common\enums\Currency;
use common\helpers\HDates;
use frontend\components\View;
use frontend\models\Analytics;
use yii\helpers\Html;
use yii\helpers\Url;

$maxRate = Analytics::getMaxRateMonthAgo(Currency::RUB);
$currDate = HDates::short();
$startDate =  date("Y-m", strtotime("today -1 month"))."-01";
$endDate = date("Y-m", strtotime("today"))."-01";
$startYearDate = HDates::short('-12 month');
?>

<div class="monitoring">
    <div class="monitoring__head">
        <h1 class="monitoring__title">Монитор сохранности моих накоплений</h1>
    </div>
    <div class="monitoring__filter">
        <div class="filter-item box-filter__item">
            <label class="filter-item__lbl">Сумма накоплений</label>
              <span class="price-selector">
              <?=Html::textInput('amount', 50000, ['id' => 'minCost', 'class' => 'input input_size_m input_type_text price-selector__input', 'placeHolder'=>'Любая', 'data-min'=>10000])?>
                  <div class="price-selector__slider disable__autostart" id="sliderPrice"></div>
              </span>
            <span class="monitoring__filter__curr curr_rouble">
                <label for="RUB" class="monitoring__curr__trigger">у</label>
            </span>
        </div><!--filter-item-->
    </div>
    <div class="monitoring__body">
        <div class="monitoring__body__head">
            <h1 class="monitoring__body__title">Что произошло с накоплениями за 1 месяц</h1>
        </div>
        <div class="monitoring__body__content">
            <div class="monitoring__body__content__left">
                <div class="monitoring__body__img">
                    <img src="<?= Yii::getAlias("@static/images/monitoring/pot.png") ?>">
                </div>
                <div class="monitoring__body__content__block">
                    <div class="monitoring__body__content__title">
                        Храню дома
                    </div>
                    <div class="monitoring__body__content__sum monitoring__sum__danger">
                        -&nbsp;<span class="monitoring__sum">0</span>
                        <span class="monitoring__content__curr curr_rouble">
                            <label for="RUB" class="monitoring__curr__trigger monitoring__curr__trigger__serif">у</label>
                        </span>
                    </div>
                    <div class="monitoring__body__content__text">
                        За месяц съела инфляция, <br /> которая  в этом году  <br />составляет <span class="monitoring__rate__danger">0</span>
                    </div>
                </div>
            </div>
            <div class="monitoring__body__content__right">
                <div class="monitoring__body__img">
                    <img src="<?= Yii::getAlias("@static/images/monitoring/bank.png") ?>">
                </div>
                <div class="monitoring__body__content__block">
                    <div class="monitoring__body__content__title">
                        Вложил во вклад
                    </div>
                    <div class="monitoring__body__content__sum  monitoring__sum__success">
                        +&nbsp;<span class="monitoring__sum">0</span>
                        <span class="monitoring__content__curr curr_rouble">
                            <label for="RUB" class="monitoring__curr__trigger monitoring__curr__trigger__serif">у</label>
                        </span>
                    </div>
                    <div class="monitoring__body__content__text">
                        Если бы вы открыли вклад с максимальной ставкой <?=HStrings::rate($maxRate)?> <br />месяц назад
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="monitoring__footer">
        <a class="monitoring__footer__button button button_size_small button_type_link" rel="nofollow"  href="<?=Url::to(['/deposits/search', 'regionSlug' => $this->getRegionSlug()])?>">Подобрать вклад сейчас</a>
    </div>
</div>
<?php $this->registerJs(<<<JS

    (function($){
        var maxRate = {$maxRate};
        var startDate = '{$startDate}';
        var startYearDate = '{$startYearDate}';
        var endDate = '{$endDate}';
        var currDate = '{$currDate}';
        var apiUrl = 'https://www.statbureau.org/calculate-inflation-price-jsonp?jsoncallback=?';
        var apiUrlRate = 'https://www.statbureau.org/calculate-inflation-rate-jsonp?jsoncallback=?';

        function getCalcInflation(sum, target){
            $.ajax({
                url:apiUrl,
                dataType: 'jsonp',
                data: {
                    country: 'russia',
                    start: startDate,
                    end: endDate,
                    amount: sum,
                    format: false
                },
                success:function(data){
                    var val = data || 0;
                    if(val >0){
                        val -= sum;
                    }
                    $(target).text(format(Math.round(val)));
                }
            });
        }

        function updateMonitoring(value){
            var sum = parseInt(value.replace(/\s+/g, ""));
            var safe = sum * maxRate * 30/365/100;
            getCalcInflation(sum, $('.monitoring__sum__danger .monitoring__sum'));
            $('.monitoring__sum__success .monitoring__sum').text(format(Math.round(safe||0)));
        }

        $.getJSON(apiUrlRate, {
            country: 'russia',
            start: startYearDate,
            end: currDate,
            format: true
        }).done(function (data) {
            $('.monitoring__rate__danger').text(data + '%');
        });

        $.each($('.monitoring__filter'),function(i){
            var parent = $(this);
            var input = parent.find('.price-selector__input');
            var slider = parent.find('.price-selector__slider');
            slider.slider({
                range: 'min',
                step: input.data('step') || 1000,
                min: input.data('min') || 20000,
                max: input.data('max') || 1500000,
                value: toNum(input.data('val')) || 100000,
                slide: function(e, ui) {
                    $(ui.handle).trigger('price-selector.update.slide', format(ui.value));
                },
                change: function(e, ui) {
                    $(ui.handle).trigger('price-selector.update', format(ui.value));
                }
            });

            var timer;
            parent.on('price-selector.update.monitoring', function(e, newval){
                clearTimeout(timer);
                setTimeout(function(){
                    updateMonitoring(newval);
                },300);
            }).trigger('price-selector.update.monitoring', input.val());
        });

    })(jQuery)

JS
)
?>