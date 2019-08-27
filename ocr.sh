#!/bin/bash
for i in {0..9}
	do
		convert "bri-captcha/$i.png" -flatten -fuzz 20% -trim +repage -white-threshold 5000 -type bilevel "bri-convert/$i.png"
		sleep 1
		tesseract "bri-convert/$i.png" "bri-convert/$i.png" -psm 7 -c tessedit_char_whitelist=01234567890		
		sleep 1
	done
