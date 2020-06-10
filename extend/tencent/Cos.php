<?php

namespace app\extend\tencent;

use Yii;
use Qcloud;
use app\models\Md5File;

/**腾讯云接口类
 * Class Cos
 * @package app\extend
 */
class Cos
{
    protected $client;

    public $region    = 'ap-beijing';
    public $appId     = '1253507986';
    public $secretId  = 'AKIDNp6C6uoYHAxa1IiHzc3LahdNvITSYwOj';
    public $secretKey = 'IRRGtLe0BB1fDalkXTJyRjpjV9cx65sX';
    public $bucket    = 'quote-1253507986';

    public function init()
    {
        if (!$this->client) {
            $this->client = new Qcloud\Cos\Client([
                'region' => $this->region,
                'credentials' => [
                    'appId'     => $this->appId,
                    'secretId'  => $this->secretId,
                    'secretKey' => $this->secretKey
                ]
            ]);
        }
    }

    public function uploadImage($file, $options = [], $headers = [])
    {
        $this->init();

        $key = date('YmdHis') . rand(1000, 9999) . '.jpg';

        $body    = fopen($file, 'rb');

        $md5 = md5_file($file);
        $md5File = Md5File::find()->where(['bucket' => $this->bucket, 'file_value' => $md5])->one();
        if ($md5File) {
            return $md5File->file_path;
        } else {
            $md5File             = new Md5File();
            $md5File->bucket     = $this->bucket;
            $md5File->file_value = $md5;
            $res = $this->client->upload($this->bucket, $key, $body, $headers);
            preg_match('/com\/(\S+)/', urldecode($res['Location']), $maths);
            $md5File->file_path = $maths['1'];
            $md5File->save();
        }

        return $key;
    }

    public function __call($method, $params = [])
    {
        $this->init();
        return call_user_func_array([$this->client, $method], $params);
    }
}
