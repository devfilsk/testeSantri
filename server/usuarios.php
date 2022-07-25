<?php
   if ($type == "dados_usuarios") {
      $sql = "SELECT * FROM `usuarios` 
                INNER JOIN autorizacoes ON autorizacoes.USUARIO_ID = usuarios.USUARIO_ID 
                WHERE usuarios.USUARIO_ID = :USUARIO_ID";
      $command = $con->prepare($sql);
      $command->bindParam(":USUARIO_ID", $_COOKIE["idusuario"]);
      $command->execute();
      $data = $command->fetch();
      arrayJSON($data);
   } else if ($type == "cadastrar_usuarios") {
      $sql = "INSERT INTO usuarios VALUES(0,:LOGIN, :SENHA, :ATIVO, :NOME_COMPLETO, NULL)";
      $command = $con->prepare($sql);
      $command->bindParam(":LOGIN", $login);
      $command->bindParam(":SENHA", $senha);
      $command->bindParam(":ATIVO", $ativo);
      $command->bindParam(":NOME_COMPLETO", $nome_completo);
      if ($command->execute()) {
         $ultimoId = $con->prepare("SELECT LAST_INSERT_ID(MAX(USUARIO_ID)) FROM usuarios");
         $ultimoId->execute();
         $idusuario = $ultimoId->fetch();

         $permissoes = array();
         $amountFiles = count($_POST['contador']);

         for($i =0; $i < $amountFiles; $i++){
            if( $_POST['opt_cadastrar_clientes'] == 'cadastrar_clientes') {
               array_push($permissoes, 'cadastrar_clientes');
            }
            if($_POST['opt_mais'] == 'mais') {
               array_push($permissoes, 'mais');
            }
            if($_POST['opt_excluir_clientes'] == 'excluir_clientes') {
               array_push($permissoes, 'excluir_clientes');
            }
         }



         foreach ($permissoes as $key => $value) {
            $sql = "INSERT INTO autorizacoes VALUES (:USUARIO_ID , '" . implode(",", $permissoes) . "')";;
            $command = $con->prepare($sql);
            $command->bindParam(":USUARIO_ID", $idusuario[0]);
            if ($command->execute()) {
               $response["status"] = 1;
               arrayJSON($response);
            } else {
               error("Erro ao cadastrar o usuario, verifique as informações");
            }
         }
      }
   } else if ($type == "listar_usuarios") {
      $sql = "SELECT * FROM usuarios WHERE LOGIN LIKE :LOGIN ORDER BY `USUARIO_ID` DESC";
      $command = $con->prepare($sql);
      $login = "%" . $login . "%";
      $command->bindParam(":LOGIN", $login);
      $command->execute();
      $data = $command->fetchAll();
      arrayJSON($data);
   } else if ($type == "consultar_usuarios") {
      if (isset($idLogin)) {
         $sql = "SELECT * FROM usuarios WHERE USUARIO_ID = :USUARIO_ID";
         $command = $con->prepare($sql);
         $command->bindParam(":USUARIO_ID", $idLogin);
         $command->execute();
         $data = $command->fetch();
         if (!$data) {
            error("Erro ao consultar! Esse ID não existe!");
         } else {
            $data["status"] = 1;
            arrayJSON($data);
         }
      } else {
         error("Erro ao consultar! É necessário o ID do cliente.");
      }
   } else if ($type == "alterar_usuarios") {
      $sql = "UPDATE usuarios SET USUARIO_ID :USUARIO_ID, LOGIN =:LOGIN, SENHA =:SENHA, ATIVO=:ATIVO, NOME_COMPLETO=:NOME_COMPLETO WHERE USUARIO_ID=:USUARIO_ID";
      $command = $con->prepare($sql);
      $command->bindParam(":USUARIO_ID", $idLogin);
      $command->bindParam(":LOGIN", $login);
      $command->bindParam(":NOME_COMPLETO", $senha);
      $command->bindParam(":ATIVO", $ativo);
      $command->bindParam(":NOME_COMPLETO", $nome_completo);
      $command->bindParam(":USUARIO_ID", $idLogin);
      if ($command->execute()) {

         $permissoes = array();
         if( $_POST['opt_cadastrar_clientes'] == 'cadastrar_clientes') {
            array_push($permissoes, 'cadastrar_clientes');
         }
         if($_POST['opt_mais'] == 'mais') {
            array_push($permissoes, 'mais');
         }
         if($_POST['opt_excluir_clientes'] == 'excluir_clientes') {
            array_push($permissoes, 'excluir_clientes');
         }

         foreach ($permissoes as $value) {
            $sql = "UPDATE autorizacoes SET $permissoes WHERE USUARIO_ID=:USUARIO_ID";
            $command = $con->prepare($sql);
            $command->bindParam(":USUARIO_ID", $idLogin);
            if ($command->execute()) {
               $response["status"] = 1;
               arrayJSON($response);
            } else {
               error("Erro ao cadastrar o usuario, verifique as informações");
            }
         }
      }
   } else if ($type == "excluir_usuarios") {
      if (isset($idLogin)) {
         $sql = "DELETE FROM usuarios WHERE USUARIO_ID=:USUARIO_ID";
         $command = $con->prepare($sql);
         $command->bindParam(":USUARIO_ID", $idLogin);
         if ($command->execute()) {
            $response["status"] = 1;
            arrayJSON($response);
         } else {
            error("Erro ao excluir!");
         }
      } else {
         error("Erro ao excluir! É necessário o ID para exclusão.");
      }
   }
?>