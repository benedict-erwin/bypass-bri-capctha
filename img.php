<?php
/**
* Requirements:
* - tesseract 4.1.0
*	- leptonica-1.78.0
* 		- libjpeg 8d (libjpeg-turbo 1.4.2) : libpng 1.2.54 : libtiff 4.0.6 : zlib 1.2.8
* - trained data
* 	sudo wget https://github.com/tesseract-ocr/tessdata_best/raw/master/eng.traineddata -O /usr/share/tesseract-ocr/4.00/tessdata/eng.traineddata
* 	sudo wget https://github.com/tesseract-ocr/tessdata_best/raw/master/ind.traineddata -O /usr/share/tesseract-ocr/4.00/tessdata/ind.traineddata
*
* Ref:
*	- https://bingrao.github.io/blog/post/2017/07/16/Install-Tesseract-4.0-in-ubuntun-16.04.html
*/

for ($i=1; $i <= 1; $i++){
	usleep(500);
	$loc = "clean-img-10"; 
	$img = getCaptcha($loc, time());
	$clr = clearImg($img);
	$ocr = ocrImg($clr);
	$ren = rename($clr, "$loc/{$ocr}.png");
	echo $i . '. [' . date('d-m-Y H:i:s') . '] - RESULT :: ' . $ocr . PHP_EOL;	
}

function getCaptcha($path, $name) {
	$ch = curl_init('https://ib.bri.co.id/ib-bri/login/captcha');
	$pt = "{$path}/{$name}.png";
	$fp = fopen($pt, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	
	return $pt;
}

function clearImg($img) {
	#@system("convert -colorspace gray -modulate 120 -contrast-stretch 10%x80% -modulate 140 -gaussian-blur 1 -contrast-stretch 5%x50% +repage -negate -gaussian-blur 4 -negate -modulate 130 -fuzz 10% -trim \"{$img}\" \"{$img}\"");
	@system("convert -alpha off -flatten -fuzz 20% -trim +repage -white-threshold 5000 -bordercolor White -border 10x10 -type bilevel -density 300 -units PixelsPerInch \"{$img}\" \"{$img}\"");
	#@system("convert -alpha off -flatten -gamma 4 -bordercolor White -border 1 -density 300 -units PixelsPerInch \"{$img}\" \"{$img}\"");
	#@system("convert -colorspace gray -flatten -normalize -fuzz 20% -trim +repage -white-threshold 5000 -type bilevel \"{$img}\" \"{$img}\"");
	return $img;
}

function ocrImg($img){
	@system("tesseract \"{$img}\" \"{$img}\" -l ind --psm 7 --oem 3 -c tessedit_char_whitelist='0123456789' > /dev/null 2>&1");
	$text = file_get_contents($img . '.txt');
	$text = preg_replace('/[^0-9]/', '', $text);
	$text = strlen($text) === 4 ? $text:'err-' . $text;
	@unlink($img . '.txt');
	return trim($text);
}
?>