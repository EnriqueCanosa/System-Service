<?php 
// get options 
include_once '../include/conexao.php';

$stmt1 = $conexao->prepare("SELECT id, name FROM locals WHERE status = 1");
$stmt1->execute();
$results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $conexao->prepare("SELECT id, name FROM sub_locals WHERE status = 1");
$stmt2->execute();
$results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$stmt3 = $conexao->prepare("SELECT id, name FROM family WHERE status = 1");
$stmt3->execute();
$results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search Page</title>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
	<link rel="icon" href="https://www.inovaretextil.com/sistema/layout/icon.png">
	<link rel="stylesheet" href="css.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="main.js"> </script>
</head>
<body>
    <!-- MENU SUPERIOR -->
    <div id="menu-superior"></div>
    <script src="../include/confereLogin.js"> </script>
    <script src="../include/addMenu.js"> </script>
    <!---->

    <!-- MENU CASO SERVICE -->
    <div id="menu-service" style="display:none">
        <div class="service-container">
            <div id="serviceTitle">Service Mode</div>
            <div class="service-info">
                <div id="serviceCostumer"></div>
                <div id="serviceRep"></div>
                <div id="serviceData"></div>
            </div>
            <button id="finishServiceButton" class="btn-finish-service">Finish Service</button>
        </div>
    </div>
    <!---->

    <a href="https://www.inovaretextil.com/sistema/login/panel.html">
        <div class='logo-container'>
            <div class="logo"></div>
            <span class="hover-text">Back to Home Panel <i class="fa-solid fa-house"></i></span>
        </div>
    </a>

    <form method="POST" class="form-container" id="searchForm">
        <div class="search-container">
            <h2>Select your informations</h2>
            <input name="ident"   id="ident" type="text" placeholder="Ident">
            <input name="date1"   id="date1" type="text" placeholder="Start date (dd/mm/yyyy)" onfocus="(this.type='date')">
            <input name="date2"   id="date2" type="text" placeholder="Final date (dd/mm/yyyy)" onfocus="(this.type='date')">
            <input name="codigo"  id="codigo" type="text" placeholder="Code">
            <input name="preco"   id="preco" type="text" placeholder="Price reference">
            <input name="date3"   id="date3" type="text" placeholder="Price reference date (dd/mm/yyyy)" onfocus="(this.type='date')">

            <input name="fornecedor" id="fornecedor"  class='esconde more' type="text" placeholder="Supplier">
            <input name="composicao" id="composicao" class='esconde more' type="text" placeholder="Comp">
            <input name="gramatura" id="gramatura" class='esconde more' type="text" placeholder="Weight">
            <input name="largura" id="largura" class='esconde more' type="text" placeholder="Width">
            <input name="desenho" id="desenho" class='esconde more' type="text" placeholder="DSN NO.">

            <select name='local' class='comboBox esconde more' size='1'>
                <option value=''>Select local</option>
                <?php foreach ($results1 as $row) {
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <select name='sublocal' class='comboBox esconde more' size='1'>
                <option value=''>Select local</option>
                <?php foreach ($results2 as $row) {
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <select name="familia" class="comboBox esconde more" size="1">
                <option value=''>Select family</option>
                <?php foreach ($results3 as $row) {
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <button type="button" class="moreButton" id="view">Show more</button>
            <button type="button" class="uploadButton" id="searchButton">Search</button>
        </div>
    </form>



</body>
</html>