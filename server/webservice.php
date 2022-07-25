<?php

   /*Função de erros*/
   function error($message)
   {
      $response["status"] = 0;
      $response["error"] = $message;
      arrayJSON($response);
   }

   /*Transforma os resultados em Json*/
   function arrayJSON($response)
   {
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
      exit;
   }

   /*Cria o array para as respostas*/
   $response = array();

   /*Extrai os $_POSTs do ajax*/
   extract($_POST);

   if (!isset($type)) {
      error("Requisição Inválida");
   }

   include_once "conexao.php";

   function checkLogin()
   {
      global $con;
      if (isset($_COOKIE["token"], $_COOKIE["idusuario"])) {
         $sql = "SELECT count(*) 'qtd' FROM usuarios WHERE USUARIO_ID=:USUARIO_ID AND TOKEN=:TOKEN";
         $command = $con->prepare($sql);
         $command->bindParam(":USUARIO_ID", $_COOKIE["idusuario"]);
         $command->bindParam(":TOKEN", $_COOKIE["token"]);
         $command->execute();
         $data = $command->fetch();
         $count = $data["qtd"];
         return $count;
      }
      return 0;
   }

   if ($type == "checkCookie") {

      if (checkLogin() == 1) {
         $response["status"] = 1;
         arrayJSON($response);
      } else {
         error("Você precisa fazer o login");
      }
   } // Rotina - Login
   else if ($type == "login") {
      if (isset($user, $password)) {
         $sql = "SELECT USUARIO_ID, count(*) 'qtd' FROM usuarios WHERE LOGIN=:LOGIN AND SENHA=:SENHA GROUP BY USUARIO_ID, SENHA LIMIT 1;";
         $command = $con->prepare($sql);
         $command->bindParam(":LOGIN", $user);
         $command->bindParam(":SENHA", $password);
         $command->execute();
         $data = $command->fetch();
         $count = $data["qtd"];
         if ($count == 1) {
            $sqlToken = "UPDATE usuarios SET TOKEN=:TOKEN WHERE USUARIO_ID = :USUARIO_ID AND ATIVO = 'S'";
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $commandToken = $con->prepare($sqlToken);
            $commandToken->bindParam(":TOKEN", $token);
            $commandToken->bindParam(":USUARIO_ID", $data["USUARIO_ID"]);
            if ($commandToken->execute()) {
               setcookie("idusuario", $data["USUARIO_ID"], time() + (86400 * 60), "/");
               setcookie("token", $token, time() + (86400 * 60), "/");
               $response["status"] = 1;
               arrayJSON($response);
            } else {
               error("Erro para gerar o Token");
            }
         } else {
            error("Email ou senha incorreto");
         }
      } else {
         error("Você precisa informar o usuario e a senha");
      }
   } else if ($type == "logout") {
      if (checkLogin() == 1) {
         $idusuario = $_COOKIE["idusuario"];
         $past = time() - 3600;
         foreach ($_COOKIE as $key => $value) {
            setcookie($key, $value, $past, '/');
         }
         $sql = "UPDATE usuarios SET TOKEN=NULL WHERE USUARIO_ID = :USUARIO_ID";
         $command = $con->prepare($sql);
         $command->bindParam(":USUARIO_ID", $idusuario);
         if ($command->execute()) {
            $response["status"] = 1;
            arrayJSON($response);
         }
      }
      error("O usuário não está logado");
   } else {
      //Usuário precisa estar logado
      if (checkLogin() == 0) {
         error("Você não está logado!");
      }

      $divisao = explode("_", $type);

      if (count($divisao) == 2) {
         $tabela = $divisao[1];
         if ($tabela == "usuarios") {
            //Parte Cliente
            include_once "usuarios.php";
         } else if ($tabela == "autorizacoes") {
            //Parte Produto
            include_once "autoriza.php";
         }
      }
   }
?>