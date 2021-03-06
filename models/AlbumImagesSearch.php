<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AlbumImagesSearch represents the model behind the search form about `app\models\AlbumImages`.
 */
class AlbumImagesSearch extends AlbumImages
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'album_id'], 'integer'],
            [['image', 'created_at'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $queryImage = AlbumImages::find();

        $dataProviderIndex = new ActiveDataProvider([
            'query' => $queryImage,]);

        $this->attributes = $params;

        if (!$this->validate()) {
            $queryImage->where('0=1');
            return $dataProviderIndex;
        }
        if ($params['status'] == null) {
            $queryImage->andFilterWhere([
                'album_id' => $this->id,
                'created_at' => $this->created_at,
            ]);
            $queryImage->andFilterWhere([
                'like', 'image', $this->image]);

            return $dataProviderIndex;
        }

        if (([$params['status']] !== null))
            $status = $params['status'];
        /*
        {
            $this->status = $params['status'];
            $queryImage = \app\models\AlbumImages::find()
                ->with(['resizedPhotos' => function($q) {
                            $q->andWhere(['status' => $this->status]);
                            $q->select(['image_id', 'status']);}
                       ])
                ->asArray();
            $dataProviderStatusIndex = new ActiveDataProvider(['query' => $queryImage]);
               return $dataProviderStatusIndex;
        }
       */
        {
            $queryImage = AlbumImages::findBySql
            ("SELECT DISTINCT album_images.image, album_images.id, resized_photos.status 
                                   FROM album_images, resized_photos 
                                   WHERE album_images.id = resized_photos.image_id 
                                   AND resized_photos.status = '" . $status . "'")
                ->asArray();

            $dataProviderStatusIndex = new ActiveDataProvider(['query' => $queryImage]);
            return $dataProviderStatusIndex;
        }
    }


}
