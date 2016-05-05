<?php

namespace data;

use Yii;
use yii\web\IdentityInterface;
use app\controllers\auth\RbacController;
use app\models\auth\AuthItem;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

class Users extends \yii\db\ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['role', 'name', 'email', 'password'], 'required'],
            [['role'], 'string'],
            [['modified_at', 'created_at'], 'safe'],
           // [['access_token'], 'string', 'max' => 100],
            [['name', 'email', 'password'], 'string', 'max' => 50],
            [['email'], 'email'],
            [['phone'], 'string', 'max' => 15],
            //[['auth_key'], 'string', 'max' => 32],
            //[['password_hash'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

public function fields()
{
    $fields = parent::fields();
    // unset unsafely fields
    unset($fields['auth_key'], $fields['password_hash'], $fields['access_token'], $fields['password']);
        return $fields;
}

public function extraFields()
{
    return ['albums'];
}

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => 'Access Token',
            'role' => 'Role',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone' => 'Phone',
            'modified_at' => 'Modified At',
            'created_at' => 'Created At',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
        ];
    }

    
    public function getAlbumClients()
    {
        return $this->hasMany(AlbumClients::className(), ['user_id' => 'id']);
    }

   
    public function getAlbums()
    {
        return $this->hasMany(Albums::className(), ['user_id' => 'id']);
    }

  
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id']);
    }

    
    public function getUserPackages()
    {
        return $this->hasMany(UserPackages::className(), ['user_id' => 'id']);
    }

    
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    // helpers
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
         if (!$this->update()) return false;
         return true;
    }
 
    public static function validateUser($email, $password)
    {   
        $authUser = null;
        $authUser = static::findByEmail($email);
        if ($authUser!=null and $password!=null) 
        {
            if ($authUser->validatePassword($password)) 
            { 
                $authUser->generateAccessToken();
                return $authUser;
            }
        } 
    }

    public function resetPassword($password)
    {
        $this->password = $password;
        if (!$this->update()) return false;
            return true;
    }


    public static function resetToken($token)
    {
        $authUser = static::findIdentityByAccessToken($token);
        if (!$authUser) return false;
            $authUser->access_token = '';
        if (!$authUser->update()) return false;
            return true;
   }


    //authentication
    public function getId()
    {
        return $this->id;
    }
    
    public function getAuthKey()
    {
      return $this->auth_key;
    }
 
    public function validateAuthKey($authKey)
    {
      return $this->getAuthKey() === $authKey;
    }
 
    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id, 'status'=>self::STATUS_ACTIVE]);
               
    }
 
    public static function findIdentityByAccessToken($token, $type = null)
    {   
        $result = static::findOne(['access_token' => $token]);
             return $result; 
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) 
            {
                $this->generateAuthKey($this->auth_key);
                $this->setPassword($this->password);
            }
            if ($this->isAttributeChanged('password'))
            {
                $this->setPassword($this->password);   
                $this->generateAuthKey($this->auth_key);
            }
                return true;

        } else {
            return false;
        }   
    }
    
    public function afterSave($insert, $attrs)
    {
        if ($insert) 
        {
            $this->roleAssignment();    
            return true;
        } else { 
            return false;
        }
    }

    public function roleAssignment()
    {
        $auth = Yii::$app->authManager;
        $roles=$auth->getRoles();

        foreach ($roles as $role) 
        {
            if ($this->role == $role->name)
               { 
                    $current_role = $auth->createRole($role->name);
                    $auth->assign($current_role, $this->id);
                }
        }
     return true;  
    }

}



