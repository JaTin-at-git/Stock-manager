<head>
    <link rel="stylesheet" href="addItem.css">
</head>

<body>

<form class="addItemForm" onsubmit="event.preventDefault()">
    <div class="formElement addItemHeader">
        <button type="reset" class="cancel" id='close'>close</button>
        <h2>Add Item</h2>
    </div>
    <div class="formElement">
        <label for="itemName">Item Name *</label>
        <input required type="text" name="itemName" id="itemName">
    </div>
    <div class="formElement">
        <label for="otherNames">Other Names</label>
        <input type="text" name="itemOtherName" id="otherNames">
    </div>
    <div class="formElement">
        <label for="cp">CP per item *</label>
        <input step="any" required min="0" type="number" name="itemCP" id="cp">
    </div>
    <div class="formElement">
        <label for="sp">SP per item *</label>
        <input step="any" required min="0" type="number" name="itemSP" id="sp">
    </div>
    <div class="formElement">
        <label for="stockQ">Total new stock *</label>
        <input required min="1" step="1" type="number" name="stock" id="stockQ">
    </div>
    <div class="formElement">
        <div class="addItemInfo">Profit per item: ₹<em id="ppi"></em></div>
        <div class="addItemInfo">Total stock profit: ₹<em id="tsp"></em></div>
    </div>
    <div class="formElement swapPlaces">
        <button type="submit" onclick="addItem()" class="btn btn-success" id="saveAddItem">Save</button>
        <button type="reset" class="btn btn-dark cancel">Cancel</button>
    </div>
    <input style="display: none" name="updateType" value="addItem">

</form>

</body>