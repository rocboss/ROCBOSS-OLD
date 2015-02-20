<?php
class Image
{
    public static function getImageInfo($img)
    {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false)
        {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info      = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        }
        else
        {
            return false;
        }
    }
    public static function createImg($source, $imgInfo, $wh, $destination)
    {
        $image_p = imagecreatetruecolor($wh, $wh);
        switch ($imgInfo[2])
        {
            case 1:
                $image = imagecreatefromgif($source);
                break;
            case 2:
                $image = imagecreatefromjpeg($source);
                break;
            case 3:
                $image = imagecreatefrompng($source);
                break;
        }
        if ($imgInfo[0] > $imgInfo[1])
        {
            $imgInfo[0] = $imgInfo[0] - ($imgInfo[0] - $imgInfo[1]);
        }
        if ($imgInfo[0] < $imgInfo[1])
        {
            $imgInfo[1] = $imgInfo[1] - ($imgInfo[1] - $imgInfo[0]);
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $wh, $wh, $imgInfo[0], $imgInfo[1]);
        imagejpeg($image_p, $destination, 100);
        imagedestroy($image_p);
        imagedestroy($image);
    }
    public static function createImgS($oldImg, $newImg, $imgInfo, $maxWidth = 200, $maxHeight = 200)
    {
        $image_p = imagecreatetruecolor($maxWidth, $maxHeight);
        switch ($imgInfo[2])
        {
            case 1:
                $image = imagecreatefromgif($oldImg);
                break;
            case 2:
                $image = imagecreatefromjpeg($oldImg);
                break;
            case 3:
                $image = imagecreatefrompng($oldImg);
                break;
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $maxWidth, $maxHeight, $imgInfo[0], $imgInfo[1]);
        imagejpeg($image_p, $newImg, 100);
        imagedestroy($image_p);
        imagedestroy($image);
    }
    public static function getSmallImg($value)
    {
        $path     = substr($value, 0, strrpos($value, '/') + 1);
        $fileName = substr($value, -36);
        $value    = $path . 's_' . $fileName;
        return $value;
    }
    
    public static function avatarPath($uid)
    {
        if (!is_dir('application/uploads/avatars/' . intval($uid / 1000) . '/'))
        {
            mkdir('application/uploads/avatars/' . intval($uid / 1000) . '/', 0777);
        }

        return 'application/uploads/avatars/' . intval($uid / 1000) . '/' . $uid . '/';
    }
    
    public static function getAvatarURL($uid, $size = 100)
    {
        $avatarPath = ROOT . self::avatarPath($uid) . $size . '.png';
        
        return $avatarPath;
    }
    public static function CreatDefaultAvatar($userId)
    {
        $avatar_dir = self::avatarPath($userId);
        if (!is_dir($avatar_dir))
        {
            mkdir($avatar_dir, 0777);
        }
        $defaultBigAvatar   = "application/uploads/avatars/0/0/avatar_b.png";
        $defaultSmallAvatar = "application/uploads/avatars/0/0/avatar_s.png";
        $newBigAvatar       = $avatar_dir . '100.png';
        $newSmallAvatar     = $avatar_dir . '50.png';
        copy($defaultBigAvatar, $newBigAvatar);
        copy($defaultSmallAvatar, $newSmallAvatar);
    }
    public function CreatQQAvatar($userId, $avatar)
    {
        $avatar_dir = self::avatarPath($userId);

        if (!is_dir($avatar_dir))
        {
            mkdir($avatar_dir, 0777);
        }
        $defaultBigAvatar   = "application/uploads/avatars/0/0/avatar_b.png";
        $defaultSmallAvatar = "application/uploads/avatars/0/0/avatar_s.png";
        $avatarFile         = $avatar_dir . "100.png";
        $avatarFile_S       = $avatar_dir . "50.png";
        $avatarData         = @file_get_contents($avatar, false, stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 3
            )
        )));
        @file_put_contents($avatarFile, $avatarData);
        @file_put_contents($avatarFile_S, $avatarData);
        $imgInfo = @getimagesize($avatarFile);
        if (isset($imgInfo[0], $imgInfo[1], $imgInfo[2]) && in_array($imgInfo[2], array(
            1,
            2,
            3
        )))
        {
            self::createImg($avatarFile, $imgInfo, 100, $avatarFile);
            self::createImg($avatarFile_S, $imgInfo, 50, $avatarFile_S);
        }
        else
        {
            @unlink($avatarFile);
            @copy($defaultBigAvatar, $avatarFile);
            @copy($defaultSmallAvatar, $avatarFile);
        }
    }
    public static function getImageURL($file)
    {
        return json_decode($file);
    }
    public static function RandomCode($width = 120, $height = 38, $verifyName = 'identifying_code')
    {
        $textArray = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'i',
            'J',
            'K',
            'L',
            'M',
            'N',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9'
        );
        $keyindex  = count($textArray) - 1;
        $verifyNum = "";
        for ($i = 0; $i < 5; $i++)
        {
            $verifyNum .= $textArray[rand(0, $keyindex)];
        }
        $_SESSION[$verifyName] = md5(strtolower($verifyNum));
        $im                    = imagecreate($width, $height);
        imagecolorallocatealpha($im, 255, 255, 255, 100);
        $color = imagecolorallocate($im, rand(0, 230), rand(0, 230), rand(0, 230));
        imagettftext($im, 16, 0, 5, 30, $color, 'system/static/OctemberScript.ttf', $verifyNum);
        Image::output($im, 'png');
    }
    public static function output($im, $type = 'png', $filename = '')
    {
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        if (empty($filename))
        {
            $ImageFun($im);
        }
        else
        {
            $ImageFun($im, $filename);
        }
        imagedestroy($im);
        exit;
    }
    public static function water($image, $water, $waterPos = 9)
    {
        if (!file_exists($image) || !file_exists($water))
            return false;
        $imageInfo = self::getImageInfo($image);
        $image_w   = $imageInfo['width'];
        $image_h   = $imageInfo['height'];
        $imageFun  = "imagecreatefrom" . $imageInfo['type'];
        $image_im  = $imageFun($image);
        $waterInfo = self::getImageInfo($water);
        $w         = $water_w = $waterInfo['width'];
        $h         = $water_h = $waterInfo['height'];
        $waterFun  = "imagecreatefrom" . $waterInfo['type'];
        $water_im  = $waterFun($water);
        switch ($waterPos)
        {
            case 0:
                $posX = rand(0, ($image_w - $w));
                $posY = rand(0, ($image_h - $h));
                break;
            case 1:
                $posX = 0;
                $posY = 0;
                break;
            case 2:
                $posX = ($image_w - $w) / 2;
                $posY = 0;
                break;
            case 3:
                $posX = $image_w - $w;
                $posY = 0;
                break;
            case 4:
                $posX = 0;
                $posY = ($image_h - $h) / 2;
                break;
            case 5:
                $posX = ($image_w - $w) / 2;
                $posY = ($image_h - $h) / 2;
                break;
            case 6:
                $posX = $image_w - $w;
                $posY = ($image_h - $h) / 2;
                break;
            case 7:
                $posX = 0;
                $posY = $image_h - $h;
                break;
            case 8:
                $posX = ($image_w - $w) / 2;
                $posY = $image_h - $h;
                break;
            case 9:
                $posX = $image_w - $w;
                $posY = $image_h - $h;
                break;
            default:
                $posX = rand(0, ($image_w - $w));
                $posY = rand(0, ($image_h - $h));
                break;
        }
        imagealphablending($image_im, true);
        imagecopy($image_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h);
        $bulitImg = "image" . $imageInfo['type'];
        $bulitImg($image_im, $image);
        $waterInfo = $imageInfo = null;
        imagedestroy($image_im);
    }
}
?>