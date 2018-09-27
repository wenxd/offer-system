<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "goods".
 *
 * @property int $id 自增id
 * @property string $goods_number 零件号
 * @property string $description 中文描述
 * @property string $description_en 英文描述
 * @property string $original_company 原厂家
 * @property string $original_company_remark 原厂家备注
 * @property string $unit 单位
 * @property string $technique_remark 技术备注
 * @property string $img_id 图纸
 * @property int $is_process 是否加工
 * @property int $is_special 是否特制
 * @property int $is_nameplate 是否铭牌
 * @property int $nameplate_img_id 铭牌照片
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Goods extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_PROCESS_NO    = '0';
    const IS_PROCESS_YES   = '1';
    
    const IS_SPECIAL_NO    = '0';
    const IS_SPECIAL_YES   = '1';
    
    const IS_NAMEPLATE_NO    = '0';
    const IS_NAMEPLATE_YES   = '1';

    public static $process = [
        self::IS_PROCESS_NO  => '否',
        self::IS_PROCESS_YES => '是',
    ];

    public static $special = [
        self::IS_SPECIAL_NO  => '否',
        self::IS_SPECIAL_YES => '是',
    ];
    
    public static $nameplate = [
        self::IS_NAMEPLATE_NO  => '否',
        self::IS_NAMEPLATE_YES => '是',
    ];
    
    public $img_url = '';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    # 修改之前
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                #设置默认值
                'value' => date('Y-m-d H:i:s', time())
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_process', 'is_deleted', 'is_special', 'is_nameplate'], 'integer'],
            [['offer_date', 'updated_at', 'created_at', 'img_url'], 'safe'],
            [['goods_number', 'original_company', 'original_company_remark', 'unit', 'technique_remark', 'img_id', 'nameplate_img_id'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 512],
            [
                ['goods_number','description'],
                'required',
                'on' => 'goods',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                      => '序号',
            'goods_number'            => '零件号',
            'description'             => '描述',
            'original_company'        => '原厂家',
            'original_company_remark' => '原厂家备注',
            'unit'                    => '单位',
            'technique_remark'        => '技术备注',
            'img_id'                  => '图纸',
            'is_process'              => '是否加工',
            'is_special'              => '是否特制',
            'is_nameplate'            => '是否铭牌',
            'nameplate_img_id'        => '铭牌照片',
            'is_deleted'              => '是否删除：0未删除 1已删除',
            'updated_at'              => '更新时间',
            'created_at'              => '创建时间',
            'img_url'                 => '图片地址',
        ];
    }

    public function afterFind()
    {
        if ($this->img_id) {
            $this->img_url = sprintf('%s/%s', Yii::$app->params['img_url_prefix'], $this->img_id);
        }
        if ($this->nameplate_img_id) {
            $this->nameplate_img_url = sprintf('%s/%s', Yii::$app->params['img_url_prefix'], $this->nameplate_img_id);
        }
    }

    public function beforeSave($insert)
    {
        $is_goods_number = self::find()->where(['is_deleted' => self::IS_DELETED_NO, 'goods_number' => $this->goods_number])->one();
        if ($insert && $is_goods_number) {
            $this->addError('id', '此零件编码已存在');
            return false;
        }
        $img = UploadedFile::getInstance($this, 'img_id');
        if ($img) {
            //用云
            $this->img_id = Util::upload($img->tempName);
            //用本地
//            $key = time() . rand(1000, 9999);
//            move_uploaded_file($img->tempName, 'images/' . $key . '.png');
//            $this->img_id = 'images/' . $key . '.png';
        }
        else {
            $this->img_id = $this->getOldAttribute('img_id') ? $this->getOldAttribute('img_id') : '';
        }

        $nameplate_img = UploadedFile::getInstance($this, 'nameplate_img_id');
        if ($nameplate_img) {
            //用云
            $this->nameplate_img_id = Util::upload($nameplate_img->tempName);
        }
        else {
            $this->nameplate_img_id = $this->getOldAttribute('nameplate_img_id') ? $this->getOldAttribute('nameplate_img_id') : '';
        }
        
        // 删除零件时，如果存在则不能删除
        if ($this->is_deleted == static::IS_DELETED_YES ) { // 删除操作

        }
        unset($this->img_url);
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $supplier = Supplier::find()->where(['is_deleted' => Supplier::IS_DELETED_NO])->one();
            $inquiry = new Inquiry();
            $inquiry->good_id          = $this->id;
            $inquiry->supplier_id      = $supplier ? $supplier->id : 0;
            $inquiry->price            = 0;
            $inquiry->tax_price        = 0;
            $inquiry->tax_rate         = 10;
            $inquiry->inquiry_datetime = date('Y-m-d H:i:s');
            $inquiry->is_newest        = Inquiry::IS_NEWEST_YES;
            $inquiry->save();
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public static function getCreateDropDown()
    {
        $goodList = self::find()->where(['is_deleted' => self::IS_DELETED_NO])->all();

        $list = [];

        foreach ($goodList as $key => $value) {
            $list[$value->id] = $value['goods_number'];
        }

        return $list;
    }

    public static function getAllDropDown()
    {
        $goodList = self::find()->all();

        $list = [];

        foreach ($goodList as $key => $value) {
            $list[$value->id] = $value['goods_number'];
        }

        return $list;
    }
}
