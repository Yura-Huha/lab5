<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header('Location: login.php');
    }
    require_once('../app/classes.php');
    $bl=new EbookList();
	$bl->importFromFile('../data/ebooks.csv');
    $cl=new CategoryList();
	$cl->importFromFile('../data/categories.csv');
    if(isset($_POST['name'])){
        eval('$propsArray='.$_POST['properties'].';');
        $bl->add($_POST['brand'], $_POST['model'], 
        $_POST['category'], $_POST['price'], $propsArray);
        
        $bl->exportToFile('../data/ebooks.csv');
    }
?>
<html>
    <head>
        <title>Ebooks List</title>
        <link href="../assets/style.css" rel="stylesheet" />
    </head>
    <body>
        <div class='container'>
            <div class='navigation'>
                <ul>
                    <li><a href="ebooks.php">Електроні книги</a></li>
                    <li><a href="categories.php">Категорії</a></li>
                    <li><a href="properties.php">Властивості</a></li>
                    <li><a href="logout.php">Вийти</a></li>
                </ul>
            </div>
            <div class='table-content'>
                    <h1>Електроні книги</h1>
                    <table>
                        <thead>
                            <th>ID</th>
                            <th>Бренд(виробник)</th>
                            <th>Модель</th>
                            <th>Категорія</th>
                            <th>Ціна</th>
                            <th>Характеристики</th>
                        </thead>
                        <tbody>
                            <?php echo $bl->getTable();?>
                        </tbody>
                    </table>
            </div>
            <div class='form-content'>
                <form method="POST">
                    <p><input type="text" placeholder="Бренд(виробник)" name="brand" required/></p>
                    <p><input type="text" placeholder="Модель" name="model" required/></p>
                    <p><?php echo $cl->getDataAsSelect(); ?></p>
                    <p><input type="text" placeholder="Ціна" name="price" required/></p>
                    <p><input type="text" placeholder="Характеристики" name="properties" required/></p>
                    <p><button type="submit">Зберегти</button></p>
                </form>
            </div>
        </div>
    </body>
</html>