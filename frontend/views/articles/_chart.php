<?php

/**
 * @var $this View
 * @var $series
 */

use frontend\components\View;
use miloschuman\highcharts\Highcharts;

//HDev::trace($series);
?>
<?=Highcharts::widget([
    'setupOptions' => [
        'global' => [
            'useUTC' => false
        ],
        'lang' => [
            'rangeSelectorFrom' => 'От',
            'rangeSelectorTo' => 'До',
            'rangeSelectorZoom' => 'Масштаб',
            'resetZoom' => 'Сбросить масштаб',
            'resetZoomTitle' => 'Сбросить масштаб к 1:1',
            'shortMonths' => array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),
            'weekdays' => array('Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'),
            'decimalPoint' => ',',
        ]
    ],
    'options' => [
        'tooltip' => array(
            'dateTimeLabelFormats' => ['day' => '%d.%m.%Y']
        ),
        'legend' => [
            'align' => 'right',
            'verticalAlign' => 'bottom',
        ],
        'chart' => [
            'spacing' => [10, 0, 0, 10],
            'zoomType' => false,
            'height' => 300,
            'resetZoomButton' => ['theme' => ['display' => 'none']]
        ],
        'title' => [
            'text' => null,
        ],
        'xAxis' => [
            'type' => 'datetime',
            'dateTimeLabelFormats' => ['day' => '%d.%m'],
            'maxPadding' => 0,
        ],
        'yAxis' => [
            'type' => 'liner',
//                    'minTickInterval' => 2.5,
            'title' => [
                'text' => null
            ],
            'labels' => [
                'format' => "{value}%",
                'formatter' => new \yii\web\JsExpression("function(){ return this.value.toString().replace('.', ',') }")
            ]
        ],
        'plotOptions' => array(
            'series' => array(
                'marker' => array(
                    'enabled'=>false,
                ),
                'states' => array(
                    'hover' => array(
                        'halo' => array(
                            'size' => 2,
                        ),
                    )
                )

            ),
        ),
        'series' => $series,
        'credits' => [
            'enabled' => false
        ]

    ],


])?>
<?
$this->registerJs(<<<JS
    var chart = $('[data-highcharts-chart]').highcharts();
    var link = $('a[data-currency]');

    $.each(chart.series, function(i){
        var line = this;
        line.hide();
        $.each(link, function(){
            var name = $(this).attr('data-currency');
            var current = $(this).parent('li').hasClass('curr_active');
            if(current && name == line.name){
                line.show();
            }
        });
    });

    link.click(function(){
        toggleLine(this);
    });

    function toggleLine(item){
        var name = $(item).attr('data-currency');
        $.each(chart.series, function(i){
            this.hide();
            if(this.name == name){
                this.show();
            }
        });
    }
JS
)
?>
