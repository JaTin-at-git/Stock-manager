function displayMessage(typeClass, message) {
    var msgElement = document.createElement("div");
    msgElement.classList.add(typeClass);
    msgElement.innerHTML = message.toUpperCase();
    document.querySelector(".messageBox").appendChild(msgElement);
    setTimeout(() => {
        document.querySelector(".messageBox").removeChild(msgElement)
    }, 4000);
}

//create html for item, input it at appropriate place
async function displayItem(itemID, itemName, itemOtherName, itemCP, itemSP, itemStock, insertType = "append") {
    itemCP = (itemCP % 1) === 0 ? parseInt(itemCP) : itemCP;
    itemSP = (itemSP % 1) === 0 ? parseInt(itemSP) : itemSP;
    var item = document.createElement("div");
    item.classList.add("item");
    item.innerHTML = `
        <div class="itemPart1">
            <div id="itemID">${itemID}</div>
            <div id="itemCP">${itemCP}</div>
            <div class="itemName">${itemName}
                <n class="green">₹${itemSP}</n> 
            </div>
            <div class="itemOtherName">${itemOtherName}</div>
            <div class="stock">Stock:
                <n class="stockQuantity">${itemStock}</n>
            </div>
            <a onclick="event.preventDefault()" href="" class="addStock">add stock</a>
        </div>
        <div class="itemPart2">
            <button onclick="changeStock('soldDefault',${itemID},'${itemName}','${itemOtherName}',${itemCP},${itemSP},-1)" class="btn soldAtDefault">Sold at
                <n class="defaultSellingPrice">₹${itemSP}</n>
            </button>
            <button class="btn soldAtOther">Bulk sell /other price</button>
            <div class="moreOptions">•••</div>
        </div>
    `;

    // add event listeners here
    item.querySelector(".addStock").addEventListener('click', (event) => {
        showHideUsefullCard("addStockCard");
        populateCard(event.target.parentElement, document.querySelector("#addStockCard"));
    });
    item.querySelector(".moreOptions").addEventListener('click', (event) => {
        showHideUsefullCard("moreOptionsCard");
        populateCard(event.target.parentElement.parentElement.querySelector(".itemPart1"), document.querySelector("#moreOptionsCard"));
    });
    item.querySelector(".soldAtOther").addEventListener('click', (event) => {
        showHideUsefullCard("soldOtherPriceCard");
        populateCard(event.target.parentElement.parentElement.querySelector(".itemPart1"), document.querySelector("#soldOtherPriceCard"));
        document.querySelector("#soldOtherPriceCard #sp").focus();
    });

    if (insertType === "prepend") {
        document.querySelector(".display").prepend(item);
        item.scrollIntoView();
    } else document.querySelector(".display").appendChild(item);

    recolorBackground(itemStock, item);

    return true;
}

//function to display next n number of items
async function displayNextNItems(startingIndex, numberOfItems, insertType = "append", search = "") {
    return new Promise(resolve => {


        // get data from php
        var data = {
            requestType: "selectNItems", startingIndex: startingIndex, numberOfItems: numberOfItems, search: search
        }

        $.post("php.php", data).then(async (response) => {
            var items = response.split("|");
            console.log(items);
            if (items[0] === '') {
                displayMessage("warning", "No more items");
                document.querySelector("#loadMore").classList.add("displayNone");
                return false;
            }
            for (const item of items) {
                // itemID, itemName, itemOtherName, itemCP, itemSP, itemStock respectively are
                var data = item.split("@");
                await displayItem(data[0], data[1], data[2], data[3], data[4], data[5], insertType);
            }
            updateQSAI_data();
            return true;
        }).then(() => {
            document.querySelector("#loadMore").classList.remove("displayNone");
            document.querySelector("#loadMore").setAttribute("onclick",
                `displayNextNItems(${document.querySelectorAll(".item").length},${numberOfItems},\"append\","${search}")`);
            resolve("items displayed");
            return true;
        })
    });

}

function changeStock(changeType, id, name, otherName, cp, sp, changeInStock) {
    var data = {
        requestType: "stockChange",
        itemID: id,
        itemName: name,
        itemOtherName: otherName,
        itemCP: cp,
        itemSP: sp,
        changeInStock: changeInStock,
        changeType: changeType
    }

    $.post("php.php", data).then((request) => {
        var stockRemaining = parseInt(request);
        if (!isNaN(stockRemaining)) {
            if (changeType === "addStock") {
                document.querySelector("#addStockCard .cancel").click();
                displayMessage('success', `${changeInStock} ${name} added to Stock.`);
            } else if (changeType === "soldDefault") {
                displayMessage('success', `${name} sold at ₹${sp}`)
            } else if (changeType === "sellStock") {
                document.querySelector("#soldOtherPriceCard .cancel").click();
                displayMessage('success', `${-changeInStock} ${name} sold at ₹${sp} each`)
            }
            //display correct value of stock here
            //     find element using xpath
            // Select the element using XPath
            const idDiv = document.evaluate(`//div[@id='itemID' and text()='${id}']`, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
            idDiv.parentNode.querySelector(".stockQuantity").textContent = stockRemaining.toString();

            //if stock is empty, change the background color
            recolorBackground(stockRemaining, idDiv.parentElement.parentElement);
            updateQSAI_data();
        } else {
            if (changeType === "sellStock") alert(request); else displayMessage("error", request);
        }
    });
}

function addItem() {
    var itemName = document.querySelector(".addItemForm [name='itemName']").value;
    var itemOtherName = document.querySelector(".addItemForm [name='itemOtherName']").value;
    var itemCP = document.querySelector(".addItemForm [name='itemCP']").value;
    var itemSP = document.querySelector(".addItemForm [name='itemSP']").value;
    var stock = document.querySelector(".addItemForm [name='stock']").value;


    var data = {
        requestType: "addItem",
        itemName: itemName,
        itemOtherName: itemOtherName,
        itemCP: itemCP,
        itemSP: itemSP,
        stock: stock
    };

    if (!itemName || !itemCP || !itemSP || !stock || itemCP < 0 || itemSP < 0 || stock < 0) {
        return;
    }

    $.post("php.php", data).then((response) => {
        console.log(response)
        var items = response.split("|");
        var data = items[0].split("@");
        displayItem(data[0], data[1], data[2], data[3], data[4], data[5], "prepend");
    }).then(() => {
        document.querySelector(".addItemForm .cancel").click();
        updateQSAI_data();
        displayMessage("success", `${itemName} added successfully`)
    });
}


//maybe merge addStock and sellStock
function addStock(event) {
    // event.preventDefault();
    var form = event.target.parentElement.parentElement;

    var itemID = form.querySelector("#itemID").value;
    var itemName = form.querySelector("#itemName").value;
    var itemOtherName = form.querySelector("#otherNames").value;
    var itemCP = form.querySelector("#cp").value;
    var itemSP = form.querySelector("#sp").value;
    var stockChange = form.querySelector("#stockQ").value;


    if (!itemName || !itemCP || !itemSP || !stockChange || itemCP < 0 || itemSP < 0 || stockChange < 0) {
        return;
    }

    console.log(itemID)
    console.log(itemName)
    console.log(itemOtherName)
    console.log(itemCP)
    console.log(itemSP)
    console.log(stockChange)

    changeStock("addStock", itemID, itemName, itemOtherName, itemCP, itemSP, stockChange);
}

function soldOtherPrice(event) {
    // event.preventDefault();
    var form = event.target.parentElement.parentElement;

    var itemID = form.querySelector("#itemID").value;
    var itemName = form.querySelector("#itemName").value;
    var itemOtherName = form.querySelector("#otherNames").value;
    var itemCP = form.querySelector("#cp").value;
    var itemSP = form.querySelector("#sp").value;
    var itemsToSell = form.querySelector("#stockQ").value;

    if (!itemName || !itemCP || !itemSP || !itemsToSell || itemCP < 0 || itemSP < 0 || itemsToSell < 0) {
        return;
    }

    console.log(itemID)
    console.log(itemName)
    console.log(itemOtherName)
    console.log(itemCP)
    console.log(itemSP)
    console.log(itemsToSell)

    changeStock("sellStock", itemID, itemName, itemOtherName, itemCP, itemSP, -itemsToSell);
}

function deleteItem(event) {
    event.preventDefault();
    var form = event.target.parentElement.parentElement.parentElement.parentElement.parentElement;

    var itemID = form.querySelector("#itemID").value;
    var itemName = form.querySelector("#itemName").value;
    var itemOtherName = form.querySelector("#otherNames").value;
    var itemCP = form.querySelector("#cp").value;
    var itemSP = form.querySelector("#sp").value;

    form.querySelector("#operation").value = "delete";


    if (!itemName || !itemCP || !itemSP || itemCP < 0 || itemSP < 0) {
        return;
    }

    //
    // console.log(itemID)
    // console.log(itemName)
    // console.log(itemOtherName)
    // console.log(itemCP)
    // console.log(itemSP)

    $.post("php.php", {id: itemID, requestType: "delete"}).then((response) => {
        if (response === "success") {
            form.submit();
            //the page will refresh, and qsai data will update itself
        } else {
            alert("Failed to delete this item, please reload the page.");
        }
    });
}


function updateQSAI_data() {
    $.post("php.php", {requestType: "getQSAI"}).then((response) => {
        console.log(response)
        var data = response.split(",");
        var totalItems = parseInt(data[0]);
        var totalSale = parseFloat(data[1]);
        var profit = parseFloat(data[2]);
        var profitP = parseFloat(data[3]);
        var qsai = document.querySelector(".qsai");
        console.log(totalItems);
        console.log(totalSale);
        console.log(profit);
        console.log(profitP);

        qsai.querySelector("#totalItems").textContent = totalItems.toString();
        qsai.querySelector("#totalSaleToday").textContent = totalSale.toString();
        qsai.querySelector("#totalProfitToday").textContent = `${profit}(${profitP}%)`;

    });
}

function recolorBackground(stockRemaining, element) {
    // console.log(stockRemaining)
    // console.log(element)
    // console.log()
    if (stockRemaining <= 0) {
        element.classList.add("emptyStock");
    } else {
        element.classList.remove("emptyStock");
    }
}

async function search_items(prevQuery, freq = 1000) {
    if (document.activeElement !== document.querySelector("#search")) {
        console.log('focus away');
        return;
    }

    console.log("searching...");

    // wait for a second, check if search is equal, if equal show result, else wait till equal
    var searchQuery = document.querySelector("#search").value;
    var changesOccurred = prevQuery !== searchQuery;

    if (changesOccurred) {
        console.log("changes encountered: ");
        while (true) {
            searchQuery = document.querySelector("#search").value;
            await new Promise((resolve) => {
                setTimeout(() => {
                    resolve("check again");
                }, freq);
            });
            var searchQuery2 = document.querySelector("#search").value;
            console.log(prevQuery, " ", searchQuery, " ", searchQuery2);

            if (searchQuery === searchQuery2) {
                document.querySelector(".display").innerHTML = '';
                if (searchQuery2 === '') {
                    await displayNextNItems(0, 10);
                } else {
                    await displayNextNItems(0, 15, "append", searchQuery2).then(() => {
                        document.querySelector("#loadMore").classList.add("displayNone");
                    });
                }
                await search_items(searchQuery2);
                break;
            } else {
                console.log("waiting to get stable")
            }
        }
    } else {
        await new Promise((resolve) => {
            setTimeout(() => {
                resolve("check again");
            }, freq);
        });
        console.log("rechecking");
        await search_items(searchQuery);
    }
}

function downloadXLS(event, tablename, filename) {
    var form = document.querySelector("#downloadForm");

    var inputFileName = document.createElement("input");
    inputFileName.setAttribute("name", "filename");
    inputFileName.setAttribute("value", filename);
    form.appendChild(inputFileName);

    var inputTableName = document.createElement("input");
    inputTableName.setAttribute("name", "tablename");
    inputTableName.setAttribute("value", tablename);
    form.appendChild(inputTableName);

    var inputRequestType = document.createElement("input");
    inputRequestType.setAttribute("name", "requestType");
    inputRequestType.setAttribute("value", "downloadXLS");
    form.appendChild(inputRequestType);

    form.submit();

    form.removeChild(inputTableName);
    form.removeChild(inputFileName);
    form.removeChild(inputRequestType);

}