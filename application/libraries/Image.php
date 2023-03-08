<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// ini_set("upload_max_filesize","100000M");
class Image
{

	public $fix;
	public $image_size_default = 390;
	public function __construct()
	{

		$this->fix = [
			'width' => 1920
		];
	}

	//
	//	upload image
	// @param pathinfo		@text = path true for upload image
	// @param file			@text = file image name
	//
	function uploadimage($path, $file, $optional = [])
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//



		$result['status'] = 0;
		$result['error'] = 0;

		//config
		$thumb_square_size = 'auto'; //Thumbnails will be cropped to 200x200 pixels
		$max_image_size = isset($optional['width']) ? $optional['width'] : $this->fix['width']; //Maximum image size (width)
		$thumb_prefix = "thumb_"; //Normal thumb Prefix

		$destination_folder = $path;

		$jpeg_quality = 55; //jpeg quality
		$number = time();	//set name image


		// check $_FILES['ImageFile'] not empty
		if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
			die();
		} else {
			//	process
			//uploaded file info we need to proceed
			$image_name = $file['name']; //file name
			$image_size = $file['size']; //file size
			$image_temp = $file['tmp_name']; //file temp

			$image_size_info = getimagesize($image_temp); //get image size

			if ($image_size_info) {
				$image_width = $image_size_info[0]; //image width
				$image_height = $image_size_info[1]; //image height
				$image_type = $image_size_info['mime']; //image type
			} else {
				die("Make sure image file is valid!");
			}

			//switch statement below checks allowed image type 
			//as well as creates new image from given file 
			switch ($image_type) {
				case 'image/png':
					$image_res = imagecreatefrompng($image_temp);
					break;
				case 'image/gif':
					$image_res = imagecreatefromgif($image_temp);
					break;
				case 'image/jpeg':
				case 'image/pjpeg':
					$image_res = imagecreatefromjpeg($image_temp);
					break;
				default:
					$image_res = false;
			}

			if ($image_res) {
				//Get file extension and name to construct new file name 
				$image_info = pathinfo($image_name);
				$image_extension = strtolower($image_info["extension"]); //image extension
				$image_name_only = strtolower($image_info["filename"]); //file name only, no extension
				//create a random name for new image (Eg: fileName_293749.jpg) ;
				// $new_file_name = $number ."_!@". $image_name_only .'.' . $image_extension;

				//folder path to save resized images and thumbnails
				$image_save_folder = $destination_folder;

				$ci->load->library(array('image'));
				$new_width = $max_image_size;
				$new_height = round($new_width * $image_size_info[1] / $image_size_info[0]);
				//call normal_resize_image() function to proportionally resize image
				if ($ci->image->normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_size, $new_width, $new_height, $image_width, $image_height, $jpeg_quality)) {
					//call crop_image_square() function to create square thumbnails
					/* if (!crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality)) {
						die('Error Creating thumbnail');
					}*/
					$result['status'] = 1;
					$result['data'] = $destination_folder;	//	path name
				} else {
					$result['error'] = 1;
					$result['error_data'] = "error : " . $destination_folder;
				}

				imagedestroy($image_res); //freeup memory
			}
		}


		return $result;
	}

	// This function will proportionally resize image
	function normal_resize_image($source, $destination, $image_type, $max_size, $new_width, $new_height, $image_width, $image_height, $quality)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//
		$ci->load->library('image');

		if ($image_width <= 0 || $image_height <= 0) {
			return false;
		} //return false if nothing to resize

		//Construct a proportional size of new image
		/* $image_scale = min($max_size / $image_width, $max_size / $image_height);
		$new_width = ceil($image_scale * $image_width);
		$new_height = ceil($image_scale * $image_height); */

		if ($new_width <= $image_width) {

			$new_canvas = imagecreatetruecolor($new_width, $new_height); //Create a new true color image
			//Copy and resize part of an image with resampling
			if (imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)) {

				$ci->image->save_image($new_canvas, $destination, $image_type, $quality); //save resized image
			}
		} else {
			if (!is_null($max_size)) {

				//do not resize if image is smaller than max size


				// if ($image_width <= $max_size && $image_height <= $max_size) {
				if ($image_width <= $max_size) {

					if ($ci->image->save_image($source, $destination, $image_type, $quality)) {
						// return true;
					}
				}
			}
		}


		return true;
	}

	// This function corps image to create exact square, no matter what its original size!
	function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality)
	{
		if ($image_width <= 0 || $image_height <= 0) {
			return false;
		} //return false if nothing to resize

		if ($image_width > $image_height) {
			$y_offset = 0;
			$x_offset = ($image_width - $image_height) / 2;
			$s_size = $image_width - ($x_offset * 2);
		} else {
			$x_offset = 0;
			$y_offset = ($image_height - $image_width) / 2;
			$s_size = $image_height - ($y_offset * 2);
		}
		$new_canvas = imagecreatetruecolor($square_size, $square_size); //Create a new true color image
		//Copy and resize part of an image with resampling
		if (imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)) {
			save_image($new_canvas, $destination, $image_type, $quality);
		}

		return true;
	}

	//	save image
	function save_image($source, $destination, $image_type, $quality)
	{
		switch (strtolower($image_type)) { //determine mime type
			case 'image/png':
				imagepng($source, $destination);
				return true; //save png file
				break;
			case 'image/gif':
				imagegif($source, $destination);
				return true; //save gif file
				break;
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg($source, $destination, 100);
				return true; //save jpeg file
				break;
			default:
				return false;
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param array|null $file
	 * @param array|null $path
	 * @param integer $type = 1=1size, 2=2size
	 * @return void
	 */
	function upload_image(array $file = null, array $path = null, int $type = 1)
	{
		# code...
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		if ($file) {

			$allowedFileType = array('jpg', 'png', 'jpeg', 'JPG', 'gif', 'GIF');
			// $uploadsDir = $path;

			$img_array = [];

			foreach ($file['name'] as $key => $val) {
				//	check file for upload
				if ($file['name'][$key]) {
					// create array FILE

					$imagefile = array(
						'type'        => $file['type'][$key],
						'name'        => $file['name'][$key],
						'tmp_name'    => $file['tmp_name'][$key],
						'error'        => $file['error'][$key],
						'size'        => $file['size'][$key]
					);

					$new_image_name = time() . '.' . explode("/", $file['type'][$key])[1];

					$img_array[] = $new_image_name;

					if ($path) {
						foreach ($path as $dir_path) {

							if (!file_exists($dir_path)) {
								mkdir($dir_path, 0777, true);
							}

							$targetPath = $dir_path . $new_image_name;

							$fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
							if (in_array($fileType, $allowedFileType)) {

								switch ($type) {
									case 2:
										$array_width[] = 90;
										$array_width[] = $this->image_size_default;
										break;
									default:
										$array_width[] = $this->image_size_default;
										break;
								}

								foreach ($array_width as $key => $width) {
									
									if($width != $this->image_size_default){

										$path_folder = $dir_path."/".$width."/";

										if (!file_exists($path_folder)) {
											mkdir($path_folder, 0777, true);
										}

										$uploadPath = $path_folder . $new_image_name;
									}else{
										$uploadPath = $targetPath;
									}

									$optional = [
										'width' => $width
									];
									$check_upload = $this->uploadimage($uploadPath, $imagefile, $optional);
								}

								
							} else {
								$text_formatfile = implode(',', $allowedFileType);
								$result = array(
									'error'     => 1,
									'txt'       => 'รองรับเฉพาะ ' . $text_formatfile
								);

								//return $result;
							}
							sleep(1);
						}
					}
				}

				$result = array(
					'error' => 0,
					'txt'   => 'อัพโหลดรูปสำเร็จ',
					'data'  => $img_array,
				);
			}
		}

		return $result;
	}
}
