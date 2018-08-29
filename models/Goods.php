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
 * @property string $goods_number 零件编号
 * @property string $description 描述
 * @property string $original_company 原厂家
 * @property string $original_company_remark 原厂家备注
 * @property string $unit 单位
 * @property string $technique_remark 技术备注
 * @property string $img_id 图纸
 * @property string $competitor 竞争对手名称
 * @property string $competitor_offer 对手报价
 * @property int $is_process 是否加工
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $offer_date 报价时间
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class Goods extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_PROCESS_NO    = '0';
    const IS_PROCESS_YES   = '1';

    public static $process = [
        self::IS_PROCESS_NO  => '否',
        self::IS_PROCESS_YES => '是',
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
            [['is_process', 'is_deleted'], 'integer'],
            [['offer_date', 'updated_at', 'created_at', 'img_url'], 'safe'],
            [['goods_number', 'original_company', 'original_company_remark', 'unit', 'technique_remark', 'img_id', 'competitor'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 512],
            [
                ['goods_number','description'],
                'required',
                'on' => 'goods',
            ],
            [['competitor_offer'], 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                      => '自增id',
            'goods_number'            => '零件编号',
            'description'             => '描述',
            'original_company'        => '原厂家',
            'original_company_remark' => '原厂家备注',
            'unit'                    => '单位',
            'technique_remark'        => '技术备注',
            'img_id'                  => '图纸',
            'competitor'              => '竞争对手名称',
            'competitor_offer'        => '对手报价',
            'is_process'              => '是否加工',
            'is_deleted'              => '是否删除：0未删除 1已删除',
            'offer_date'              => '报价时间',
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
    }

    public function beforeSave($insert)
    {
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
            $this->img_id = $this->getOldAttribute('img_id');
        }

        if (!$this->img_id) {
            $this->addError('img_id', '请上传图纸');
            return false;
        }

        // 删除零件时，如果存在则不能删除
        if ($this->is_deleted == static::IS_DELETED_YES ) { // 删除操作

        }
        unset($this->img_url);
        return parent::beforeSave($insert);
    }
}
