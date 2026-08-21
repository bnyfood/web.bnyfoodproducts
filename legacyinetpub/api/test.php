<?php

		

		$handle = fopen("c:\\inetpub\\injection\\injection.injection", "r");

// Output one line until end-of-file
while(!feof($handle)) {
  echo fgets($handle) . "<br>";
}
fclose($handle);
?>