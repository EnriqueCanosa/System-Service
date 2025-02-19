<?php session_start();

include_once '../include/conexao.php';

$ident = $_GET['codigo'];

$query = "SELECT * FROM Infos WHERE ident = :ident LIMIT 1";
$stmt = $conexao->prepare($query);
$stmt->bindValue(':ident', $ident, PDO::PARAM_STR);
$stmt->execute();
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

$id = $lista[0]["id"];
$codigo = $lista[0]["codigo"];
$fornecedor = $lista[0]["fornecedor"];
$composicao = $lista[0]["composicao"];
$gramatura = $lista[0]["gramatura"];
$largura = $lista[0]["largura"];
$desenho = $lista[0]["desenho"];
$familia = $lista[0]["familia"];
$preco = $lista[0]["preco"];
$familia = $lista[0]["familia"];
$local = $lista[0]["local"];
$link = "https://www.inovaretextil.com/sistema/layout/imagens/tecido/";
$imagem = $link . $ident . ".jpg";
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no">
<link rel="icon" href="https://www.inovaretextil.com/sistema/layout/icon.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="css.css" rel="stylesheet" type="text/css">
<title>Ficha Técnica</title>
</head>
<body>
<section class="box-degrade">
    
    <!-- MENU SUPERIOR -->
    <div id="menu-superior"></div>
    <script src="../include/addMenu.js"> </script>
    <!---->

    <div class='logo-container'> </div>

    <script> 

        function getUserData() {
            let user = localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        }

        function confereLogin() {
            let user = getUserData();
            const menuContainer = document.getElementById("menu-superior");
            //const botoesFoto = document.getElementById("foto-change");
            const logoContainer = document.querySelector(".logo-container");

            if (user) {
                console.log('User is logged in');
                console.log(user);
                
                //CARREGA O MENU SUPERIOR
                carregarMenuSuperior();
                
                //CARREGA OS BOTOES DE FOTO

                //DEIXA O LOGO LOGADO
                logoContainer.innerHTML = '<a href="https://www.inovaretextil.com/sistema/login/panel.html"><div class="logo"></div><span class="hover-text">Back to Home Panel <i class="fa-solid fa-house"></i></span></a>';
            } else {
                console.log('User is not logged in');

                //ESCONDE MENU
                menuContainer.classList.add('hidden');

                //ESCONDE BOTOES FOTO
                //botoesFoto.classList.add('hidden');

                //DEIXA O LOGO NORMAL
                logoContainer.innerHTML = '<div class="logo2"></div>';
            }
        }

        function carregarMenuSuperior() {
            const script = document.createElement("script");
            script.src = "../include/addMenu.js";
            document.getElementById("menu-superior").appendChild(script);
        }
        confereLogin();

    </script>
        <h1 class="tit"> Ficha Técnica </h1>
        <img class="img" onclick='ampliaImagem("<?php echo $imagem?>")'>

        <style>
            .img{
                background-image: url(<?php echo $imagem?>) ;
            }
        </style>

    <div class="grid">
        <div class="box">Código: <?php echo $codigo ?> </div>
        <div class="box">Fornecedor: <?php echo $fornecedor ?> </div>
        <div class="box">Composição: <?php echo $composicao ?> </div>
        <div class="box">Gramatura: <?php echo $gramatura ?> </div>
        <div class="box">Largura: <?php echo $largura ?> </div>
        <div class="box">Desenho: <?php echo $desenho ?> </div>
        <div class="box">Família: <?php echo $familia ?> </div>
        <div class="box">Local: <?php echo $local ?> </div>
        <?php 
        if(isset($_SESSION['funcao'])){
            if($_SESSION['funcao'] == 0){
                echo'<div class="box">Preço: '.$preco.' </div>';
            }
        }
        ?>
        <!--
        <div class="box">Família: <?php //echo $familia ?> </div>
        <div class="box">Subfamília: <?php //echo $subfamilia1 . " - " . $subfamilia2 ?> </div>
        -->
        <form method='POST' action='../controle/download/ficha.php'>
            <input type='hidden' name='products[]' value='<?php echo $id ?>'>
            <?php
            if(isset($_SESSION['funcao']) && $_SESSION['funcao'] == 0):
            ?>
            <input class='box btn' type='submit' value='Ficha de atendimento' name='ficha'>
            <input class='box btn' type='submit' value='Editar código' name='edit' formaction='../controle/edit.php'>
            <?php 
            endif; 
            ?>
        </form>
    </div>

</section>

    <div id="visualizacaoImagem">
        <div class="flex-itens">
            <div class="btn_fechar" id="btn_fechar" onclick="fechaImagem()">CLOSE 
                <i class="fa-regular fa-circle-xmark" style="color: #ffffff; margin-left: 10px"></i>
            </div>
            <img id="imagemAmpliada" src="">

            <?php if(isset($_SESSION['funcao']) && $_SESSION['funcao'] <= 1): ?>
            <div class="area-btns">
                <div class='btn_img' onclick="abrirCamera()"> Câmera</div>
                <div class='btn_img' onclick="abrirGaleria()"> Galeria</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
            function fechaImagem(){
                document.querySelector("#visualizacaoImagem").style.display = "none";
            };

            function ampliaImagem(img){
                document.querySelector("#visualizacaoImagem").style.display = "block";
                document.querySelector("#imagemAmpliada").setAttribute("src", img);
            };

            function abrirCamera(){
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        var video = document.getElementById('cameraStream');
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function(error) {
                        console.error("Erro ao acessar a câmera: ", error);
                    });
                } else {
                    alert("A API de acesso à câmera não é suportada pelo seu navegador.");
                }
            };

            function abrirGaleria(){

            };

    </script>
</body>

</html>