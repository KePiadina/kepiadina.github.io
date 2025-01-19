<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>KePiadina</title>
    <style>
        #code-container {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="center">
        <center>
        <?php
        clearstatcache();
        $dirPath = "/var/www/html/files/";
        $dir = $_GET['dir'];
        $fullPath = $dirPath . $dir;
        echo "<a href=\"index.html\"><h1>/".$dir."</h1></a>";
        ?>
        <div id="originalDiv">
        <a href="#" id="fetchDataBtn" onclick="activateKepicCode()">use a code</a>
        <div id="inputContainer"></div>
        </div>
        </center>
        <?php
        $dirPath = "/var/www/html/files";
        # $dirPath = getcwd();
        $dir = "/" . $_GET['dir'];
        $fullPath = $dirPath . $dir;   
        $files = scandir($fullPath);
        foreach ($files as $item) {
            $itemPath = $dirPath . $dir . "/" . $item;
            if ($item == '.') continue;
            if ($item == '..') {
                echo "<a href=\"files.php\">./</a><br>";
                continue;
            }
            if (is_dir($itemPath)) {
                echo "<a href=\"files.php?dir=$item\" class=\"dir\">" . $item . "<br>";
            } else {
                echo "<a File: href=\"/files".$dir ."/". $item . "\">" . $item . "<br>";
            }
        }
        ?>
    </div>
    <script src="kepiccodes.js"></script>
</body>
</html>
