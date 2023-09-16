<?php
  /**
   *Thumb Generator using GD Library
   *Created By: Syed Abdul Baqi.
   *Date: 11th July 2009   
   *
   *Usage:
   *$image = new Imagethumb(<source file path>, [aspect ratio(DEFAULT = false)]);   
   *$image->getThumb(<thumb file path>, (int)<thumb image width>, (int)<thumb image heigth>);
   *         
   */           


  class Imagethumb
  {
    public $height;
    public $width;
    public $type;
    public $aspect = false;
    public $thumb;
    public $height_t = 100;
    public $width_t = 100;
    public $height_at = 100;
    public $width_at = 100;	
    public $filename;

    function __construct($filename, $aspect = false)
    {
      $this->filename = $filename;
      $this->aspect = $aspect;

      $image = getimagesize($this->filename);
      $this->width = $image[0];
      $this->height = $image[1];
      $this->type = $image['mime'];
    }
    
    function getThumb($thumb, $width_t, $height_t)
    {
      $this->thumb = $thumb;
      $this->width_t  = $width_t;
      $this->height_t  = $height_t;
      
      if($this->aspect == true)
      {
        $this->setAspect();
      }
      switch($this->type)
      {
        case 'image/jpg':
        case 'image/jpeg':
        case 'image/pjpeg':
          $this->createJpeg();
        break;

        case 'image/png':
        case 'image/x-png':
          $this->createPng();
        break;

        case 'image/gif':
          $this->createGif();
        break;

        case 'image/bmp':
        case 'image/wbmp':
          $this->createBmp();
        break;
      }
    }
    
    function createJpeg()
    {
		$src = imagecreatefromjpeg($this->filename);
		$desc = imagecreatetruecolor($this->width_at, $this->height_at);
		imagesavealpha($desc, TRUE);
		$top = 0;
		$left = 0;
		imagecopyresampled($desc, $src, $left, $top, 0, 0, $this->width_at, $this->height_at, $this->width, $this->height);
		imagejpeg($desc, $this->thumb);
		return $desc;
    }

    function createPng()
    {
		$src = imagecreatefrompng($this->filename);
		$desc = imagecreatetruecolor($this->width_at, $this->height_at);
		imagesavealpha($desc, TRUE);
		$top = 0;
		$left = 0;
		imagecopyresampled($desc, $src, $left, $top, 0, 0, $this->width_at, $this->height_at, $this->width, $this->height);
		imagepng($desc, $this->thumb);
		return $desc;
    }

    function createGif()
    {
		$src = imagecreatefromgif($this->filename);
		$desc = imagecreatetruecolor($this->width_at, $this->height_at);
		imagesavealpha($desc, TRUE);
		$top = 0;
		$left = 0;
		imagecopyresampled($desc, $src, $left, $top, 0, 0, $this->width_at, $this->height_at, $this->width, $this->height);
		imagegif($desc, $this->thumb);
		return $desc;
    }

    function createBmp()
    {
		$src = imagecreatefromwbmp($this->filename);	  
		$desc = imagecreatetruecolor($this->width_at, $this->height_at);
		imagesavealpha($desc, TRUE);
		$top = 0;
		$left = 0;
		imagecopyresampled($desc, $src, $left, $top, 0, 0, $this->width_at, $this->height_at, $this->width, $this->height);
		imagewbmp($desc, $this->thumb);
		return $desc;
    }
    
    function setAspect()
    {
		$ratio_width = $this->width/$this->width_t;
		$this->width_at = $this->width/$ratio_width;
		$this->height_at = $this->height/$ratio_width;
    }
	function destroy($src)
	{
		imagedestroy($src);	
	}
  }
?>