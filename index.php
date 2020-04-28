<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
</head>
<body>
<?php
	$host = 'localhost';
	$db   = 'test';
	$user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

	for($it = 1; $it < 154; $it++){
		$file = "Inp/1 (".$it.").xml";
		//Obiekt do parsowania XML
		$dom = new DOMDocument();
		//Wczytaj XML
		$dom->load($file);
		$myfile = fopen("Output_file/1 (".$it.").txt", "w");

		// tekst, który nas interesuje znajduję się pomiędzy znacznikami body
		$article = $dom->getElementsByTagName("body")->item(0);
		$counter = 0; // licznik cytowań
		foreach ($article->getElementsByTagName("xref") as $xref) {
			if ($xref->getAttribute("ref-type") == "bibr") {
				$counter++;  // inkrementacja liczby cytowań
				$text = $xref->nodeValue;
				$xref->nodeValue = "[CYTOWANIE]";
				echo $text."\r\n"; // pomocnicze wyświetlam cytowanie
			}
		}	
		// jeżeli mamy jakiekolwiek cytowanie to wykonujemy poniższy kod, w przeciwnym wypadku zajmujemy się innym plikem
		// czyli tekst który nie ma żadnego cytowania jest automatycznie odrzucony 
		if($counter > 0){
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
				$text = trim(preg_replace('/\s\s+/', ' ', $text)); // usuwamy przejście do nowej linni 
				echo $text; // podląd w przeglądarce zapisywanego tekstu
				fwrite($myfile, $text);
			}
			// zamykamy plik w którym dokonaliśmy zapisu 
			fclose($myfile);

			$myfile = "Output_file/1 (".$it.").txt";
			if ($handle = fopen($myfile, "r")) {
				$data = fread($handle, filesize($myfile));
				try {
					$pdo = new PDO($dsn, $user, $pass);
					$statement = $pdo->prepare("INSERT INTO test (text,howManyQuotes) values (?,$counter);");
            		$statement->execute([$data]);
				} catch (PDOException $ex) {
					echo "Błąd";
				}
			}
			echo $counter; // pomocnicze wyświetlam liczbę cytowań
			// $dom->save("nowy.txt"); // jakbyśmy chcieli zapisać nową wersję xml 
		}
	}
?>
</body>
</html>

