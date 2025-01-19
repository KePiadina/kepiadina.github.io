<html>
<style>
    body {
        font-family: "HelveticaNeue", Helvetica;
    }
    div.gallery {
            margin: 5px;
            border: 1px solid #ccc;
            float: left;
            width: 180px;
        }

        div.gallery img {
            width: 100%;
            height: auto;
            padding-left: auto;
            padding-right: auto;
        }
        .show {
            margin: auto;
            width: 66%;
        }
        img {
            max-width: 300px;
            height: auto;
        }
        .container {
            display: flex;
            align-items: center;
            padding: auto;
            place-content: center;
        }
        form {
            margin: 0;
            top: 50%;
            transform: translateY(12.5%);
        }
    </style>
    <body>
        <div class="container">
            <h1>Immagini di   ​</h1>
            <form action="cats.php" method="post">
                <select name="gatto" value="micio">
                    <option value="lupin"  <?php if($_POST["gatto"]=="lupin") echo "selected";?>>Lupin</option>
                    <option value="fiocco" <?php if($_POST["gatto"]=="fiocco") echo "selected";?>>Fiocco</option>
                    <option value="fiamma" <?php if($_POST["gatto"]=="fiamma") echo "selected";?>>Fiamma</option>
                </select>
                <input type="submit" value="Cerca">
            </form>
        </div>
    <?php
        include "connessione.php";
        $selected_option = $_POST["gatto"];

        $sql = "SELECT * FROM gatti WHERE gatto='$selected_option'";

        $result = $conn->query($sql);
        echo "<div class=\"show\">";
        while($row = $result->fetch_assoc()) {
            echo "<div class=\"gallery\">";
            echo "<img style=\"width=300px height 300px\"src=\"https://kepiadina.net/files/catpics/".$row["filename"]."\">";
            echo "</div>";
        }
        $conn->close();
        echo "</div>"
    ?>
</body>
</html>
