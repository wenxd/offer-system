<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\web\UploadedFile;
use app\extend\tencent\Cos;

/**
 * This is the model class for table "goods".
 *
 * @property int $id 自增id
 * @property string $goods_number 厂家号
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
 * @property string $device_info 设备信息
 * @property string $material 材质
 * @property string $is_tz
 * @property string $is_standard
 * @property string $is_import
 * @property string $is_repair
 * @property string $part
 * @property string $remark
 * @property string $goods_number_b
 * @property string $is_emerg
 * @property string $is_assembly
 * @property string $publish_tax_price
 * @property string $publish_delivery_time
 * @property string $estimate_publish_price
 * @property string $material_code
 * @property string $import_mark
 * @property string $publish_price
 * @property string $publish_tax
 * @property string $brand_id
 * @property string $factory_price
 * @property string $locking
 * @property string $self_number
 */
class Goods extends ActiveRecord
{
    const IS_DELETED_NO    = '0';
    const IS_DELETED_YES   = '1';

    const IS_PROCESS_NO    = '0';
    const IS_PROCESS_YES   = '1';
    
    const IS_SPECIAL_NO    = '0';
    const IS_SPECIAL_YES   = '1';
    
    const IS_NAMEPLATE_NO   = '0';
    const IS_NAMEPLATE_YES  = '1';

    const IS_EMERG_NO       = '0';
    const IS_EMERG_YES      = '1';
    
    const IS_ASSEMBLY_NO    = '0';
    const IS_ASSEMBLY_YES   = '1';

    const IS_TZ_NO       = '0';
    const IS_TZ_YES      = '1';

    const IS_STANDARD_NO   = '0';
    const IS_STANDARD_YES  = '1';

    const IS_IMPORT_NO     = '0';
    const IS_IMPORT_YES    = '1';

    const IS_REPAIR_NO     = '0';
    const IS_REPAIR_YES    = '1';

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

    public static $emerg = [
        self::IS_EMERG_NO  => '否',
        self::IS_EMERG_YES => '是',
    ];

    public static $assembly = [
        self::IS_ASSEMBLY_NO  => '否',
        self::IS_ASSEMBLY_YES => '是',
    ];

    public static $tz = [
        self::IS_TZ_NO  => '否',
        self::IS_TZ_YES => '是',
    ];

    public static $standard = [
        self::IS_STANDARD_NO  => '否',
        self::IS_STANDARD_YES => '是',
    ];

    public static $import = [
        self::IS_IMPORT_NO  => '否',
        self::IS_IMPORT_YES => '是',
    ];

    public static $repair = [
        self::IS_REPAIR_NO  => '否',
        self::IS_REPAIR_YES => '是',
    ];

    public $img_url = '';
    public $nameplate_img_url = '';
    public $is_inquiry;
    public $is_better_inquiry;
    public $suggest_number;

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
            [['is_process', 'is_deleted', 'is_special', 'is_nameplate', 'is_emerg', 'is_assembly', 'is_inquiry',
                'is_tz', 'is_standard', 'is_import', 'is_repair', 'suggest_number', 'brand_id', 'locking'], 'integer'],
            [['offer_date', 'updated_at', 'created_at', 'img_url', 'nameplate_img_url', 'device_info',
                'publish_tax_price', 'publish_delivery_time', 'estimate_publish_price', 'publish_price', 'publish_tax', 'self_number'], 'safe'],
            [['goods_number', 'goods_number_b', 'original_company', 'original_company_remark', 'unit', 'technique_remark',
                'img_id', 'nameplate_img_id', 'description', 'description_en', 'material', 'part', 'remark', 'material_code',
                'import_mark'], 'safe'],
            [
                ['brand_id', 'goods_number'],
                'required',
                'on' => 'goods',
            ],
            [['publish_tax', 'publish_tax_price', 'estimate_publish_price', 'publish_delivery_time', 'publish_price',
                'factory_price'], 'default', 'value' => 0],
            [['publish_tax_price', 'factory_price', 'estimate_publish_price', 'publish_tax', 'publish_delivery_time'], 'number'],
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
            'goods_number_b'          => '厂家号',
            'description'             => '中文描述',
            'description_en'          => '英文描述',
            'original_company'        => '厂家',
            'original_company_remark' => '厂家备注',
            'unit'                    => '单位',
            'technique_remark'        => '技术备注',
            'img_id'                  => '图纸',
            'is_process'              => '加工',
            'is_special'              => '特制',
            'is_nameplate'            => '铭牌',
            'is_emerg'                => '紧急',
            'is_assembly'             => '总成',
            'nameplate_img_id'        => '铭牌照片',
            'is_deleted'              => '是否删除：0未删除 1已删除',
            'updated_at'              => '更新时间',
            'created_at'              => '创建时间',
            'img_url'                 => '图片地址',
            'nameplate_img_url'       => '图片地址',
            'device_info'             => '设备信息',
            'material'                => '材质',
            'is_tz'                   => 'TZ',
            'is_standard'             => '标准',
            'is_import'               => '进口',
            'is_repair'               => '大修',
            'part'                    => '所属部件',
            'remark'                  => '零件备注',
            'publish_tax_price'       => '发行含税单价',
            'publish_delivery_time'   => '发行货期',
            'suggest_number'          => '建议库存',
            'estimate_publish_price'  => '预估发行价',
            'material_code'           => '品牌',
            'import_mark'             => '导入类别',
            'publish_price'           => '发行未税单价',
            'publish_tax'             => '发行税率',
            'factory_price'           => '美金出厂价',
            'self_number'           => '零件自编号',
        ];
    }

    public static function getGoodsCode()
    {
        $model = Goods::find()->where(['is_deleted' => Goods::IS_DELETED_NO]);
        $use_admin = AuthAssignment::find()->where(['item_name' => '系统管理员'])->all();
        if (!in_array(Yii::$app->user->identity->getId(), array_column($use_admin, 'user_id'))) {
            $model->andWhere(['like', 'goods_number', "杂项-"]);
        }
        $res = $model->asArray()->all();
        $data = [];
        foreach ($res as $item) {
            $data[] = [
                'goods_id' => $item['id'],
                'info' => "{$item['goods_number']} {$item['material_code']}",
            ];
        }
        return $data;
    }

    public function afterFind()
    {
        if ($this->img_id) {
            $this->img_url = sprintf('%s/%s', Yii::$app->request->getHostInfo() . '/images', $this->img_id);
        }
        if ($this->nameplate_img_id) {
            $this->nameplate_img_url = sprintf('%s/%s', Yii::$app->request->getHostInfo() . '/images', $this->nameplate_img_id);
        }
    }

    public function beforeSave($insert)
    {
        if ($this->brand_id) {
            $brand = Brand::findOne($this->brand_id);
            $this->material_code = $brand->name;
            $is_goods_number = self::find()->where([
                'is_deleted'   => self::IS_DELETED_NO,
                'goods_number' => $this->goods_number,
                'brand_id'     => $brand->id
            ])->one();
            if ($insert && $is_goods_number) {
                $this->addError('id', '此零件编码已存在');
                return false;
            }
        }

        $this->goods_number             = strtoupper($this->goods_number);
        $this->goods_number_b           = strtoupper($this->goods_number_b);
        $this->description              = strtoupper($this->description);
        $this->description_en           = strtoupper($this->description_en);
        $this->original_company         = strtoupper($this->original_company);
        $this->original_company_remark  = strtoupper($this->original_company_remark);
        $this->unit                     = strtoupper($this->unit);
        $this->material                 = strtoupper($this->material);
        $this->material_code            = strtoupper($this->material_code);
        $this->import_mark              = strtoupper($this->import_mark);
        $this->part                     = strtoupper($this->part);
        $this->remark                   = strtoupper($this->remark);
        if (!$this->self_number) {
            $this->self_number              = 'G' . time() . '-' . rand(10, 99);
        }


        //设备信息处理
        if ($this->device_info != $this->getOldAttribute('device_info')) {
            $arr = [];
            if (isset($this->device_info['name'])) {
                foreach ($this->device_info['name'] as $key => $item) {
                    if ($item) {
                        $arr[strtoupper($item)] = $this->device_info['number'][$key];
                    }
                }
                $this->device_info = json_encode($arr, JSON_UNESCAPED_UNICODE);
            }
        }

        $this->description_en = strtoupper($this->description_en);
        $img = UploadedFile::getInstance($this, 'img_id');
        //$cos = new Cos();
        if ($img) {
            //用云
            //$this->img_id = $cos->uploadImage($img->tempName);
            //用本地
            $key = time() . rand(1000, 9999);
            move_uploaded_file($img->tempName, 'images/' . $key . '.png');
            $this->img_id = $key . '.png';
        } else {
            $this->img_id = $this->getOldAttribute('img_id') ? $this->getOldAttribute('img_id') : '';
        }

        $nameplate_img = UploadedFile::getInstance($this, 'nameplate_img_id');
        if ($nameplate_img) {
            //用本地
            $key = time() . rand(1000, 9999);
            move_uploaded_file($nameplate_img->tempName, 'images/' . $key . '.png');
            $this->nameplate_img_id = $key . '.png';
        } else {
            $this->nameplate_img_id = $this->getOldAttribute('nameplate_img_id') ? $this->getOldAttribute('nameplate_img_id') : '';
        }
        if ($this->nameplate_img_id) {
            $this->is_nameplate = self::IS_NAMEPLATE_YES;
        }

        // 删除零件时，如果存在则不能删除
        if ($this->is_deleted == static::IS_DELETED_YES ) { // 删除操作

        }

        if ($this->suggest_number) {
            $high_stock_ratio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_HIGH_STOCK_RATIO])->scalar();
            $low_stock_ratio  = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_LOW_STOCK_RATIO])->scalar();
            $stock = Stock::find()->where(['good_id' => $this->id])->one();
            if ($stock) {
                $stock->suggest_number  = $this->suggest_number;
                $stock->high_number     = (int) round($high_stock_ratio * trim($this->suggest_number));
                $stock->low_number      = (int) round($low_stock_ratio * trim($this->suggest_number));
                $stock->save();
            }
        }

        //计算税率问题
        if ($this->publish_tax_price != '0.00') {
            if ($this->publish_tax) {
                $this->publish_price = number_format(($this->publish_tax_price / (1 + $this->publish_tax/100)), 2, '.', '' );
            } else {
                $this->publish_price = $this->publish_tax_price;
            }
        } elseif ($this->estimate_publish_price) {
            if ($this->publish_tax) {
                $this->publish_price = number_format(($this->estimate_publish_price / (1 + $this->publish_tax/100)), 2, '.', '' );
            } else {
                $this->publish_price = $this->estimate_publish_price;
            }
        }

        unset($this->img_url);
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {//这里是新增数据
            $high_stock_ratio = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_HIGH_STOCK_RATIO])->scalar();
            $low_stock_ratio  = SystemConfig::find()->select('value')->where(['title' => SystemConfig::TITLE_LOW_STOCK_RATIO])->scalar();
            $stock = new Stock();
            $stock->good_id  = $this->id;
            $stock->tax_rate = SystemConfig::find()->select('value')->where([
                'title'  => SystemConfig::TITLE_TAX,
                'is_deleted' => SystemConfig::IS_DELETED_NO])->orderBy('id Desc')->scalar();
            $stock->suggest_number  = $this->suggest_number ? $this->suggest_number : 0;
            $stock->high_number     = (int) round($high_stock_ratio * trim($stock->suggest_number));
            $stock->low_number      = (int) round($low_stock_ratio * trim($stock->suggest_number));
            $stock->save();
        }
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

    public function getInquirySn()
    {
        return $this->hasOne(InquiryGoods::className(), ['goods_id' => 'id']);
    }

    public function getInquiry()
    {
        return $this->hasOne(Inquiry::className(), ['good_id' => 'id']);
    }

    public function getInquiryNumber()
    {
        return $this->hasMany(Inquiry::className(), ['good_id' => 'id'])->count();
    }

    /**
     * 关联成本单
     */
    public function getFinallow()
    {
        return $this->hasOne(FinalGoods::className(), ['goods_id' => 'id'])->with('inquirylow')->orderBy('price ASC');
    }

    /**
     * 关联询价单
     */
    public function getInquirylow()
    {
        return $this->hasOne(Inquiry::className(), ['good_id' => 'id'])->orderBy('price ASC');
    }

    public function getInquiryBetter()
    {
        return $this->hasOne(Inquiry::className(), ['good_id' => 'id'])->where(['is_better' => 1]);
    }

    public function getStockNumber()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'id'])->where(['>', 'number', 0]);
    }

    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['good_id' => 'id']);
    }

    public function getSon()
    {
        return $this->hasOne(GoodsRelation::className(), ['goods_id' => 'id'])->where(['goods_relation.is_deleted' => GoodsRelation::IS_DELETED_NO]);
    }
    public function getSons()
    {
        return $this->hasMany(GoodsRelation::className(), ['p_goods_id' => 'id'])
            ->where(['goods_relation.is_deleted' => GoodsRelation::IS_DELETED_NO]);
    }

    /**
     * 获取导入类别
     */
    public function getImportmark()
    {
        $data = $this->hasMany(GoodsPublish::className(), ['id' => 'id'])->select(['import_mark'])->groupBy('import_mark')->all();
        if (empty($data)) {
            return false;
        }
        return array_column($data, 'import_mark');

    }
}
