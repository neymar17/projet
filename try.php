<!DOCTYPE html>
<html>
<head>
    <title>Recherche User</title>
</head>
<body>

<input type="text" id="search" placeholder="Tapez un nom...">
<ul id="result"></ul>

<script>
document.getElementById("search").addEventListener("keyup", function() {
    let value = this.value;

    fetch("search.php?nom=" + value)
        .then(response => response.text())
        .then(data => {
            document.getElementById("result").innerHTML = data;
        });
});
</script>

</body>
</html>
