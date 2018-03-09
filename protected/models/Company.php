<?php
/**
 * This is the model class for table "{{company}}".
 * 单位
 * The followings are the available columns in table '{{company}}':
 * @property integer $id
 * @property string $name
 * @property string $entering_time
 * @property integer $trade_id
 * @property integer $city_id
 * @property string $full_address
 * @property string $phone
 * @property integer $type_id
 * @property integer $property_id
 * @property string $postal_code
 * @property string $email
 * @property string $website
 * @property integer $is_famous
 * @property integer $concern_num
 * @property integer $is_join_big_recruitment
 * @property string $introduction
 * @property string $logo
 * @property integer $is_join_recruitment_week
 * @property string $video_url
 *
 * @property integer $economic_type_id
 * @property string $unit_size
 * @property string $is_school_company
 */
class Company extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return "{{company}}";
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Company the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function relations(){
        if(!isset(Yii::app()->session['user_id']))
            $arr = array(self::MANY_MANY, 'User',
                't_company_user(company_id, user_id)','having'=>'concern.id=0');
        else
            $arr = array(self::MANY_MANY, 'User',
                't_company_user(company_id, user_id)','having'=>'concern.id='.Yii::app()->session['user_id']);
        if(!isset(Yii::app()->session['user_id']))
            $arr2 = array(self::MANY_MANY, 'User',
                't_company_user(company_id, user_id)','condition'=>'concern2.id=0','together'=>true);
        else
            $arr2 = array(self::MANY_MANY, 'User',
                't_company_user(company_id, user_id)','condition'=>'concern2.id='.Yii::app()->session['user_id'],'together'=>true);
    return array(
        'companytrade'=>array(SELF::BELONGS_TO,'CompanyTrade','trade_id','select'=>'name'),
        'companyproperty'=>array(SELF::BELONGS_TO,'CompanyProperty','property_id','select'=>'name'),
        'city'=>array(self::MANY_MANY, 'City',
            't_company_city(company_id, city_id)'),
        'concernCount'=>array(self::STAT, 'CompanyUser', 'company_id'),
        'focusNum'=>array(self::STAT, 'CompanyUser', 'company_id',
            'condition'=>'user_id='.Yii::app()->session['user_id']),
        'concern'=>$arr,
        'concern2'=>$arr2,
        'companycity'=>array(SELF::BELONGS_TO,'CompanyCity','','on'=>'t.id=companycity.company_id'),
        'companypic'=>array(SELF::HAS_MANY,'CompanyPic','company_id','select'=>'url'),

        'brightened'=>array(SELF::HAS_MANY,'Brightened','related_id','select'=>'name,type','order'=>'brightened.id DESC',
							'limit'=>'2','having'=>'brightened.type=1'),
        'careertalk'=>array(SELF::HAS_MANY,'CareerTalk','company_id','having'=>'careertalk.url!=""'),
    );
   }
}