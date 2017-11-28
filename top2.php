
<?php
require_once ('php/testmodal.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PörssiGuru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/main2.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>
<body>
<header>
    <hedi>
        <div id="logo">
            <h1>PörssiGuru </h1>
        </div>
        <div id="varat">
            <h1>66,666€</h1>
        </div>
        <div class="dropdown">
            <button onclick="myFunction()" class="dropbtn">XX</button>
            <div id="myDropdown" class="dropdown-content">
                <a id="logout">Logout</a>
                <a id="login">Login</a>
            </div>

            <!-- MODAALI -->
            <div id="myModal" class="modal">
                <!-- Modaalin sisalto -->
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, eius!</p>
                    <?php
    include('php/testimodal.php');
    ?>
                </div>

            </div>

        </div>
    </hedi>
    <navi>
        <a href="top.html" class="active">TOP</a>
        <a href="osta.html">OSTA</a>
        <a href="myy.html">MYY</a>
        <a href="profiili.html">GURU</a>
    </navi>
</header>
<main>
<table>
    <tr>
        <th>GuruLista</th>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <td><img src="http://www.placecage.com/c/100/100"></td>
        <td><h2>1.</h2>Maria Anders</td>
        <td><h3>105000</h3></td>
    </tr>
    <tr>
        <td><img src=" http://www.placecage.com/100/100"></td>
        <td><h2>2.</h2>Christina Berglund</td>
        <td>102200</td>
    </tr>
    <tr>
        <td><img src=" http://www.placecage.com/c/100/100"></td>
        <td><h2>3.</h2>Francisco Chang</td>
        <td>100230</td>
    </tr>
    <tr>
        <td><img src=" http://www.placecage.com/g/100/100"></td>
        <td><h2>4.</h2>Roland Mendel</td>
        <td>16200</td>
    </tr>
    <tr>
        <td><img src=" http://www.placecage.com/c/100/100"></td>
        <td><h2>5.</h2>Helen Bennett</td>
        <td>102200</td>
    </tr>
</table>

</main>
<script src="js/droppi.js"></script>
</body>
</html>