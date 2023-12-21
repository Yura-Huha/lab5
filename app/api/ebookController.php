<?php
    session_start();
    if(!isset($_SESSION['username'])){
        echo '{"error":"Unauthorized"}';
        die();
    }
    require_once('../classes.php');
    $bl=new EbookList();
	$bl->importFromFile('../../data/ebooks.csv');
    echo $bl->convertToJSON();
    if(isset($_POST['name'])){
        eval('$propsArray='.$_POST['properties'].';');
        $bl->add($_POST['brand'], $_POST['model'], 
        $_POST['category'], $_POST['price'], $propsArray);
        
        $bl->exportToFile('../../data/ebooks.csv');
    }
?>