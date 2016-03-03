<?php
namespace AppBundle\ImageHandle;
#use AppBundle\Helper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
class Image {
	private $file_name;
	private $file_path;
	public function __construct(ContainerInterface $container) {
	  $request = new Request();
		$this->file_path = preg_replace('/app$/si', 'web/uploads', $container->get('kernel')->getRootDir());
		if( !is_dir($this->file_path))
			mkdir($this->file_path);
		//$this->file_name;
	}
	#生成图片
	public function create()
	{
		$image_path = $this->file_path.'/'.$this->file_name;
		$imagine = new Imagine();
		$image = $imagine->open($image_path);
		$exif = @exif_read_data($image_path, 0, true);
		if (isset($exif['IFD0']['Orientation'])) {
			if ($exif['IFD0']['Orientation'] == 6) {
				$image->rotate(90)->save($image_path);
			} elseif ($exif['IFD0']['Orientation'] == 3) {
				$image->rotate(180)->save($image_path);
			}
		}
		$image->effects()->grayscale();
		$image->save($this->file_path.'/gray/'.$this->file_name);
		return $this->file_name;
		#缩放图片
		/*
		$photo = $imagine->open($image_path);
    $size = $photo->getSize();
    $w1 = $this->width*$scale;
    $h1 = $w1*$size->getHeight()/$size->getWidth();
    $imagine->open($image_path)
			->resize(new Box($w1, $h1))
			->save($image_path);
		#生成最大图片
		$photo = $imagine->open($image_path);
    $size = $photo->getSize();
		if(abs($pos_x) - $this->width/2 < $size->getWidth()/2){
			$w = $size->getWidth() > abs($pos_x) + $this->width/2 ? $size->getWidth() : abs($pos_x) + $this->width/2;
		}
		else{
			$w = 0;
		}
		if(abs($pos_y) - $this->height/2 < $size->getHeight()/2){
			$h = $size->getHeight() > abs($pos_x) + $this->height/2 ? $size->getHeight() : abs($pos_x) + $this->height/2;
		}
		else{
			$h = 0;
		}
		#超出边界
		if( $h == 0 || $w == 0){
			$imagine->create(new Box($w, $h))->save($image_path);
		}
		else{
			//$left = ($this->width - $w1)/2 + $pos_x;
	    //$top = (($h1/$scale) - $h1)/2 + $pos_y;
	    $left = $size->getWidth()/2 - $pos_x > $this->width/2 ? 0 : $pos_x;
	    $top = $pos_y + $size->getHeight()/2 > $this->height ? 0 : $pos_y;
			$collage = $imagine->create(new Box($w, $h));
			$collage->paste($photo, new Point($left, $top))
	        ->save($image_path);
		}
		#裁切图片
		*/
	}
	#上传图片
	public function upload(UploadedFile $file)
	{
		if( !in_array($file->guessExtension(), array('png','gif','jpeg','png'))){
			return false;
		}
    else{
    	$file_name = uniqid().date('ymdhis').'.'.$file->guessExtension();
    	$file->move($this->file_path, $file_name);
    	$this->file_name = $file_name;
    }
    return true;
	}
	public function getImageFromStream($data)
	{
		$data = preg_replace('/data:image\/(png|gif|jpeg);base64,/', '', $data);
		$data = str_replace(' ', '+', $data);
		$data = base64_decode($data);
		$file_name = uniqid().date('ymdhis').'.png';
		file_put_contents($this->file_path.'/'.$file_name, $data);
		$this->file_name = $file_name;
		return true;
	}
	public function getImageFromWechat($image_id, $token)
	{
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$image_id;
		$file_path = $this->file_path;
		/*
		if( exif_imagetype($url) === false ){
			return false;
		}
		*/
		$path_parts = pathinfo($url);
		if( null == $path_parts){
			return false;
		}
		$file_name = uniqid().date('ymdhis').'.'.$path_parts['extension'];
		file_put_contents($file_path.'/'.$file_name, file_get_contents($url));
		return true;
	}
}