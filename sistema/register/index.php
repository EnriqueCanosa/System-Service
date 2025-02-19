
<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Upload Excel Files</title>
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

    <form method="POST" class="form-container" action="process.php" enctype="multipart/form-data">

    <div class="upload-files-container">

    <a style="text-decoration: none;color: black;" href="https://www.inovaretextil.com/sistema/include/info.xlsx" download>
        <div class="downloadFile"> Download Excel base file 
            <span style="
            margin: 10px;
            padding: 3px;
            width: 20px; 
            height: 20px; 
            background-image: url('https://www.inovaretextil.com/sistema/layout/down.png'); 
            background-size: cover">
            </span>
        </div>
    </a>

        <h2> Upload excel file to database </h2>
        <?php
            if(isset($_SESSION['message'])){
                echo "<h3>" . $_SESSION['message'] . "</h3>"; 
            }
            unset($_SESSION['message']);
        ?>

            <input type="file" name="arquivo" id="inputArquivo" accept=".xls, .xlsx">
            <div class="botaoArquivo" id="botaoArquivo">Click here and select your file </div>
            <input type="text" placeholder="Your Name" name="nameUser" id="nameUser" onkeyup="confereDados()" >
            <input type="text" placeholder="Company Name" name="nameComp" id="nameComp" onkeyup="confereDados()" >

            <div class="errorFile" id="errorFile"> Error: you have to select a CSV file and enter name /company name </div>

            <input type="submit" id="uploadButton" class="uploadButton" disabled="disabled" value="Upload">

        
                <script> 
                    botaoArquivo = document.getElementById("botaoArquivo");
                    inputArquivo = document.getElementById("inputArquivo");
                    allowedExtensions = /(\.xls|\.xlsx)$/i;
                    inputNome = document.getElementById("nameUser");
                    inputCompania = document.getElementById("nameComp");
                    errorFile = document.getElementById("errorFile");
                    errorName = document.getElementById("errorName");
                    uploadButton = document.getElementById("uploadButton");

                    botaoArquivo.addEventListener("click", function(){
                        inputArquivo.click();
                    });
    
                    inputArquivo.addEventListener("change", function(){
                        var nome = "Click here and select your file";
                        if(inputArquivo.files.length > 0) {    
                            nome = inputArquivo.files[0].name;
                            alert ("the file " + nome + " was selected");
                            filePath = inputArquivo.value;
                            botaoArquivo.innerHTML = nome;
                            confereDados();
                        } 
                    });

                        function confereDados(){
                            if(allowedExtensions.exec(filePath) && inputNome.value != "" && inputCompania.value != ""){
                                uploadButton.style.display = "block";
                                uploadButton.disabled = false;
                                //errorFile.style.display = "none";
                            } 

                            else {
                                uploadButton.style.display = "none";
                                uploadButton.disabled = true;
                                //errorFile.style.display = "block";
                            }
                        };

                </script>


    </div>

    </form>


</body>
</html>