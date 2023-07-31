<?php

//establishing connection and making a table items


//maybe input them through password
$servername = "localhost";
$username = "root";
$password = "";


// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
//    echo "<script>displayMessage('error','connection Failed.')</script>";
    die("Connection failed: " . $conn->connect_error);
} else {
//    echo "<script>displayMessage('success','connected to database.')</script>";
}

//create prototype database if it does not exist
// Database name to check
$dbName = "prototype1";
// Check if the database exists
$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    //pass
//    echo "<script>displayMessage('success','connected to $dbName.')</script>";
} else {
// Create a new database
    $createDbQuery = "CREATE DATABASE $dbName";
    if ($conn->query($createDbQuery) === true) {
//        echo "<script>displayMessage('success','Database $dbName created successfully.')</script>";
    } else {
//        echo "<script>displayMessage('error','Error creating database: ' . $conn->error )</script>";
    }
}

$conn->query("use prototype1");

//sql to create the table items
$sql = "CREATE TABLE items (
  id smallint AUTO_INCREMENT PRIMARY KEY,
  itemName VARCHAR(30) NOT NULL,
  itemOtherName varchar(30),
  itemCP decimal(6,2) not null ,
  itemSP decimal(6,2) not null ,
  itemStock smallint,
  totalQuantityEverSold int default 0,
  totalProfitEverMade int default 0,
  lastUpdate datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
";

try {
    $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    //pass
}

//sql to create the table items
$sql = "CREATE TABLE sale
(
    id           smallint AUTO_INCREMENT PRIMARY KEY,
    item_id      smallint,
    soldCP       decimal(6, 2) not null CHECK ( soldCP >= 0 ),
    soldSP       decimal(6, 2) not null CHECK ( soldSP >= 0 ),
    soldQuantity smallint      NOT NULL CHECK ( sale.soldQuantity >= 0 ),
    soldDate     datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    foreign key (item_id) references items (id) on delete set null
);
";

try {
    $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    //pass
}

$sql = "create table logs
(
    id           smallint AUTO_INCREMENT PRIMARY KEY,
    data         varchar(200),
    dateTime     datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
";


try {
    $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    //pass
}


function feedLogs()
{
    global $conn;
    $data_string = http_build_query($_POST);
    $query = "insert into logs(data) values ('$data_string');";
    $conn->query($query);
}


//this file contains useful functions that will be used through the app

if (isset($_POST["requestType"])) {
    $requestType = $_POST["requestType"];
    if ($requestType == "selectNItems") {
        $startingIndex = $_POST["startingIndex"];
        $numberOfItems = $_POST["numberOfItems"];
        $search = isset($_POST["search"]) ? $_POST["search"] : '';
        echo returnNItemDetails($startingIndex, $numberOfItems, $search);
    } elseif ($requestType == "addItem") {
        feedLogs();
        echo addItemToItems($_POST);
    } elseif ($requestType == "stockChange") {
        feedLogs();
        updateItem($_POST);
    } elseif ($requestType == "delete") {
        feedLogs();
        delete($_POST);
    } elseif ($requestType == "getQSAI") {
        getQSAI_data();
    } elseif ($requestType == "downloadXLS"){
        print_r($_POST);
        downloadXLS($_POST["tablename"],$_POST["filename"]);
    }
}


//function to display all items available in the database
function returnNItemDetails($startingIndex, $numberOfItems, $search = '')
{
    $itemDetails = [];
    global $conn;

    if ($search == '')
        $query = "SELECT * FROM `items` ORDER BY lastUpdate DESC limit $startingIndex, $numberOfItems;";
    else {
        $search = "%" . implode("%", str_split($search)) . "%";
        $query = "select * from items where concat(itemName, itemOtherName, itemSP) LIKE '$search';";
    }
    $result = $conn->query($query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $itemID = $row["id"];
            $itemName = $row["itemName"];
            $itemOtherName = $row["itemOtherName"];
            $itemCP = $row["itemCP"];
            $itemSP = $row["itemSP"];
            $itemStock = $row["itemStock"];
            $itemDetail = "$itemID@$itemName@$itemOtherName@$itemCP@$itemSP@$itemStock";
            $itemDetails[] = $itemDetail;
        }
    }
    $conn->close();
    return implode("|", $itemDetails);
}


//function to add new item to the database
function addItemToItems($data)
{
    global $conn;
    $itemName = $data['itemName'];
    $itemOtherName = $data['itemOtherName'];
    $itemCP = $data['itemCP'];
    $itemSP = $data['itemSP'];
    $stock = $data['stock'];
    $query = "insert into items (itemName, itemOtherName, itemCP, itemSP, itemStock) values ( '$itemName' ,  '$itemOtherName', $itemCP, $itemSP ,$stock ); ";
    $conn->query($query);
    $itemID = mysqli_fetch_assoc($conn->query("select id from items order by lastUpdate desc limit 0,1"))["id"];
    $conn->close();
    return ("$itemID@$itemName@$itemOtherName@$itemCP@$itemSP@$stock");
}


function updateItem($data)
{
    global $conn;
    $itemID = $data['itemID'];
    $itemName = $data['itemName'];
    $itemOtherName = $data['itemOtherName'];
    $itemCP = $data['itemCP'];
    $itemSP = $data['itemSP'];
    $changeInStock = $data['changeInStock'];
    $changeType = $data['changeType'];

    $currentStock = mysqli_fetch_assoc(($conn->query("select (itemStock) from items where id=$itemID")))['itemStock'];
    $stock = $changeInStock + $currentStock;
    if ($stock < 0) {
        if ($changeType == "sellStock")
            echo "Items to sell out of range, max stock is $currentStock. Please enter appropriate number.";
        else
            echo "Stock is empty!";
        $conn->close();
        return false;
    }


    if ($changeType == "sellStock" || $changeType == "soldDefault") {
        if (!sellItem($itemID, $itemCP, $itemSP, -$changeInStock)) {
            return false;
        }
    }

    if ($changeType == "sellStock") {
        $itemSP = mysqli_fetch_assoc(($conn->query("select (itemSP) from items where id=$itemID")))['itemSP'];
    }

    $query = "
    update items 
    set 
        itemName = '$itemName',
        itemOtherName = '$itemOtherName',
        itemCP =  $itemCP,
        itemSP = $itemSP,
        itemStock = $stock
     where id=$itemID; 
    ";
    $result = $conn->query($query);
    $conn->close();
    echo $stock;
    return $result;
}

function delete($data)
{
    global $conn;
    $id = $data['id'];
    $query = "
    delete from items
    where id = $id
    ";
    $result = $conn->query($query);
    if ($result)
        echo "success";
    else
        echo "failure";

    $conn->close();
    return $result;
}

function sellItem($itemID, $itemCP, $itemSP, $itemQuantity)
{
    global $conn;
    $query = "
    insert into sale(item_id, soldCP, soldSP, soldQuantity)
    values ($itemID, $itemCP, $itemSP, $itemQuantity);
    ";
    //connection shall not be closed.
    return $conn->query($query);
}

function getQSAI_data()
{
    global $conn;
    $data = mysqli_fetch_assoc($conn->query("
        select ifnull(sum(soldSP * soldQuantity),0)                                                            as sale,
        ifnull(sum(soldSP * soldQuantity) - sum(soldCP * soldQuantity),0)                                   as profit,
        round(ifnull((sum(soldSP * soldQuantity) - sum(soldCP * soldQuantity)) / sum(soldCP * soldQuantity) * 100 ,0),2) as 'profitP'
        from sale
        where cast(soldDate as DATE) = cast(sysdate() as DATE);
    "));
    $sale = $data["sale"];
    $profit = $data["profit"];
    $profitP = $data["profitP"];

    $totalItems = mysqli_fetch_assoc($conn->query("
    select count(id) as totalItems from items;
    "))['totalItems'];

    $conn->close();
    echo "$totalItems,$sale,$profit,$profitP";
}

function downloadXLS($DB_TBLName, $filename)
{
    global $conn;
    $file_ending = "xls";   //file_extention

    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$filename.$file_ending");
    header("Pragma: no-cache");
    header("Expires: 0");

    $sep = "\t";

    $sql = "SELECT * FROM $DB_TBLName";
    $resultt = $conn->query($sql);
    while ($property = mysqli_fetch_field($resultt)) { //fetch table field name
        echo $property->name . "\t";
    }

    print("\n");

    while ($row = mysqli_fetch_row($resultt))  //fetch_table_data
    {
        $schema_insert = "";
        for ($j = 0; $j < mysqli_num_fields($resultt); $j++) {
            if (!isset($row[$j]))
                $schema_insert .= "NULL" . $sep;
            elseif ($row[$j] != "")
                $schema_insert .= "$row[$j]" . $sep;
            else
                $schema_insert .= "" . $sep;
        }
        $schema_insert = str_replace($sep . "$", "", $schema_insert);
        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
        $schema_insert .= "\t";
        print(trim($schema_insert));
        print "\n";
    }

    $conn->close();
}
