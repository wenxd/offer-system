<?php

namespace mdm\admin\models\form;

use Yii;
use mdm\admin\models\User;
use yii\base\Model;

/**
 * Description of ChangePassword
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class UpdatePassword extends Model
{
    public $newPassword;
    public $retypePassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['newPassword', 'retypePassword'], 'required'],
            [['newPassword'], 'string', 'min' => 6],
            [['retypePassword'], 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'newPassword'          => '新密码',
            'retypePassword'       => '再输一次新密码',
        ];
    }
    /**
     * Change password.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function change($user)
    {
        if ($this->validate()) {
            /* @var $user User */
            $user->setPassword($this->newPassword);
            $user->generateAuthKey();
            if ($user->save()) {
                return true;
            }
        }

        return false;
    }
}
