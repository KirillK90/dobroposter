<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\ArticleFilter;
use backend\models\NewPageForm;
use common\enums\ArticleStatus;
use common\enums\ArticleType;
use common\enums\UserRole;
use common\models\Article;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\ActiveForm;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 *  Articles controller
 */
class ArticlesController extends Controller
{

    public $allowedRoles = [UserRole::ADMIN, UserRole::MODERATOR, UserRole::EDITOR];

    public function actionIndex($type)
    {
        if (!in_array($type, ArticleType::getValues())) {
            throw new BadRequestHttpException("Unknown type: $type");
        }
        $filter = new ArticleFilter();
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
                $this->redirect(['/articles/create', 'type' => $model->type]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }

        return $this->render('new', ['model' => $model]);
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $article = new Article() ;
        $article->setScenario($article::SCENARIO_UPLOAD);
        $article->load(Yii::$app->request->post());
        $article->uploadedImage = UploadedFile::getInstance($article, 'uploadedImage');

        if ($article->upload()) {
            return ['result' => true, 'src' => $article->getImageSrc(), 'filename' => $article->image_src];
        }
        return ['result' => false, 'message' => reset($article->getErrors())];
    }

    public function actionCreate($type)
    {
        if (!in_array($type, ArticleType::getValues())) {
            throw new BadRequestHttpException("Unknown type: $type");
        }

        $model = new Article();
        $model->loadDefaultValues(true, $type);
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $this->setFlash('success', ACTION_CREATE_SUCCESS);
                $this->redirect(['/articles/update', 'id' => $model->id]);
            } else {
                $this->setFlash('error', ACTION_VALIDATE_ERROR);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findArticle($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if (isset($_POST['publish'])) {
                $model->status = ArticleStatus::PUBLISHED;
            }
            if (isset($_POST['unpublish'])) {
                $model->status = ArticleStatus::UNPUBLISHED;
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
        $model = $this->findArticle($id);
        if ($model->delete()) {
            $this->setFlash('success', ACTION_DELETE_SUCCESS);
        } else {
            $this->setFlash('error', ACTION_VALIDATE_ERROR);
        }

        return $this->redirect(['/articles/index', 'type' => $model->type]);
    }

    /**
     * @param $id
     * @return Article
     * @throws NotSupportedException
     */
    protected function findArticle($id)
    {
        if ($model = Article::findOne($id)) {
            return $model;
        }
        throw new NotSupportedException('Материал не найден.');
    }

}
