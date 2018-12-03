<?php

class Minifier {
	private function getFiles ($dirs){
		foreach ($dirs as $dir) {
			foreach (scandir($dir) as $item) {
				if (
					strpos($item, '.') == false ||
					strpos($item, '.min.') !== false ||
					$item == '.' ||
					$item == '..'
					) {
					continue;
				}
				else
				$files[] = $dir.'/'.$item;
			}
		}
		return $files;
	}

	public function minify($dirs, $format){
		foreach ($this->getFiles($dirs) as $item) {
			// setup the URL and read the CSS from a file
			$url = $format == 'css'?
				"https://cssminifier.com/raw":
				"https://javascript-minifier.com/raw";
			
			$input = file_get_contents($item);
			// init the request, set various options, and send it
			$ch = curl_init();
	
			curl_setopt_array($ch, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
				CURLOPT_POSTFIELDS => http_build_query([ "input" => $input ])
			]);
	
			$minified = curl_exec($ch);
	
			// finally, close the request
			curl_close($ch);
	
			// output the $minified file
			$newItem = str_replace('.'.$format, '.min.'.$format, $item);
			file_put_contents($newItem, $minified);
		}
		return true;
	}
}