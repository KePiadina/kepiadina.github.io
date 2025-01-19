<?php
if (isset($_POST['inputValue'])) {
  // Read the CSV file and find the corresponding value in the second column
  $inputValue = $_POST['inputValue'];
  $csvFile = '/var/www/kepiccodes.csv'; // Update with your CSV file path
  $file = fopen($csvFile, 'r');

  while (($line = fgetcsv($file)) !== FALSE) {
    if ($line[0] == $inputValue) {
      // Return the value of the second column
      echo $line[1];
      break;
    }
  }

  fclose($file);
}
?>
