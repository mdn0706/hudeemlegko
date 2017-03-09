<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Article;
use app\modules\admin\models\Tag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Article::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Article();

        if ($model->load(Yii::$app->request->post())) {
            $model->text_preview = $model->anons($model->text);
            $model->save();

            Yii::$app->session->setFlash('success', "статья {$model->title} добавлена");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->text_preview = $model->anons($model->text);
            $model->save();
            $model->image = UploadedFile::getInstance($model, 'image');

            if ($model->image) {
                $model->upload();
            }

            unset($model->image);
            $model->gallery = UploadedFile::getInstances($model, 'gallery');

            if ($model->gallery) {
                $model->uploadGallery();
            }

            Yii::$app->session->setFlash('success', "статья {$model->title} отредактирована");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "статья {$model->title} удалена");

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionSetTags($id){
        
        $article = $this->findModel($id);
        
        $selectedTags = $article->getSelectedTags();
                        
        $tags = ArrayHelper::map(Tag::find()->all(), 'id', 'title');
        
        if(Yii::$app->request->isPost)
        {
            $tags = Yii::$app->request->post('tags');
            
            $article->saveTags($tags);
            return $this->redirect(['view', 'id' => $article->id]);
        }
        
        return $this->render('tags', compact('selectedTags', 'tags'));
        
    }  
    

}
