<?php
/**
 * Created by PhpStorm.
 * User: kkurkin
 * Date: 4/20/15
 * Time: 5:36 PM
 */

namespace console\controllers;


use common\components\helpers\HStrings;
use common\enums\EventStatus;
use common\helpers\HDates;
use common\models\Category;
use common\models\Event;
use common\models\Format;
use common\models\Place;
use common\models\User;
use console\components\Controller;
use console\components\UConsoleCommand;
use Faker\Factory;

class EventsController  extends Controller
{

    public function actionGenerate($count = 1, $status = EventStatus::PUBLISHED)
    {
        $faker = Factory::create();

        $users = User::getPrivilegedList();
        $formats = Format::getList();
        $categories = Category::getList();
        $places = Place::getList();
        for($i = 0; $i<$count; $i++) {
            $event = new Event();
            $event->loadDefaultValues(true);
            $event->title = $faker->sentence(4);
            $event->slug = $faker->slug;
            $event->announcement = $faker->text(200);
            $event->description = $faker->text(1000);
            $dateStart = $faker->dateTimeBetween('now', '+1 mont');
            $event->start_time = $dateStart->format('Y-m-d H').':00:00';
            $dateStart->modify("+".rand(0, 3).' days'." +".rand(0, 4).' hours');
            $event->end_time = $dateStart->format('Y-m-d H').':00:00';
            $event->status = $status;
            $event->image_src = $faker->image(\Yii::getAlias('@upload/images/events'), 640, 480, 'nature', false);
            $event->author_id = array_rand($users);

            $event->format_id = array_rand($formats);
            $event->place_id = array_rand($places);
            $event->category_ids = [array_rand($categories)];

            $event->in_top = rand(0, 3) ? false : true;
            if (!$event->free = rand(0, 1) ? false : true) {
                $event->price_min = rand(1, 30) * 100;
            }
            $event->url = $faker->url;

            if (!$event->save()) {
                $this->logSaveError($event);
            } else {
                $event->updateAttributes(['published_at' => HDates::long("-1 year")]);
            }
            $this->log($event->attributes);
        }
        $this->log("$count events generated");
        $this->endProfile();
    }

    private function getRandomText($fullText, $parCount = 1)
    {
        $pars = HStrings::parseItems($fullText, "\r");
        shuffle($pars);
        return "<p>".implode("</p>\n<p>", array_slice($pars, 0, $parCount))."</p>";
    }

    public function actionTest()
    {

    }

}