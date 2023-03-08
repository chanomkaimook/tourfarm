<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Barcode
{
	private $path_barcode = FCPATH . 'asset/image/barcode/';

	public function __construct()
	{
	}

	/**
	 * generate barcode
	 *
	 * @param String|null $code = 
	 * @return void
	 */
	function generate_image_barcode(String $code = null)
	{
		//=	 call database	=//
		$ci = &get_instance();
		$ci->load->database();
		//===================//

		// $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$border = 2; //กำหนดความหน้าของเส้น Barcode
		$height = 40; //กำหนดความสูงของ Barcode

		$img_barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128, $border, $height);
		$path_upload = $this->path_barcode . $code . ".png";

		if (!file_exists($this->path_barcode)) {
			mkdir($this->path_barcode, 0777, true);

			//  save image on director 
			file_put_contents($path_upload, $img_barcode);
		} else {
			//  save image on director 
			file_put_contents($path_upload, $img_barcode);
		}
	}

	/**
	 * random text
	 *
	 * @param integer|null $range = length
	 * @return void
	 */
	function randText(int $range = null)
	{
		# code...
		// $char = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ123456789';	////	cut o
		/* $char = '0123456789';	////	cut o
		$start = rand(1, (strlen($char) - $range));
		$shuffled = str_shuffle($char);
		return substr($shuffled, 0, $range); */

		$digits = 15;
		return str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
	}
}
