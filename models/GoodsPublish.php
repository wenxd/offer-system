<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods_publish".
 *
 * @property int $id 自增id
 * @property string $goods_number 零件编号
 * @property string $goods_number_b 零件号B
 * @property string $description 中文描述
 * @property string $description_en 中文描述
 * @property string $original_company 原厂家
 * @property string $original_company_remark 原厂家备注
 * @property string $unit 单位
 * @property string $technique_remark 技术备注
 * @property string $img_id 图纸
 * @property int $is_process 是否加工
 * @property int $is_special 是否特制 0不是 1是
 * @property int $is_nameplate 是否铭牌 0不是  1是
 * @property int $is_emerg 是否紧急 0否 1是
 * @property int $is_assembly 是否总成 0否 1是
 * @property string $nameplate_img_id 铭牌照片
 * @property int $is_deleted 是否删除：0未删除 1已删除
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 * @property string $device_info 设备信息 json存储
 * @property string $material 材质
 * @property int $is_tz 是否TZ 0否 1是
 * @property int $is_standard 是否标准 0否 1是
 * @property int $is_import 是否进口 0否 1是
 * @property int $is_repair 是否大修 0否 1是
 * @property string $part 所属部件
 * @property string $remark 零件备注
 * @property string $publish_tax_price 发行含税单价
 * @property string $publish_delivery_time 发行货期
 * @property string $estimate_publish_price 预估发行价
 * @property string $material_code 品牌商名称
 * @property string $import_mark 导入类别
 * @property string $publish_price 发行未税单价
 * @property string $publish_tax 发行税率
 * @property int $brand_id 品牌商ID
 * @property string $factory_price 美金出厂价
 * @property int $locking 锁定：1锁定，0未锁定
 * @property string $publish_type 发行价类别
 * @property int $is_publish_accuracy 是否准确
 * @property int $is_price 是否有价
 */
class GoodsPublish extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods_publish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_process', 'is_special', 'is_nameplate', 'is_emerg', 'is_assembly', 'is_deleted', 'is_tz', 'is_standard', 'is_import', 'is_repair', 'brand_id', 'locking', 'is_publish_accuracy', 'is_price'], 'integer'],
            [['publish_tax_price', 'publish_delivery_time', 'estimate_publish_price', 'publish_price', 'publish_tax', 'factory_price'], 'number'],
            [['goods_number', 'goods_number_b', 'original_company', 'original_company_remark', 'unit', 'img_id', 'nameplate_img_id', 'material', 'part', 'remark', 'material_code', 'import_mark', 'publish_type', 'updated_at', 'created_at'], 'string', 'max' => 255],
            [['description', 'description_en', 'technique_remark'], 'string', 'max' => 1000],
            [['device_info'], 'string', 'max' => 510],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'goods_number' => '零件编号',
            'goods_number_b' => '零件号B',
            'description' => '中文描述',
            'description_en' => '中文描述',
            'original_company' => '原厂家',
            'original_company_remark' => '原厂家备注',
            'unit' => '单位',
            'technique_remark' => '技术备注',
            'img_id' => '图纸',
            'is_process' => '是否加工',
            'is_special' => '是否特制 0不是 1是',
            'is_nameplate' => '是否铭牌 0不是  1是',
            'is_emerg' => '是否紧急 0否 1是',
            'is_assembly' => '是否总成 0否 1是',
            'nameplate_img_id' => '铭牌照片',
            'is_deleted' => '是否删除：0未删除 1已删除',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
            'device_info' => '设备信息 json存储',
            'material' => '材质',
            'is_tz' => '是否TZ 0否 1是',
            'is_standard' => '是否标准 0否 1是',
            'is_import' => '是否进口 0否 1是',
            'is_repair' => '是否大修 0否 1是',
            'part' => '所属部件',
            'remark' => '零件备注',
            'publish_tax_price' => '发行含税单价',
            'publish_delivery_time' => '发行货期',
            'estimate_publish_price' => '预估发行价',
            'material_code' => '品牌商名称',
            'import_mark' => '导入类别',
            'publish_price' => '发行未税单价',
            'publish_tax' => '发行税率',
            'brand_id' => '品牌商ID',
            'factory_price' => '美金出厂价',
            'locking' => '锁定：1锁定，0未锁定',
            'publish_type' => '发行价类别',
            'is_publish_accuracy' => '是否准确',
            'is_price' => '是否有价',
        ];
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
        $query = self::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => ['pageSize' => 30]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'material_code', $this->material_code])
            ->andFilterWhere(['like', 'goods_number', $this->goods_number])
            ->andFilterWhere(['like', 'original_company', $this->original_company])
            ->andFilterWhere(['like', 'goods_number_b', $this->goods_number_b])
            ->andFilterWhere(['like', 'publish_tax_price', $this->publish_tax_price])
            ->andFilterWhere(['like', 'estimate_publish_price', $this->estimate_publish_price])
            ->andFilterWhere(['like', 'factory_price', $this->factory_price])
            ->andFilterWhere(['like', 'publish_tax', $this->publish_tax])
            ->andFilterWhere(['like', 'publish_type', $this->publish_type])
            ->andFilterWhere(['is_price' => $this->is_price])
            ->andFilterWhere(['is_publish_accuracy' => $this->is_publish_accuracy]);
        return $dataProvider;
    }
}
