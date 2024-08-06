<?php
require('config/conexao.php');

if(isset($_GET['cod']) && !empty($_GET['cod'])){
    //LIMPAR O GET
    $cod = limparPost($_GET['cod']);

    //VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
if(isset($_POST['senha']) && isset($_POST['repete_senha'])){
    //VERIFICAR SE TODOS OS CAMPOS FORAM PREENCHIDOS
    if(empty($_POST['senha']) or empty($_POST['repete_senha'])){
        $erro_geral = "Todos os campos são obrigatórios!";
    }else{
        //RECEBER VALORES VINDOS DO POST E LIMPAR        
        $senha = limparPost($_POST['senha']);
        $senha_cript = sha1($senha);
        $repete_senha = limparPost($_POST['repete_senha']);
      
        //VERIFICAR SE SENHA TEM MAIS DE 6 DÍGITOS
        if(strlen($senha) < 6 ){
            $erro_senha = "Senha deve ter 6 caracteres ou mais!";
        }

        //VERIFICAR SE RETEPE SENHA É IGUAL A SENHA
        if($senha !== $repete_senha){
            $erro_repete_senha = "Senha e repetição de senha diferentes!";
        }
      
        if(!isset($erro_geral)  && !isset($erro_senha) && !isset($erro_repete_senha)){
            //VERIFICAR SE ESTE RECUPERACAO DE SENHA EXISTE
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE recupera_senha=? LIMIT 1");
            $sql->execute(array($cod));
            $usuario = $sql->fetch();
            //SE NÃO EXISTIR O USUARIO - ADICIONAR NO BANCO
            if(!$usuario){
                echo "Recuperação de senha inválida!";
            }else{
                //JÁ EXISTE USUARIO COM ESSE CÓDIGO DE RECUPERAÇÃO
                 //ATUALIZAR O TOKEN DESTE USUARIO NO BANCO
                $sql = $pdo->prepare("UPDATE usuarios SET senha=? WHERE recupera_senha=?");
                if($sql->execute(array($senha_cript,$cod))){
                    //REDIRECIONAR PARA LOGIN
                    header('location: index.php');
                }
               
            }
        }

    }



}

}else{
    header('location:index.php');
}



?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/estilo.css" rel="stylesheet">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
    <title>Trocar Senha</title>
</head>
<body>
    <form method="post">
        <h1>Trocar a Senha</h1>
        
        <?php if(isset($erro_geral)){ ?>
            <div class="erro-geral animate__animated animate__rubberBand">
            <?php  echo $erro_geral; ?>
            </div>
        <?php } ?>
        
        
        <div class="input-group">
            <img class="input-icon" src="img/lock.png">
            <input type="password" <?php if(isset($erro_geral) or isset($erro_senha)){echo 'class="erro-input"';}?> name="senha" placeholder="Nova Senha 6 Dígitos" <?php if(isset($_POST['senha'])){ echo "value='".$_POST['senha']."'";}?> required>
            <?php if(isset($erro_senha)){ ?>
            <div class="erro"><?php echo $erro_senha; ?></div>
            <?php } ?>     
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/lock-open.png">
            <input type="password" <?php if(isset($erro_geral) or isset($erro_repete_senha)){echo 'class="erro-input"';}?> name="repete_senha" placeholder="Repita a nova senha" <?php if(isset($_POST['repete_senha'])){ echo "value='".$_POST['repete_senha']."'";}?> required>
            <?php if(isset($erro_repete_senha)){ ?>
            <div class="erro"><?php echo $erro_repete_senha; ?></div>
            <?php } ?>                 
        </div>        
        
        <button class="btn-blue" type="submit">Alterar a Senha</button>
       
    </form>
</body>
</html>