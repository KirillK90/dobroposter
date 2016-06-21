<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\EventFilter;
use backend\models\NewPageForm;
use common\enums\EventStatus;
use common\enums\EventType;
use common\enums\UserRole;
use common\models\Event;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 *  Events controller
 */
class EventsController extends Controller
{

    public $allowedRoles = [UserRole::ADMIN, UserRole::MODERATOR, UserRole::EDITOR];

    public function actionIndex($type)
    {
        if (!in_array($type, EventType::getValues())) {
            throw new BadRequestHttpException("Unknown type: $type");
        }
        $filter = new EventFilter();
        $filter->type = $type;
        $filter->load(Yii::$app->request->get());
        return $this->render('index', compact('filter'));
    }

    public function actionNew()
    {
        $model = new NewPageForm();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->validate()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                $this->redirect(['/events/create', 'type' => $model->type]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('new', ['model' => $model]);
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $event = new Event() ;
        $event->setScenario($event::SCENARIO_UPLOAD);
        $event->load(Yii::$app->request->post());
        $event->uploadedImage = UploadedFile::getInstance($event, 'uploadedImage');

        if ($event->upload()) {
            return ['result' => true, 'src' => $event->getImageSrc(), 'filename' => $event->image_src];
        }
        return ['result' => false, 'message' => reset($event->getErrors())];
    }

    public function actionCreate($type)
    {
        if (!in_array($type, EventType::getValues())) {
            throw new BadRequestHttpException("Unknown type: $type");
        }

        $model = new Event();
        $model->loadDefaultValues(true, $type);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                $this->redirect(['/events/update', 'id' => $model->id]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findEvent($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if (isset($_POST['publish'])) {
                $model->status = EventStatus::PUBLISHED;
            }
            if (isset($_POST['unpublish'])) {
                $model->status = EventStatus::UNPUBLISHED;
            }
            if (isset($_POST['preview']) && $model->savePreview()) {
                $model->preview = true;
                $this->setFlash('success', "Режим предпросмотра");
            } elseif (!isset($_POST['preview']) && $model->save()) {
                if (isset($_POST['publish'])) {
                    $this->setFlash('info', "Материал опубликован");
                }
                $this->setFlash('success', ACTION_UPDATE_SUCCESS);
                $model->refresh();
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findEvent($id);
        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(['/events/index', 'type' => $model->type]);
    }

    /**
     * @param $id
     * @return Event
     * @throws NotSupportedException
     */
    protected function findEvent($id)
    {
        if ($model = Event::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Материал не найден.');
    }

}
