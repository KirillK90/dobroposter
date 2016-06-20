<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 03.07.15
 * Time: 20:39
 */

namespace frontend\components;

use Closure;
use Yii;

class GridView extends \yii\grid\GridView
{

    public $renderTableRow;

    public $renderTableBody;

    public $renderTableHeader;

    public $renderPager;

    public $renderItems;

    public $renderSummary;

    public $renderTableFooter;

    public $layout = "{summary}\n{items}";

    public function renderTableRow($model, $key, $index)
    {
        if ($this->renderTableRow instanceof Closure) {
            $cells = [];
            foreach ($this->columns as $column) {
                $cells[] = $column->grid->formatter->format($column->getDataCellValue($model, $key, $index), $column->format);
            }
            return call_user_func($this->renderTableRow, $cells, $model, $this);
        } else {
            return parent::renderTableRow($model, $key, $index);
        }
    }

    public function renderTableBody()
    {
        if ($this->renderTableBody instanceof Closure) {
            return call_user_func($this->renderTableBody, $this);
        } else {
            return parent::renderTableBody();
        }
    }

    public function renderPager()
    {
        if ($this->renderPager instanceof Closure) {
            return call_user_func($this->renderPager, $this);
        } else {
            return parent::renderPager();
        }
    }

    public function renderSummary(){
        if ($this->renderSummary instanceof Closure) {
            return call_user_func($this->renderSummary, $this);
        } else {
            return parent::renderSummary();
        }
    }

    public function renderTableHeader()
    {
        if ($this->renderTableHeader instanceof Closure) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->label;
            }
            return call_user_func($this->renderTableHeader, $cells, $this);
        } else {
            return parent::renderTableHeader();
        }
    }

    public function renderTableFooter()
    {
        if ($this->renderTableFooter instanceof Closure) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->footer;
            }
            return call_user_func($this->renderTableFooter, $cells, $this);
        } else {
            return parent::renderTableFooter();
        }
    }

    public function renderItems()
    {
        if ($this->renderItems instanceof Closure) {

            $caption = $this->renderCaption();
            $columnGroup = $this->renderColumnGroup();
            $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
            $tableBody = $this->renderTableBody();
            $tableFooter = $this->showFooter ? $this->renderTableFooter() : false;

            return call_user_func($this->renderItems, $caption, $columnGroup, $tableHeader, $tableFooter, $tableBody, $this);
        } else {
            return parent::renderItems();
        }
    }

}