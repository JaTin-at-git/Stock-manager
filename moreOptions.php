<head>
    <link rel="stylesheet" href="addItem.css">
    <link rel="stylesheet" href="moreOptions.css">
</head>

<body>

<form class="addItemForm" method="post" action="index.php">

    <input hidden name="itemName" id="itemName">
    <input hidden name="itemOtherName" id="otherNames">
    <input hidden name="itemCP" id="cp">
    <input hidden name="itemSP" id="sp">
    <input hidden name="itemID" id="itemID">
    <input hidden name="operation" id="operation">

    <div class="formElement addItemHeader">
        <button type="reset" class="cancel" id='close'>close</button>
        <h2>More Options</h2>
    </div>

    <div class="moreOptionsActualOptions">
        <div class="moreOptionsElement deleteItemContainer">
            <li>
                <label class="deleteItAsk" for="deleteItCheckbox">Delete this item</label>
                <input hidden type="checkbox" id="deleteItCheckbox">
                <div class="deleteItConfirm">
                    Are you sure?<br>
                    <label for="deleteItCheckbox">
                        <button type="button" onclick="event.target.parentElement.click()" class="deleteBtns btn btn-secondary">No
                        </button>
                    </label>
                    <button onclick="deleteItem(event)" class="deleteBtns btn btn-danger">Yes</button>
                </div>
            </li>
        </div>

    </div>

    <div class="formElement">

        <button type="reset" class="btn btn-dark cancel">Cancel</button>
    </div>
</form>

</body>