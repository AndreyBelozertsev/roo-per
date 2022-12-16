<?php

namespace Portal\HelperBundle\Helper;

use Portal\UserBundle\Entity\User;

class EsiaHelper
{
    const PATH_TO_SAVE_FILE = '../esia/user_token/';

    /**
     * @inheritdoc
     */
    public static function httpRequest($url, $content = null, array $headers = [], $method = null)
    {
        if (null === $method) {
            $method = null === $content || '' === $content ? 'GET' : 'POST';
        }

        $header = null;

        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                $header .= "{$key}: {$value}\r\n";
            }
        }

        $headers = $header;
        if (is_string($content)) {
            $headers .= "Content-Length: " . strlen($content) . "\r\n";
        } elseif (is_array($content)) {
            $content = http_build_query($content, '', '&');
        }
        $headers .= "User-Agent: HWIOAuthBundle (https://github.com/hwi/HWIOAuthBundle)\r\n";

        $contextOptions = [
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
            ],
        ];

        if ($content !== null) {
            $contextOptions['http']['content'] = $content;
        }

        $contextOptions['http']['header'] = $headers;

        try {
            $context = stream_context_create($contextOptions);
            $stream = fopen($url, 'rb', false, $context);
            $responseContent = stream_get_contents($stream);
            // see http://php.net/manual/en/reserved.variables.httpresponseheader.php
            $responseHeaders = $http_response_header;
            fclose($stream);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $responseContent;
    }

    /**
     * Url safe for base64 encoded string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function base64UrlSafeDecode($string)
    {
        $base64 = strtr($string, '-_', '+/');

        return base64_decode($base64);
    }

    /**
     * @param string $string
     *
     * @return boolean
     */
    public static function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    /**
     * @param $json
     * @return array|mixed
     */
    public static function getResponseContentFromJson($json)
    {
        // First check that content in response exists, due too bug: https://bugs.php.net/bug.php?id=54484
        $content = (string)$json;
        if (!$content) {
            return array();
        }

        $response = json_decode($content, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            parse_str($content, $response);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return string
     */
    public static function normalizeUrl($url, array $parameters = [])
    {
        $normalizedUrl = $url;
        if (!empty($parameters)) {
            $normalizedUrl .= (false !== strpos($url, '?') ? '&' : '?').http_build_query($parameters, '', '&');
        }

        return $normalizedUrl;
    }

    /**
     * Save access_token and refresh_token to file
     *
     * @param User $user
     * @param string  $access_token
     * @param string  $refresh_token
     *
     * @return boolean
     */
    public static function saveUserEsiaTokensToFile (User $user, $access_token, $refresh_token)
    {
        if (!file_exists(self::PATH_TO_SAVE_FILE)) {
            mkdir(self::PATH_TO_SAVE_FILE, 0777, true);
        }

        $userData = [];
        $userData['id'] = $user->getId();
        $userData['access_token'] = $access_token;
        $userData['refresh_token'] = $refresh_token;
        $jsonUserData = json_encode($userData);

        $fileName = self::PATH_TO_SAVE_FILE.$user->getUsername();
        $file = fopen($fileName, "w");
        if ($file) {
            $result = fwrite($file, $jsonUserData);
            fclose($file);
            if (!$result) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Read access_token and refresh_token from file
     *
     * @param User $user
     *
     * @return boolean|array
     */
    public static function readUserEsiaTokensFromFile (User $user)
    {
        $fileName = self::PATH_TO_SAVE_FILE . $user->getUsername();
        if (file_exists($fileName)) {

            $file = fopen($fileName, "r");
            $jsonUserData = fgets($file);
            fclose($file);

            $userData = json_decode($jsonUserData, true);

            return $userData;
        }

        return false;
    }

    /**
     * Delete file
     * @param User $user
     *
     * @return boolean
     */
    public static function deleteUserEsiaTokensFromFile (User $user)
    {
        $fileName = self::PATH_TO_SAVE_FILE . $user->getUsername();
        if (file_exists($fileName)) {
            $result = unlink($fileName);
            if ($result) {
                return true;
            }
        }

        return false;
    }
}
