<?php
session_start();

/* COLOQUE AQUI A URL DO SEU SITE - DAÍ NÃO PRECISA ALTERAR NOS LUGARES DE ENVIO DE EMAIL */
$site = "https://seusite.com.br/"; // <--- troque pro seu site (não tire a barra final);

/* DOIS MODOS POSSÍVEIS -> local, producao*/
$modo = 'local'; 

//CREDENCIAIS LOCAL (XAMPP)
if($modo =='local'){
    $servidor ="localhost";
    $usuario = "root";
    $senha = "";
    $banco = "login";
}

//CREDENCIAIS PRODUÇÃO
if($modo =='producao'){
    $servidor ="";
    $usuario = "";
    $senha = "";
    $banco = "";
}

//CONEXÃO COM BANCO DE DADOS
try{
   $pdo = new PDO("mysql:host=$servidor;dbname=$banco",$usuario,$senha); 
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
   //echo "Banco conectado com sucesso!"; 
}catch(PDOException $erro){
    echo "Falha ao se conectar com o banco! ";
}

//FUNÇÃO PARA LIMPAR O POST
function limparPost($dados){
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

//FUNÇÃO PARA AUTENTICAÇÃO
function auth($tokenSessao){
    global $pdo;
    //VERIFICAR SE TEM AUTORIZAÇÃO
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? LIMIT 1");
    $sql->execute(array($tokenSessao));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    //SE NÃO ENCONTRAR O USUÁRIO
    if(!$usuario){
        return false;
    }else{
       return $usuario;
    }
}

