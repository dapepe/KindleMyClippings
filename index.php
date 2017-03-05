<?php

include('./lib/parser.php');

$db = parseClippingsFile('My Clippings.txt');

$title = 'Kindle Clippings';

if (isset($_REQUEST['author']) && isset($_REQUEST['book'])) {
	$title = $_REQUEST['author'] . ' - ' . $_REQUEST['book'];
	$content = '# '.$title."\n\n";
	foreach ($db[$_REQUEST['author']][$_REQUEST['book']] as $note) {
		$content .= '### '.$note['pos']."\n\n";
		$content .= '_'.$note['date'].'_'."\n\n";
		$content .= '> '.$note['content']."\n\n\n";
	}
} else {
	$content = '# Kindle Clippings'."\n\n";
	foreach ($db as $author => $books) {
		foreach ($books as $book => $notes) {
			$content .= '* ['.$author.' - '.$book.'](index.php?author='.urlencode($author).'&book='.urlencode($book).')'."\n";
		}
	}
}

// header('Content-type: text/plain');
// echo $content;

echo str_replace(['%%%TITLE%%%', '%%%CONTENT%%%'], [$title, $content], file_get_contents('template.html'));
