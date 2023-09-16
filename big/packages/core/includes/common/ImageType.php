<?php

/**
 * This class describes image types.
 */
class ImageType{

    const HO_SO_XIN_VIEC         = 1;
    const SO_HO_KHAU             = 2;
    const HOP_DONG_HOP_TAC       = 3;
    const CAM_KET                = 4;
    const ID_CARD_FRONT          = 6;
    const ID_CARD_BACK           = 7;
    const KHAI_SINH              = 8;
    const GIAY_KHAM_SUC_KHOE     = 9;
    const BANG_CAP               = 10;
    const CAM_KET_BAO_MAT_TT     = 11;


    /**
     * Determines ability to upload image with mime type.
     *
     * @param      string  $type         The type
     * @param      string  $tmpFilePath  The temporary file path
     *
     * @return     bool    True if able to upload image with mime type, False otherwise.
     */
    public static function canUploadImageWithMimeType(string $type, string $tmpFilePath)
    {
        if(preg_match('#jpe?g#', $type) && @imagecreatefromjpeg($tmpFilePath)){
            return true;
        }

        if(preg_match('#gif#', $type) && @imagecreatefromgif($tmpFilePath)){
            return true;
        }

        if(preg_match('#png#', $type) && @imagecreatefrompng($tmpFilePath)){
            return true;
        }

        return false;
    }
}