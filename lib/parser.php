<?php

function parseClippingsFile($file) {
	$txt = file_get_contents($file);

	$parts = preg_split('/[\s]+[=]{5,}[\s]+/', $txt);

	function checkmatch($section, $match, $part) {
		if (!$match) {
			header('Content-type: text/plain');
			echo 'Could not match "'.$section.'":'."\n";
			print_r($part);
			die();
		}
	}

	$db = [];
	foreach ($parts as $part) {
		if (empty($part))
			continue;

		$header = preg_split('/[\s]- (Ihre Markierung|Ihr Lesezeichen|Ihre Notiz) (bei|auf)[\s]+/i', $part);
		if (sizeof($header) < 2) {
			header('Content-type: text/plain');
			echo 'Could not parse header:'."\n";
			print_r($header);
			die();
		}


		preg_match_all('/^(.*)\s\((.*)\)/i', $header[0], $matches);

		checkmatch('Author/Title', $matches, $part);
		
		$book   = $matches[1][0];
		$author = $matches[2][0];

		preg_match_all('/Position [0-9-]+/', $header[1], $matches);
		checkmatch('Position', $matches, $part);
		$position = $matches[0][0];

		preg_match_all('/Hinzugefügt am (.*)/', $header[1], $matches);
		checkmatch('Date', $matches, $part);
		$date = trim($matches[1][0]);

		$content = explode($date, $header[1]);
		$content = trim(array_pop($content));

		if (!isset($db[$author]))
			$db[$author] = [];
		if (!isset($db[$author][$book]))
			$db[$author][$book] = [];

		$db[$author][$book][] = [
			'pos' => $position,
			'date' => $date,
			'content' => $content
		];
	}

	return $db;
}
