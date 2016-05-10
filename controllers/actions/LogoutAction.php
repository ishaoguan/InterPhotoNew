<?php

namespace app\controllers\actions;


use Yii;
use yii\web\ServerErrorHttpException;
use yii\rest\Action;
use yii\web\UnprocessableEntityHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;


class LogoutAction extends \yii\rest\Action
{
 
    public $typeAuth = 'Bearer ';
    public $realm = 'api';

    public function run()
    {    
        $matches = array();
        $tokenHeader = \Yii::$app->request->headers['authorization'];
        if (!preg_match_all("/Bearer\s.{32}/", $tokenHeader, $matches))
            throw new BadRequestHttpException('Incorrect token', 400);

        $token = substr_replace($matches[0][0], '',0, strlen($this->typeAuth));
    
        $authModel=$this->modelClass;
    
        if (!$authUser = $authModel::deleteToken($token))
            throw new NotFoundHttpException('There is not such token', 404);
    
        $this->setAuthenticateHeader();
    }

    public function setAuthenticateHeader()
        {
            $response = \Yii::$app->response;
            $response->getHeaders()->set('WWW-Authenticate', "Basic realm=\"{$this->realm}\"");
        }    
}
