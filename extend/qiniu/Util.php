<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/8/29
 * Time: 15:49
 */
namespace app\models;

use Yii;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**扩展类
 * Class Util
 * @package app\models
 */
class Util
{
    public static $accessKey = 'fq4oCHodZTtJ2cYZ_vVF7D2XDT06LdcZa0z2Ilir';
    public static $secretKey = 'f7J496DYQyvrQXFSkyNYYili6k7miMriC2Gdf9AA';
    public static $bucket    = 'quote';

    public static function xorEncode($str, $key = 'a1b2c3d4e5f6')
    {
        $slen = strlen($str);
        $klen = strlen($key);

        $cipher = '';
        for ($i = 0; $i < $slen; $i = $i+$klen) {
            $cipher .= substr($str, $i, $klen) ^ $key;
        }

        return base64_encode($cipher);
    }

    public static function xorDecode($str, $key = 'a1b2c3d4e5f6')
    {
        $str  = base64_decode($str);
        $slen = strlen($str);
        $klen = strlen($key);

        $plain = '';
        for ($i=0; $i < $slen; $i = $i+$klen) {
            $plain .= $key^substr($str, $i, $klen);
        }

        return $plain;
    }

    public static function sourceEncode($str)
    {
        $str = static::xorEncode($str);
        return str_replace('=', '_', $str);
    }

    public static function sourceDecode($str)
    {
        $str = str_replace('_', '=', $str);
        $source = static::xorDecode($str);

        return $source;
    }

    //七牛上传
    public static function upload($filePath)
    {
        $auth = new Auth(self::$accessKey, self::$secretKey);
        // 生成上传Token
        $token = $auth->uploadToken(self::$bucket);
        // 构建 UploadManager 对象
        $uploadMgr = new UploadManager();

        $key = time() . rand(1000, 9999);

        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

        if ($err != null) {
            var_dump($err);
        } else {
            return $key;
        }
    }
}