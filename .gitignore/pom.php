<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
</head>
<body>
<?php
	$file = "text.xml";

	//Obiekt do parsowania XML
	$dom = new DOMDocument();
	//Wczytaj XML
	$dom->load($file);

	//Główny znacznik - article
	$article = $dom->getElementsByTagName("article")->item(0);

	//Przykłady: 
	//Pobierz pierwszy znacznik front z artykułu
	$front = $article->getElementsByTagName("front")->item(0);
	//Usuń go z XML
	$article->removeChild($front);

	//Usuń wszystkie table
	$tables = $article->getElementsByTagName("table");
	foreach ($tables as $t)
	$article->remove($t);

	//Weź tekst z <italic>, wyświetl go, a następnie usuń
	$italics = $article->getElementsByTagName("italic");
	foreach($italics as $i) {
		$text = $i->nodeValue;
		echo $text . "<br>";
		$i->nodeValue = "";
	}

	//Generuj XML (wyświetla w przeglądarce)
	//echo $dom->saveXML();
	//Generuj XML (zapisz do pliku)
	$dom->save("nowy.txt");
?>
</body>
</html>