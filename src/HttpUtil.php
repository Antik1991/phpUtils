<?php
namespace PhpUtils;

class HttpUtil
{

    /**
     * @var int
     */
    public static $connectTimeout = 30;//30 second

    /**
     * @var int
     */
    public static $readTimeout = 80;//80 second

    /**
     * @param $url
     * @param string $httpMethod
     * @param null $postFields
     * @param null $headers
     * @return stdClass
     * @throws Exception
     * @author: Antik
     * @Time: 2020/5/7  3:47 PM
     */
    public static function curl($url, $httpMethod = 'GET', $postFields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? self::getPostHttpBody($postFields) : $postFields);

        if (self::$readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        }

        if (self::$connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        }

        //https request
        if (strlen($url) > 5 && stripos($url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($headers) && 0 < count($headers)) {
            $httpHeaders = self::getHttpHearders($headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }

        $httpResponse = new \stdClass();
        $httpResponse->body = curl_exec($ch);
        $httpResponse->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            throw new Exception('Server unreachable: Errno: ' . curl_errno($ch) . ' ' . curl_error($ch));
        }
        curl_close($ch);

        return $httpResponse;
    }

    /**
     * @param $postFields
     * @return bool|string
     * @author: Antik
     * @Time: 2020/5/7  3:48 PM
     */
    public static function getPostHttpBody($postFields)
    {
        $content = '';
        foreach ($postFields as $apiParamKey => $apiParamValue) {
            $content .= "$apiParamKey=" . urlencode($apiParamValue) . '&';
        }
        return substr($content, 0, -1);
    }

    /**
     * @param $headers
     *
     * @return array
     */
    public static function getHttpHearders($headers)
    {
        $httpHeader = array();
        foreach ($headers as $key => $value) {
            $httpHeader[] = $key . ':' . $value;
        }
        return $httpHeader;
    }
}