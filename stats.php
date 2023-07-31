<head>
    <link rel="stylesheet" href="stats.css">
</head>

<body>
<div class="statsBody">
    <h3>Downloads</h3>
    <form id="downloadForm" action="php.php" method="POST" target="_blank">
        <ul class="downloadLinks">
            <li onclick="downloadXLS(event,'items','items')"><a>Download items.xls</a></li>
            <li onclick="downloadXLS(event,'sale','sale')"><a>Download sale.xls</a></li>
        </ul>
    </form>
</div>
</body>