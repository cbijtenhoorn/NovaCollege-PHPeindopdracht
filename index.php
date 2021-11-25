<html lang="en">
<head>
    <title>F i l e b r o w s e r</title>
    <link href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
function human_filesize($bytes, $dec = 2) //Functie die bytes omzet naar "normale" waarden
{
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
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
// beveiliging zodat je niet hoger kunt dan de current working directory, ook niet als je /.. in de url typt.
if(!str_contains($cwd, getcwd())){
    $cwd = getcwd();
}

// alle bestanden en mappen scannen van de huidige directory, de . en .. er af halen
$all = scandir($cwd);
$all = array_slice($all, 1);

// ---------------------------------------------------------------------------------------------------------------------


echo '<div id="breadcrumb">';
$breadcrumbs = explode('\\', str_replace(getcwd(), '', $cwd));
$breadcrumbbuilder = "";
echo '<a href="' . "index.php?dir=" . getcwd() . '">' . "root" . '</a>';
foreach ($breadcrumbs as $crumb) {
    $breadcrumbbuilder .= "/" . $crumb;
    echo '<a href="' . "index.php?dir=" . getcwd() . $breadcrumbbuilder . '">' . $crumb . '</a> ➜ ';
}
if (isset($_GET['file'])) {
    echo $_GET['file'];
}
echo '</div>';

// ---------------------------------------------------------------------------------------------------------------------

echo '<div id="dirfiles">';

if ($cwd == getcwd()) {
    $all = array_slice($all, 1);
}
foreach ($all as $item) {
    if (is_dir($cwd . '/' . $item)) {
        echo '[D] <a href="index.php?dir=' . $cwd . '/' . $item . '">' . $item . "</a><br>";
    } else {
        echo '[F] <a href="index.php?dir=' . $cwd . '&file=' . $item . '">' . $item . "</a><br>";
    }
}
echo '</div>';

// ---------------------------------------------------------------------------------------------------------------------

//Informatie over bestand
echo '<div id="contents"><b>Inhoud:</b><br>';

if (isset($_GET['file'])) {
    echo 'Bestandsnaam: ' . $_GET['file'] . '<br>';
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
    if (is_writable($file)) {
        echo 'Schrijfbaar: Ja<br>';
    } else {
        echo 'Schrijfbaar: Nee<br>';
    }
    echo 'Laatst aangepast op: ' . date("j-m-y", filemtime($file)) . '<br>';
    echo 'Bestandstype: ' . mime_content_type($file) . '<br><br>';
}

if (isset($_GET['file'])) {
    $mime = explode('/', mime_content_type($file))[0];
    $phpcheck = explode('/', mime_content_type($file))[1];

    // Weergeven van afbeelding
    if ($mime == "image") {
        $imgpath = str_replace(getcwd(), '', $file);
        $imgpath = ltrim($imgpath, '\\');

        echo '<img src="' . $imgpath . '" height="70%">';
    }

    // Bewerken van tekstbestanden
    if ($mime == "text") {
        $inhoud = file_get_contents($file);

        echo '<form method="post">
        <textarea name="textadd" rows="20" cols="20">' . htmlentities($inhoud) . '</textarea><br>';
        if($phpcheck != "html"){
           echo '<input type="submit" value="Aanpassen">';
        }
        echo '</form>';
    }
}
echo '</div>';
?>
</body>