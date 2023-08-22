<?php
	// Verifica se o método de envio do formulário foi do tipo POST
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Armazena as informações dos campos name, email e password nas variáveis
		$user_ip = $_SERVER["REMOTE_ADDR"]; // Pega o ip do usuário automaticamente
		$name = $_POST["name"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$anoatual = date("Y");
		$datahora = date("Y-m-d H:i:s");
		
		// Verifica se não tem nenhum código HTML malicioso
		$name = strip_tags($name);
		$email = strip_tags($email);
		$password = strip_tags($password);
		$password2 = strip_tags($password2);
		
		$name = htmlspecialchars($name);
		$email = htmlspecialchars($email);
		$password = htmlspecialchars($password);
		$password2 = htmlspecialchars($password2);
		
		// Verifica se tem espaços e tira eles da string
		$name = preg_replace("/\s+/", "", $name);
		$email = str_replace(" ", "", "$email");
		$password = str_replace(" ", "", $password);
		$password2 = str_replace(" ", "", $password2);
		
		// Verifica se as variáveis estão vazias
		if (empty($name)) {
			echo "<script>alert('Por favor, insira o login');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		if (empty($email)) {
			echo "<script>alert('Por favor, insira o e-mail');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		if (empty($password)) {
			echo "<script>alert('Por favor, insira o password');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		// Valida se o email está no formato certo
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "<script>alert('Erro, o formato de e-mail é inválido');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		// Verifica se os campos estão com o tamanho certo
		if (strlen($name) < 3) {
			echo "<script>alert('Por favor, coloque mais de 3 dígitos no campo username');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		if (strlen($name) > 16) {
			echo "<script>alert('Por favor, insira 16 ou menos, no campo username');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		if (strlen($password) < 4) {
			echo "<script>alert('Por favor, coloque mais de 3 dígitos no campo password');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		if (strlen($password) > 10) {
			echo "<script>alert('Por favor, insira 10 ou menos, no campo password');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
		// Verifica se as senhas são identicas 
		if ($password !== $password2) {
			echo "<script>alert('Erro! as senhas não são iguais');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}	




















// FAZER UMA CONEXÃO SEGURA


		
		// Armazena as informações do banco de dados nas variáveis para fazer a entrada
 		require_once('config.php');
		      //      $server_host	$database  $server_user    $server_password 
		
		// Conecta ao banco de dados SQL Server
		$connect = sqlsrv_connect($server_host, array("Database" => $database, "UID" => $server_user, "PWD" => $server_password, "TrustServerCertificate" => true));
					
		
		// Verifica os dados de entrada do banco de dados
		if (!$connect) {
			echo "Falha ao conectar com o banco de dados";
			print_r(sqlsrv_errors());
			exit();
		}


















		
		// Faz a prepação para a consulta
		$sql = "SELECT UserID FROM dbo.Users_Master WHERE UserID = ?";
		$params = array($name);
		$stmt = sqlsrv_prepare($connect, $sql, $params);
		
		// Faz a verificação dos dados
		if (!$stmt) {
			die("Falha na preparação da consulta: ". print_r(sqlsrv_errors(), true));
		}
		
		// Verifica a execução da consulta.
		if (!sqlsrv_execute($stmt)) {
			die ("Falha na execução da consulta: ". print_r(sqlsrv_errors(), true));
		}
		
		// Verifica o resultado da consulta no banco
		if (sqlsrv_has_rows($stmt)) {
			echo "<script>alert('O nome escolhido já está em uso. Por favor, escolha outro nome.');</script>";
			echo "<script>history.go(-1);</script>";
			exit();

		} else {
			// Pega o total de linhas da tabela e soma mais 1 para o novo valor
			$query = sqlsrv_query($connect, "SELECT COUNT(*) as total_rows FROM dbo.Users_Master");
			$result = sqlsrv_fetch_array($query);
			$tot = $result["total_rows"] + 1;
			
			// Faz a limpeza na variavel $stmt para poder usar denovo ela
			sqlsrv_free_stmt($stmt);
		




			// Adiciona os valores no banco de dados
			$sql = "INSERT INTO dbo.Users_Master (UserUID, UserID, Pw, JoinDate, Admin, AdminLevel, UseQueue, Status, Leave, UserType, UserIp, Point, Enpassword, Birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			$params = array($tot, $name, $password, $datahora, "False", "0", "False", "0", "0", "N", $user_ip, "0", $email, $anoatual);
			$stmt = sqlsrv_prepare($connect, $sql, $params);
		
			if(!$stmt) {
			die("Falha na preparação dos dados". print_r(sqlsrv_errors(), true));
			}
			
			// Verificação da adição dos dados
			if (!sqlsrv_execute($stmt)) {
				echo "Ocorreu um erro ao inserir os dados ". print_r(sqlsrv_errors(), true);
				exit();
			}
		}
		
		// Fecha o banco de dados
		sqlsrv_close($connect);
	}
?>


<html>
	<head>
		<meta charset="UTF-8"/>
	</head>
	<body>








// Retirar a chamanda "processamento.php"

		<form action="processamento.php" method="post" onsubmit="return validar()">

			<label for="name">Username</label><br/>
			<input type="text" name="name" id="name" class="input" maxlength="16" onkeypress="verificar()" />
			<br/>
			<label for="email">Email</label><br/>
			<input type="email" name="email" id="email" class="input" maxlength="32" onkeypress="verificar()" />
			<br/>
			<label for="password">Password</label><br/>
			<input type="password" name="password" id="password" class="input" maxlength="10" onkeypress="verificar()" />
			<br/>
			<label for="password2">Confirm Password</label><br/>
			<input type="password" name="password2" id="password2" class="input" maxlength="10" onkeypress="verificar()" />
			<br/>
			<input type="submit" id="btn-submit" value="Create account"/><p>Copyright 2023 Shaiya. by Yugo</p>
		</form>
		
		<script>
			var login = document.getElementById("name");
			var email = document.getElementById("email");
			var password = document.getElementById("password");
			var password2 = document.getElementById("password2");

			function verificar() {
				login.value = login.value.replace(/\s/g, "");
				email.value = email.value.replace(/\s/g, "");
				password.value = password.value.replace(/\s/g, "");
				password2.value = password2.value.replace(/\s/g, "");
			}

			function validar() {
				var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Validação completa
				// var emailPattern = /^[^\s@]+@[^\s@].[^\s@]+$/; validação simples
				
				// Verifica se o login está configurado corretamente
				if (login.value === "") {
					alert("The username field is required.");
					login.focus();
					return false;	
				}
				
				if (login.value.length < 3) {
					alert("Please enter a username with at least 3 characters.");
					login.focus();
					return false;
				}
				
				if (login.value.length > 16) {
					alert("Username is too long. Please enter a username with less than 16 characters.");
					login.value = "";
					login.focus();
					return false;
				}
				
				// Faz a verificação do email para ver se está configurado corretamente
				if (email.value === "") {
					alert("Please enter a valid email address.");
					email.focus();
					return false;
				}
				
				if (email.value.length > 50) {
					alert("Error: Email is too long. Please use an email address with less than 50 characters.");
					email.value = "";
					email.focus();
					return false;
				} 
						
				if (!emailPattern.test(email.value)) {
					alert("Please enter a valid email address.");
					email.focus();
					return false;
				}
				
				// Verifica se a senha está configurada corretamente
				if (password.value === "") {
					alert("The password field is required.");
					password.focus();
					return false;
				}
					
				if (password.value.length < 4) {
					alert("The password field requires a minimum of 4 characters.");
					password.focus();
					return false;
				} 
						
				if (password.value.length > 10) {
					alert("Password is too long. Please use a password with less than 10 characters.");
					password.value = "";
					password.focus();
					return false;
				}
				
				// Verifica se confirmação de senha está configurada corretamente
				if (password2.value === "") {
					alert("The password confirmation field is required.");
					password2.focus();
					return false;
				} 
				
				if (password2.value.length < 4) {
					alert("The password confirmation field requires a minimum of 4 characters.");
					password2.focus()
					return false;
				}
				
				if (password2.value.length > 10) {
					alert("The password confirmation field has a limit of 10 characters.");
					password2.value = "";
					password2.focus();
					return false;
				}
				
				// Faz Verificação se as duas senhas são iguais
				if (password.value !== password2.value) {
					alert("The passwords do not match. Please re-enter.");
					password.value = "";
					password2.value = "";
					password.focus();
					return false;
				}
				
				// Envia uma mensagem de sucesso e manda o formulário para o arquivo PHP
				alert("Validation Successful.");
				return true;
			}
		</script>
	</body>
</html>

