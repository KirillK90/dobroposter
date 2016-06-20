<?php

namespace common\models;

/**
 * Интерфейс для подстановки сео параметров
 * Interface SubstitutionsInterface
 * @package common\models
 *
 * @property $name string
 */
interface SubstitutionsInterface
{
    public function getSubstitutions();
}