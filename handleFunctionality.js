if (document.querySelector(".qsai_main")) {

//display usefulcard => addItem when add item button is clicked
    document.querySelector(".addItemBtn").addEventListener('click', () => {
        showHideUsefullCard("addItemsCard");
        document.querySelector("#addItemsCard #itemName").focus();
    });


//cross and cancel buttons hides the usefull card (addItemCard, addStockCard)
    for (var closeBtn of document.querySelectorAll(".cancel"))
        closeBtn.addEventListener('click', (event) => {
            var cardID = event.target.parentNode.parentNode.parentElement.id;
            showHideUsefullCard(cardID);
        });


//function to toggle css classes to make element hide and visible
    function showHideUsefullCard(cardID) {
        document.querySelector(".usefullCards").classList.toggle('displayVisible');
        document.querySelector(`#${cardID}`).classList.toggle('displayVisible');
        document.querySelector(".mainframe").classList.toggle('blur');
    }

//function to populate addStock Fields
    function populateCard(itemPart1, card) {
        var itemName = itemPart1.querySelector(".itemName").textContent.split("₹")[0].trim();
        var itemOtherName = itemPart1.querySelector(".itemOtherName").textContent;
        var itemCP = parseFloat(itemPart1.querySelector("#itemCP").textContent);
        var itemSP = parseFloat(itemPart1.querySelector(".itemName").textContent.split("₹")[1]);
        var itemID = parseFloat(itemPart1.querySelector("#itemID").textContent);
        card.querySelector("#itemName").value = itemName;
        card.querySelector("#otherNames").value = itemOtherName;
        card.querySelector("#cp").value = itemCP;
        card.querySelector("#sp").value = itemSP;
        card.querySelector("#itemID").value = itemID;
        card.querySelector("#stockQ").focus();
    }


//calculate profit
    for (var i of document.querySelectorAll("#cp, #sp, #stockQ")) {
        i.addEventListener('keyup', (event) => {
            displayProfit(event.target)
        })
    }

    function displayProfit(target) {
        var cp = target.parentElement.parentElement.querySelector("#cp").value;
        var sp = target.parentElement.parentElement.querySelector("#sp").value;
        var stock = target.parentElement.parentElement.querySelector("#stockQ").value;
        target.parentElement.parentElement.querySelector("#ppi").textContent = (sp - cp).toString();
        target.parentElement.parentElement.querySelector("#tsp").textContent = ((sp - cp) * stock).toString();
    }

}