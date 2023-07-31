<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!--    reset css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!--    footer css-->
    <link rel="stylesheet" href="footer_css.css">

</head>
<body>

<main class="footer_main">
    <form action="index.php" method="post">
        <button class="footer_element" type="submit" name="tabName" value="settings">Settings</button>
        <button class="footer_element" type="submit" name="tabName" value="merchants">Merchants</button>
        <button class="footer_element" type="submit" name="tabName" value="items">Items</button>
        <button class="footer_element" type="submit" name="tabName" value="expenses">Expenses</button>
        <button class="footer_element" type="submit" name="tabName" value="stats">Stats</button>
    </form>
</main>

</body>
</html>