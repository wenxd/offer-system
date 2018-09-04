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

        foreach ($jsonList as $key => $value) {
            if ($value['type'] == '0') {
                $newList = $value['list'];
            }
            if ($value['type'] == '1') {
                $betterList = $value['list'];
            }
            if ($value['type'] == '2') {
                $stockList = $value['list'];
            }
        }
        //最新
        $newIds = ArrayHelper::getColumn($newList, 'id');
        $inquiryNewQuery = Inquiry::find()->where(['is_newest' => Inquiry::IS_NEWEST_YES])->andWhere(['in', 'id', $newIds])->asArray()->all();
        foreach ($inquiryNewQuery as $key => $inquiry) {
            foreach ($newList as $new) {
                if ($inquiry['id'] == $new['id']) {
                    $inquiryNewQuery[$key]['number'] = $new['number'];
                }
            }
        }

        //最优
        $betterIds = ArrayHelper::getColumn($betterList, 'id');
        $inquiryBetterQuery = Inquiry::find()->where(['is_better' => Inquiry::IS_BETTER_YES])->andWhere(['in', 'id', $betterIds])->asArray()->all();
        foreach ($inquiryBetterQuery as $key => $inquiry) {
            foreach ($betterList as $better) {
                if ($inquiry['id'] == $better['id']) {
                    $inquiryBetterQuery[$key]['number'] = $better['number'];
                }
            }
        }

        //库存记录
        $stockIds = ArrayHelper::getColumn($stockList, 'id');
        $stockQuery = Stock::find()->andWhere(['in', 'id', $stockIds])->asArray()->all();
        foreach ($stockQuery as $key => $inquiry) {
            foreach ($stockList as $stock) {
                if ($inquiry['id'] == $stock['id']) {
                    $stockQuery[$key]['number'] = $stock['number'];
                }
            }
        }

        $data['inquiryNewest'] = $inquiryNewQuery;
        $data['inquiryBetter'] = $inquiryBetterQuery;
        $data['stockList']     = $stockQuery;
        $data['model']         = $model;

    }


}