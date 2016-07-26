<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of ProductAttachmentService
 *
 * @author johnh
 */
class ProductAttachmentService {

    private $url;
    private $s3bucket;
    private $accessKey;
    private $secretKey;

    public function __construct($url, $s3bucket, $accessKey, $secretKey) {

        $this->url = $url;
        $this->s3bucket = $s3bucket;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Uploads a file, and returns the url
     * 
     * @param UploadedFile $file
     */
    public function uploadAttachment(UploadedFile $file) {
            
    }

    private function resizeImage($file, $width, $height) {

        $dest = tempnam(sys_get_temp_dir(), "attachment");
        
        $imagesize = getimagesize($file);

        $src_width = $imagesize[0];
        $src_height = $imagesize[1];

        if ($width == 0) {

            $width = $height * ($src_width / $src_height);
        } elseif ($height == 0) {

            if ($src_height > $src_width) {

                $height = $width;
                $width = $height * ($src_width / $src_height);
            } else {

                $height = $width * ($src_height / $src_width);
            }
        }

        $image_p = imagecreatetruecolor($width, $height);
        $image = imagecreatefromjpeg($file);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
        imagejpeg($image_p, $dest);
        
        return $dest;
        
    }
    
}