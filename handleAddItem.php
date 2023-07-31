
$data = $_POST;
if (isset($data['updateType'])) {
    if ($data['updateType']=="addItem"){
        addItemToItems($data);
    }
}