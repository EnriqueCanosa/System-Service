
function loadMenu() {
    
    let user = JSON.parse(localStorage.getItem('user'));

    let menuHTML = `
        <div style="
            font-family: 'Montserrat';
            display:flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
            padding: 5px 10px;
            font-size: 15px; 
            width:100%; 
            height:auto; 
            text-align: center;
            background-color:white;">
    
            <div style=" 
                height: 24px;
                width: 24px;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center; 
                background-image: url(https://www.inovaretextil.com/sistema/layout/user.png);">
            </div>
    
    	    <div style="display: flex;flex-direction: row;flex-wrap: wrap;justify-content: center;" id="user-info">
                <div style="font-weight:bold;" id="user-name"></div>
                <div style="margin:0 5px;"> | </div>
                <div id="user-tipo"></div>
            </div>
    
            <div id="logout" style="
                cursor: pointer;
                background-color: #e08b7e;
                color: black;
                padding: 8px;
                font-size: smaller;
                border-radius: 10px;">
                Exit X
            </div>
        </div>
    `;

    document.getElementById('menu-superior').innerHTML = menuHTML;
    document.getElementById('user-name').textContent = user.nome;
    document.getElementById('user-tipo').textContent = user.tipo;


    document.getElementById('logout').addEventListener('click', function() {
        console.log('logging out');
        localStorage.removeItem('user');
        window.location.href = 'https://www.inovaretextil.com/sistema/login/';
    });
}

loadMenu();
