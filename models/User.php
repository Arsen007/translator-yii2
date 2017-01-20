<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Security;
/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            'value' => function() { return date('Y-m-d H:i:s');  },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    /**
     * @inheritdoc
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
//        echo '<pre>';print_r(Security::validatePassword($password, $this->password));die;
        return Security::validatePassword($password, $this->password);
    }

    /**
     * hash password
     *
     * @param $password
     * @return string
     */
    public function hashPassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $this->hashPassword($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates "api" access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString(32);
    }

    /**
     * Generates new random password
     */
    public function generateRandPassword()
    {
        return Yii::$app->security->generateRandomString(8);
    }

    /**
     * save a new verification code
     */
    public function saveVerificationCode()
    {
        $this->sms_code = (string)mt_rand(10000, 99999);
        $this->sms_code_expire = date('Y-m-d H:i:s', time() + self::EXPIRE_TIME);
        return $this->save();
    }

    /**
     * Send sms to consumer with verification code
     * @return boolean
     */
    public function sendVerificationCode()
    {
        if ($this->saveVerificationCode()) {
            $sms = new \NexmoMessage(Yii::$app->params['nexmo']['key'], Yii::$app->params['nexmo']['secret']);
            $num = $this->cellphone;
            $info = $sms->sendText(
                $num,
                Yii::$app->params['nexmo']['default_number'],
                sprintf("Your XCINEX verification code is: %s", $this->sms_code)
            );

            return $info;
        }

        return false;
    }

    /**
     * Generate token for change password
     *
     * @param $model
     * @return $this
     */
    public function passwordChange($model)
    {

        $user = $this->find()->where(['username' => $model->username])->one();
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('User not found');
        } else {
            $user->password_reset_token = $this->generatePasswordResetToken();
            $user->save();

            return $this->sendEmail($user);
        }
    }

}
