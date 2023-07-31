<head>
    <link rel="stylesheet" href="addItem.css">
</head>

<body>

<form class="addItemForm" onsubmit="event.preventDefault()">
    <div class="formElement addItemHeader">
        <button type="reset" class="cancel" id='close'>close</button>
        <h2>Sold in bulk or other price</h2>
    </div>
    <div class="formElement">
        <label for="itemName">Item Name *</label>
        <input disabled required type="text" id="itemName">
    </div>
    <div class="formElement">
        <label for="otherNames">Other Names</label>
        <input disabled type="text" name="itemOtherName" id="otherNames">
    </div>
    <div class="formElement">
        <label for="cp">CP per item *</label>
        <input disabled step="any" required min="0" type="number" name="itemCP" id="cp">
    </div>
    <div class="formElement">
        <label for="sp">SP per item *</label>
        <input step="any" required min="0" type="number" name="itemSP" id="sp">
    </div>
    <div class="formElement">
        <label for="stockQ">Total item sold *</label>
        <input required min="1" step="1" type="number" name="stock" id="stockQ">
    </div>
    <div class="formElement">
        <div class="addItemInfo">Profit per item: ₹<em id="ppi"></em></div>
        <div class="addItemInfo">Total profit on this sale: ₹<em id="tsp"></em></div>
    </div>
    <div class="formElement">
        <button type="submit" onclick="soldOtherPrice(event)" class="btn btn-success" id="saveAddItem">Sell</button>
        <button type="reset" class="btn btn-dark cancel">Cancel</button>
    </div>
    <input hidden name="itemID" id="itemID" value="">
</form>

</body>