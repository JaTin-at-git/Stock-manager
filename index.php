<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PROTOTYPE</title>
    <!--    reset css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <!--    //clear the data, prevent form resubmission-->
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <link rel="stylesheet" href="style.css">
    <script src="js.js"></script>
</head>
<body>

<div style="color: white">

    <?php
    $tabName = "items";
    if (isset($_POST["tabName"])) {
        $tabName = $_POST["tabName"];
    }
    //    print_r($_POST)
    ?>

</div>

<!--just for background animation-->
<div class="area">
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>


<div class="mainframe">
    <div class="messageBox">
        <!--        <div class="success">This is supposed to be a success message.</div>-->
        <!--        <div class="error">This is supposed to be an error message.</div>-->
    </div>

    <?php
    if (isset($_POST["operation"])) {
        $operationType = $_POST["operation"];
        if ($operationType == "delete") {
            $itemName = $_POST['itemName'];
            echo "<script>displayMessage('warning','$itemName deleted succesfully')</script>";
        }
    }
    ?>

    <h1 class="appName">Stock Manager 📦</h1>

    <div class="main">
        <div class="search_bar"><?php include 'search_bar.php' ?></div>
        <div class="qsai"><?php
            if ($tabName == "items")
                include 'quickStats_addItem.php'
            ?></div>
        <div class="listItems"><?php
            if ($tabName == "settings")
                include 'settings.php';
            elseif ($tabName == "merchants")
                include 'merchants.php';
            elseif ($tabName == "items")
                include 'listItems.php';
            elseif ($tabName == "expenses")
                include 'expenses.php';
            elseif ($tabName == "stats")
                include 'stats.php';
            ?></div>
    </div>
    <div class="footer">
        <?php include 'footer.php' ?>
    </div>

</div>

<div class="usefullCards">
    <div id="addItemsCard">
        <?php include "addItem.php" ?>
    </div>
    <div id="addStockCard">
        <?php include "addStock.php" ?>
    </div>
    <div id="soldOtherPriceCard">
        <?php include "soldOtherPrice.php" ?>
    </div>
    <div id="moreOptionsCard">
        <?php include "moreOptions.php" ?>
    </div>
</div>

<?php
echo '<script>document.querySelector("[value=\'' . $tabName . '\']").style.backgroundColor="rgba(255, 255, 255, 1)";</script>';
?>

<script src="handleFunctionality.js"></script>

</body>
</html>