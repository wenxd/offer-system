<?php

class Code
{
    public function cartOld()
    {
        //最新
        $newList = Cart::findAll(['type' => Cart::TYPE_NEW]);
        $newIds  = ArrayHelper::getColumn($newList, 'inquiry_id');
        $inquiryNewQuery = Inquiry::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'inquiry.id = c.inquiry_id')
            ->where(['is_newest' => Inquiry::IS_NEWEST_YES, 'c.type' => Cart::TYPE_NEW])->andWhere(['in', 'inquiry.id', $newIds]);

        //最优
        $betterList = Cart::findAll(['type' => Cart::TYPE_BETTER]);
        $betterIds  = ArrayHelper::getColumn($betterList, 'inquiry_id');
        $inquiryBetterQuery = Inquiry::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'inquiry.id = c.inquiry_id')
            ->where(['is_better' => Inquiry::IS_BETTER_YES, 'c.type' => Cart::TYPE_BETTER])->andWhere(['in', 'inquiry.id', $betterIds]);

        //库存记录
        $stockList  = Cart::findAll(['type' => Cart::TYPE_STOCK]);
        $stockIds   = ArrayHelper::getColumn($stockList, 'inquiry_id');
        $stockQuery = Stock::find()->select('*, c.id as cart_id, c.number, c.type')->leftJoin('cart c', 'stock.id = c.inquiry_id')
            ->where(['c.type' => Cart::TYPE_STOCK])->andWhere(['in', 'stock.id', $stockIds]);

        $newCount    = $inquiryNewQuery->count();
        $betterCount = $inquiryBetterQuery->count();
        $stockCount  = $stockQuery->count();

        $count = $newCount > $betterCount ? ($newCount > $stockCount ? $newCount : $stockCount) : $betterCount;

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 1000000]);

        $data['inquiryNewest'] = $inquiryNewQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['inquiryBetter'] = $inquiryBetterQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['stockList']     = $stockQuery->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data['pages']         = $pages;

    }


}