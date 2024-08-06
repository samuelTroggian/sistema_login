<?php
require('config/conexao.php');

//REQUERIMENTO DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

if(isset($_POST['email']) && !empty($_POST['email'])){
    //RECEBER OS DADOS VINDO DO POST E LIMPAR
    $email = limparPost($_POST['email']);
    $status="confirmado";

    //VERIFICAR SE EXISTE ESTE USUÁRIO COM STATUS CONFIRMADO
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND status=? LIMIT 1");
    $sql->execute(array($email,$status));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    if($usuario){
        //EXISTE O USUARIO
        //ENVIAR EMAIL PARA USUARIO FAZER NOVA SENHA
        $mail = new PHPMailer(true);
        $cod = sha1(uniqid());

         //ATUALIZAR O CÓDIGO DE RECUPERACAO DESTE USUARIO NO BANCO
         $sql = $pdo->prepare("UPDATE usuarios SET recupera_senha=? WHERE email=?");
         if($sql->execute(array($cod,$email))){

            try {
        
                //Recipients
                $mail->setFrom('sistema@emailsistema.com', 'Sistema de Login'); //QUEM ESTÁ MANDANDO O EMAIL
                $mail->addAddress($email, $nome); //PESSOA PARA QUEM VAI O EMAIL
                
                //Content
                 $mail->isHTML(true);  //CORPO DO EMAIL COMO HTML
                 $mail->Subject = 'Recuperar a senha!'; //TITULO DO EMAIL
                 $mail->Body    = '<h1>Recuperar a senha:</h1><a style="background:green; color:white; text-decoration:none; padding:20px; border-radius:5px;" href="'.$site.'login/recuperar-senha.php?cod='.$cod.'">Recuperar a senha</a><br><br><p>Equipe do Login</p>';
                 
                 $mail->send();
                 header('location: email-enviado-recupera.php');
        
        
                } catch (Exception $e) {
                    echo "Houve um problema ao enviar -email de confirmação: {$mail->ErrorInfo}";
                }

         }

        

    }else{
        $erro_usuario = "Houve uma falha ao buscar este e-mail. Tente novamente!";
    }

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
    <title>Esqueceu a senha</title>
</head>
<body>
    <form method="post">
        <h1>Recuperar Senha</h1>     

        <?php if(isset($erro_usuario)){ ?>
            <div style="text-align:center" class="erro-geral animate__animated animate__rubberBand">
            <?php  echo $erro_usuario; ?>
            </div>
        <?php } ?>
         
    <p>Informe o e-mail cadastrado no sistema</p>
        <div class="input-group">
            <img class="input-icon" src="img/user.png">
            <input type="email" name="email" placeholder="Digite seu email" required>
        </div>
              
       
        <button class="btn-blue" type="submit">Recuperar a Senha</button>
        <a href="index.php">Voltar para login</a>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>   
    
    

</body>
</html>