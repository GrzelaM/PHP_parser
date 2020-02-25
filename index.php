<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
</head>
<body>
<?php
	$file = "Input_file/inp3.xml";
	//Obiekt do parsowania XML
	$dom = new DOMDocument();
	//Wczytaj XML
	$dom->load($file);
	$myfile = fopen("Output_file/outp3.txt", "w");

	// tekst, który nas interesuje znajduję się pomiędzy znacznikami body
	$article = $dom->getElementsByTagName("body")->item(0);
	$counter = 0; // licznik cytatów
	foreach ($article->getElementsByTagName("xref") as $xref) {
		if ($xref->getAttribute("ref-type") == "bibr") {
			//$xref jest cytatem, zastepujemy standardowy cytat napisem: [cytat]
			//$xref->nodeValue = "[cytat]";
			$counter++;  // inkrementacja liczby cytatów
			$text = $xref->nodeValue;
			// sprawdzamy czy pobrany text to [1] czy 1, ponieważ w jednym przypadku
			// mamy: [<tag>1<tag>] i zamienienie tego na [cytat] dałoby nam [[cytat]]
			// mamy też 3 przypadek ...<tag>1<tag>.... 
			// sprawdzanie odbywa się tak jeżeli mamy [1] to zamieniamy na [cytat]
			// natomiast jeżeli mamy 1, ale przed mamy [ i za mamy ] to dajemy samo cytat,
			// jeżeli mamy 1, ale przed nie mamy [ i za też nie mamy ] to znowu dajemy [cytat]
			if(strpos($text, "[") !== false){ 
				$xref->nodeValue = "[cytat]"; 
			} else{							  
				if(strpos($xref->getAttribute("rid"), "CIT") !== false){
					$xref->nodeValue = "cytat";
				}else{ 							
					$xref->nodeValue = " [cytat]";
				}		
				
			}
			echo $text."\r\n"; // pomocnicze wyświetlam cytaty
		}
		
	}
	// wyrzucamy media - czyli odnośniki do nagrań itp.
	// w przeciwnym wypadku pobiera nam się komunikat - Click here for additional data file.
	$media = $article->getElementsByTagName("media");
	foreach($media as $m) {
		$text = $m->nodeValue;
		$m->nodeValue = "";
	}

	// pomiędzy znacznikami p znajduję się czysty tekst bez grafik, wykresów, tabel itp.
	$p = $article->getElementsByTagName("p");
	foreach($p as $i) {
		$text = $i->nodeValue;
		$text = trim(preg_replace('/\s\s+/', ' ', $text)); // usuwamy przejście do nowej linni (teoretcznie cały tekst będzie jedną liną)
		echo $text; // podląd w przeglądarce zapisywanego tekstu
		fwrite($myfile, $text);
	}

	// zamykamy plik w którym dokonaliśmy zapisu 
	echo $counter; // pomocnicze wyświetlam liczbę cytatów
	fclose($myfile);
	// $dom->save("nowy.txt"); // jakbyśmy chcieli zapisać nową wersję xml 
?>
</body>
</html>

