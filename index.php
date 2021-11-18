<html lang="en">
<head>
    <title>F i l e b r o w s e r</title>
    <link href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
function human_filesize($bytes, $dec = 2)
{
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

if (isset($_GET['dir']) && isset($_GET['file'])) {
    $file = $_GET['dir'] . '/' . $_GET['file'];
}

if (isset($_POST['textadd'])) {
    $textadd = $_POST['textadd'];
    file_put_contents($file, $textadd);
}

// huidige werkdirectory ophalen
$cwd = getcwd();

if (isset($_GET['dir'])) {
    $cwd = $_GET['dir'];
    $cwd = realpath($cwd);
}

// alle bestanden en mappen scannen van de huidige directory, de . en .. er af halen
$all = scandir($cwd);
$all = array_slice($all, 1);

echo '<div id="dirfiles">';

foreach ($all as $item) {
    if (is_dir($cwd . '/' . $item)) {
        echo '[D] <a href="index.php?dir=' . $cwd . '/' . $item . '">' . $item . "</a><br>";
    } else {
        echo '[F] <a href="index.php?dir=' . $cwd . '&file=' . $item . '">' . $item . "</a><br>";
    }
}
echo '</div>';


//Informatie over bestand
echo '<div id="contents"><b>Inhoud:</b><br>';

if (isset($_GET['file'])) {
    //Bestandsnaam:
    echo 'Bestandsnaam: ' . $_GET['file'] . '<br>';

    //Bestandsgrootte:
    $bytes = filesize($file);
//    if ($bytes < 1024) {
//        echo 'Bestandsgrootte: ' . number_format($bytes,1) . ' bytes<br>';
//    } elseif ($bytes >= 1024 && $bytes < 1048576) {
//        $bytes = $bytes / 1024;
//        echo 'Bestandsgrootte:' . number_format($bytes,1) . ' kB<br>';
//    } elseif ($bytes >= 1048576 && $bytes < 1073741824) {
//        $bytes = $bytes / 1048576;
//        echo 'Bestandsgrootte: ' . number_format($bytes,1) . ' mB<br>';
//    } else {
//        $bytes = $bytes / 1073741824;
//        echo 'Bestandsgrootte: ' . number_format($bytes,1) . 'gB<br>';
//    }
    echo "Bestandsgrootte: " . human_filesize($bytes) . "<br>";

    //Rechten/Schrijfbaar:
    if (is_writable($file)){
        echo 'Schrijfbaar: Ja<br>';
    }
    else{
        echo 'Schrijfbaar: Nee<br>';
    }

    //Laatst aangepast:
    echo 'Laatst aangepast op: ' . date("j-m-y", filemtime($file)) . '<br>';

    //Bestandstype:
    echo 'Bestandstype: ' . mime_content_type($file) . '<br><br>';
}





if(isset($_GET['file'])){
    $mime = explode('/', mime_content_type($file))[0];

    // Weergeven van afbeelding
    if($mime == "image"){
        //echo 'Hoi ik ben een afbeelding ja leuk' . '<br>';
        //echo $file;
        $imgpath = str_replace(getcwd(), '', $file);
        $imgpath = ltrim($imgpath, '\\');
//        echo $imgpath . '<br>';
//        echo getcwd() . '<br>';
//        echo $file;
        echo '<img src="' . $imgpath . '" alt="afbeelding">';

    }

    // Bewerken van tekstbestanden
    if ($mime == "text") {
        //echo "Leuk! Ik ben een bestand wat je mag openen!<br>";
        $inhoud = file_get_contents($file);

        echo '<form method="post">
        <textarea name="textadd" rows="20" cols="20">' . htmlentities($inhoud) . '</textarea><br>
        <input type="submit" value="Aanpassen">
        </form>';
    }
}
echo '</div>';
?>

</body>
