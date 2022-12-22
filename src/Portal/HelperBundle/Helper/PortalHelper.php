<?php

namespace Portal\HelperBundle\Helper;

use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PortalHelper
{
    /**
     * MAX_PER_PAGE_PAGINATION for PagerFanta
     *
     */
    const MAX_PER_PAGE_PAGINATION = 10;

    /**
     * Form a JSON response
     *
     * @param bool $status
     * @param array $data Message, messages or payload
     * @param bool $overrideStatus If we don't need 500
     * @return JsonResponse
     */
    public static function response($status, $data, $overrideStatus = false)
    {
        $responseData = [
            'status' => $status
        ];

        if ($status) {
            $responseData['payload'] = $data;
        } else {
            $responseData['message'] = $data;
        }

        $code = ($status || $overrideStatus) ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;
        $headers = ['Access-Control-Allow-Origin' => '*'];

        return new JsonResponse($responseData, $code, $headers);
    }
//  TODO: rewrite strategy slug formation

    /**
     * Transliterate using intl extension
     *
     * @return string
     */
    public static function slug($name)
    {
        $slug = (new \DateTime)->getTimestamp() . "_" . $name;

        return $slug;
    }

    /**
     * Slugifies and sanitizes filename
     *
     * @param string $string
     * @param bool $withDate
     * @return string
     */
    public static function sanitizeFilename($string, $withDate = false)
    {
        return str_replace(array('`', 'ʹ'), '', (($withDate) ? date('d-m-Y-') : '') . PortalHelper::slug($string));
    }

    /**
     * @param string $filePath Full file path
     * @param string $attachmentName Used as attachment name
     * @param bool $unlinkAfter Delete file after response
     *
     * @return Response|JsonResponse
     */
    public static function fileResponse($filePath, $attachmentName, $unlinkAfter = false)
    {
        if (file_exists($filePath)) {
            // buffer file
            $contents = file_get_contents($filePath);

            $response = new Response();
            // create content disposition
            $d = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $attachmentName
            );
            $response->headers->set('Content-Disposition', $d);

            $response->setContent($contents);

            if ($unlinkAfter)
                unlink($filePath);
        } else {
            $response = self::response(false, 'Could not process file.');
        }

        return $response;
    }

    public static function copyFolderContents($dirSource, $dirDestination, $withAllLevels = true)
    {
        $dir = opendir($dirSource);
        // read dir contents
        while (($file = readdir($dir)) !== false) {
            // if is file => copy
            if (is_file($dirSource . "/" . $file)) {
                copy($dirSource . "/" . $file, $dirDestination . "/" . $file);
            }
            // copy all levels
            if ($withAllLevels) {
                // if is dir => create
                if (is_dir($dirSource . "/" . $file) && $file != "." && $file != "..") {
                    if (!mkdir($dirDestination . "/" . $file)) {
                        echo "Can't create " . $dirDestination . "/" . $file . "\n";
                    }
                    // call recursive function copyFolderContents
                    self::copyFolderContents("$dirSource/$file", "$dirDestination/$file");
                }
            }
        }
        closedir($dir);
    }

    public static function makeSymlinksFolderContents($dirSource, $dirDestination, $withAllLevels = true)
    {
        $dir = opendir($dirSource);
        // read dir contents
        while (($file = readdir($dir)) !== false) {
            // if is file => make symlink
            if (is_file($dirSource . '/' . $file)) {
                if ($file == 'parameters.yml') {
                    if (is_file($dirDestination . '/' . $file)) {
                        unlink($dirDestination . '/' . $file);
                    }
                    copy($dirSource . '/' . $file, $dirDestination . '/' . $file);
                } else {
                    if (is_link($dirDestination . '/' . $file)) {
                        unlink($dirDestination . '/' . $file);
                    }
                    symlink($dirSource . '/' . $file, $dirDestination . '/' . $file);
                }
            }
            // copy all levels
            if ($withAllLevels) {
                // if is dir => create
                if (is_dir($dirSource . '/' . $file) && $file != '.' && $file != '..') {
                    if (!mkdir($dirDestination . '/' . $file)) {
                        echo 'Can\'t create ' . $dirDestination . '/' . $file . '\\n';
                    }
                    // call recursive function copyFolderContents
                    self::makeSymlinksFolderContents("$dirSource/$file", "$dirDestination/$file");
                }
            }
        }
        closedir($dir);
    }

    public static function removeFolderWithContents($dirSource)
    {
        if (is_link($dirSource)) {
            unlink($dirSource);
        }
        if (is_dir($dirSource)) {
            $dir = opendir($dirSource);
            // read dir contents
            while (($file = readdir($dir)) !== false) {
                // if is file => copy
                if (is_file($dirSource . "/" . $file)) {
                    unlink($dirSource . "/" . $file);
                }
                // if is dir => create
                if (is_dir($dirSource . "/" . $file) && $file != "." && $file != "..") {
                    // call recursive function removeFolderWithContents
                    self::removeFolderWithContents("$dirSource/$file");
                }
            }
            closedir($dir);
            rmdir($dirSource);
        }
    }

    /**
     * Get path
     * @param integer $type
     * @param string $instance
     *
     * @return string
     */
    public static function getAttachmentPath($type, $instance = '')
    {
        if ($instance) {
            $fileDir = Attachment::FILE_DIR . $instance . Attachment::ATTACHMENTS_DIR;
        } else {
            $fileDir = Attachment::MAIN_FILE_DIR;
        }
        switch ($type) {
            case Attachment::TYPE_BANNERS:
                $path = $fileDir . Attachment::PATH_BANNERS;
                break;
            case Attachment::TYPE_DOCUMENTS:
                $path = $fileDir . Attachment::PATH_DOCUMENTS;
                break;
            case Attachment::TYPE_ARTICLES:
                $path = $fileDir . Attachment::PATH_ARTICLES;
                break;
            case Attachment::TYPE_EVENTS:
                $path = $fileDir . Attachment::PATH_EVENTS;
                break;
            case Attachment::TYPE_PAGES:
                $path = $fileDir . Attachment::PATH_PAGES;
                break;
            case Attachment::TYPE_MEDIA:
                $path = $fileDir . Attachment::PATH_MEDIA;
                break;
            case Attachment::TYPE_MAGAZINE_ARTICLES:
                $path = $fileDir . Attachment::PATH_MAGAZINE_ARTICLES;
                break;
            default:
                $path = null;
        }
        return $path;
    }

    /**
     * Get structure array
     *
     * @param array $data
     * @param integer $pid
     * @param integer $level
     *
     * @return array
     */
    public static function generateStructureTree($data, $pid = null, $level = 0, &$tree = null)
    {
        if ($tree === null)
            $tree= [];

        foreach ($data as $row) {
            if ($row['parent_id'] === $pid) {

                $_row['id'] = $row['id'];
                $_row['parentId'] = $row['parent_id'];
                $_row['slug'] = $row['slug'];
                $_row['route'] = $row['route'];
                $_row['name'] = $row['title'];
                $_row['level'] = $level;
                $_row['isLinkOnId'] = $row['is_link_on_id'];
                if (isset($row['is_hide_childs'])) {
                    $_row['isHideChilds'] = $row['is_hide_childs'];
                }
                if (isset($row['is_separator'])) {
                    $_row['isSeparator'] = $row['is_separator'];
                }

                $tree[] = $_row;

                self::generateStructureTree($data, $row['id'], $level + 1, $tree);
            }
        }

        return $tree;
    }

    /**
     * Get structure array
     *
     * @param object $data
     *
     * @return array
     */
    public static function breadcrumbs($data)
    {
        static $breadcrumbs = [];
        if (method_exists($data, 'getMenuNode')) {
            $_row['id'] = $data->getId();
            $_row['parent_id'] = null;
            $_row['route'] = $data->getSlug();
            $_row['name'] = $data->getTitle();
            $breadcrumbs[] = $_row;
            $menuNode = $data->getMenuNode();
        } else {

            $menuNode = $data;
        }

        if ($menuNode->getParent()) {
            $_row['id'] = $menuNode->getId();
            $_row['parent_id'] = $menuNode->getParent()->getId();
            $_row['route'] = $menuNode->getRoute();
            $_row['name'] = $menuNode->getLabel();
            array_unshift($breadcrumbs, $_row);
            self::breadcrumbs($menuNode->getParent());
        }

        return $breadcrumbs;
    }

    public static function sliceFullWord($str, $len)
    {
        $str = trim($str);
        if (mb_strlen($str) > $len) {
            $subStr = mb_substr($str, 0, $len + 1);
            $lastSpace = mb_strrpos($subStr, ' ');
            if ($lastSpace) {
                $resultStr = mb_substr($str, 0, $lastSpace);
            } else {
                $firstSpace = mb_strpos($str, ' ');
                $len = $firstSpace ?: $len;
                $resultStr =  mb_substr($str, 0, $len);
            }
            $resultStr .= '...';
        } else {
            $resultStr = $str;
        }

        return $resultStr;
    }

    /**
     * Add ending to $val from a '|' divided $string
     *
     * @param $val
     * @param $string
     *
     * @return string
     */
    public static function pluralize($val, $string)
    {
        $endings = explode('|', $string);
        if (count($endings) === 3) {

            $num = (int)$val % 100;
            if ($num >= 11 && $num <= 19) {
                $ending = $endings[2];
            } else {
                switch ($num % 10) {
                    case (1):
                        $ending = $endings[0];
                        break;
                    case (2):
                    case (3):
                    case (4):
                        $ending = $endings[1];
                        break;
                    default:
                        $ending = $endings[2];
                }
            }

        } else $ending = $string;

        return $val . ' ' . $ending;
    }

    /**
     *
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     */
    public static function uuidV4Generate()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
