<html class="search_bar_html" lang="en">
<head>
    <!--    footer css-->
    <link rel="stylesheet" href="search_bar.css">
</head>
<body class="search_bar_body">

<form class="search_bar_form" onsubmit="event.target.parentElement.blur(); event.preventDefault();" role="search">
    <input onfocusin="search_items(event)" class="search_bar_input" id="search" type="search" placeholder="Search Items" required/>
    <button class="search_bar_button" type="submit">Go</button>
</form>

</body>
</html>