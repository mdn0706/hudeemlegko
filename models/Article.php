<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;


/**
 * Description of Article
 *
 * @author kafa
 */
class Article extends ActiveRecord{
    
    public function behaviors()
    {
        return [
            'image' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }
    
    public static function tableName(){
        return 'article';
    }
    
    public function getCategory(){
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    
    
    
    public function getTags(){
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('article-tag', ['article_id' => 'id']);
    }
    
    public function getSelectedTags(){
        
        $selectedTags = $this->getTags()->select('id')->asArray()->all();
        
        debug($selectedTags);
        
    }
    
}
