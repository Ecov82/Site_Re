<?php
session_start(); // Inicia a sessão para verificar login (caso necessário)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário e sanitizar entradas
    $nome_cliente = htmlspecialchars($_POST['nome_cliente']);
    $telefone_cliente = htmlspecialchars($_POST['telefone_cliente']);
    $email_cliente = filter_var($_POST['email_cliente'], FILTER_SANITIZE_EMAIL);
    $procedimento = htmlspecialchars($_POST['procedimento']);
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];

    // Validar a data para garantir que não seja menor que a data atual
    $data_atual = date('Y-m-d');
    if ($data_agendamento < $data_atual) {
        $mensagem = "A data do agendamento não pode ser menor que hoje.";
    } else {
        // Conectar ao banco de dados e verificar se o cliente já foi atendido (já está registrado)
        $conn = new mysqli('localhost', 'root', '', 'banco');  // Certifique-se que 'banco' é o nome correto

        // Verificar se a conexão foi bem-sucedida
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        // Consultar o banco de dados para verificar se o cliente já foi atendido
        $query = "SELECT * FROM agendamentos_re WHERE email_cliente = ?";  // Alterado para agendamentos_re
        $stmt = $conn->prepare($query);

        // Verificar se a preparação da consulta foi bem-sucedida
        if ($stmt === false) {
            die('Erro ao preparar a consulta: ' . $conn->error);
        }

        $stmt->bind_param("s", $email_cliente); // Evita SQL Injection
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar se encontrou registros para o cliente
        if ($result->num_rows > 0) {
            // Cliente já foi atendido, exibe mensagem de boas-vindas
            $mensagem = "Que bom que você voltou, $nome_cliente! Seu agendamento foi confirmado.";
        } else {
            // Cliente nunca foi atendido, mensagem de boas-vindas para um novo cliente
            $mensagem = "Bem-vinda, $nome_cliente! Seu agendamento foi confirmado e, em breve, você receberá uma confirmação no WhatsApp.";
        }

        // Inserir o agendamento no banco de dados (caso necessário)
        $query_insert = "INSERT INTO agendamentos_re (nome_cliente, telefone_cliente, email_cliente, procedimento, data_agendamento, hora_agendamento) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);

        // Verificar se a preparação da consulta foi bem-sucedida
        if ($stmt_insert === false) {
            die('Erro ao preparar a consulta de inserção: ' . $conn->error);
        }

        $stmt_insert->bind_param("ssssss", $nome_cliente, $telefone_cliente, $email_cliente, $procedimento, $data_agendamento, $hora_agendamento);

        if ($stmt_insert->execute()) {
            // Agendamento inserido com sucesso
            $mensagem = "Seu agendamento foi confirmado com sucesso!";
            // Limpar os dados do formulário
            $nome_cliente = '';
            $telefone_cliente = '';
            $email_cliente = '';
            $procedimento = '';
            $data_agendamento = '';
            $hora_agendamento = '';
            // Exibir mensagem de sucesso e limpar o formulário no frontend
            echo '<script>limparFormulario();</script>';
        } else {
            $mensagem = "Ocorreu um erro ao agendar. Tente novamente mais tarde.";
        }

        // Fechar a conexão
        $conn->close();
    }

    // Exibir mensagem
    echo $mensagem;
}
?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento - Lashes Cílios da Rê</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Função para aplicar a máscara de telefone
        function mascaraTelefone(input) {
            let valor = input.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2"); // Adiciona o parêntese
            valor = valor.replace(/(\d)(\d{4})$/, "$1-$2"); // Adiciona o traço
            input.value = valor; // Atualiza o valor do campo
        }

        // Função para validar a data de agendamento
        function validarData() {
            const dataAgendamento = document.getElementById('data_agendamento').value;
            const dataAtual = new Date().toISOString().split('T')[0]; // Pega a data atual no formato YYYY-MM-DD
            
            if (dataAgendamento < dataAtual) {
                document.getElementById('erroData').innerText = "A data do agendamento não pode ser menor que hoje!";
                document.getElementById('erroData').style.color = 'red';
                document.getElementById('btnAgendar').disabled = true; // Desabilita o botão de agendar
            } else {
                document.getElementById('erroData').innerText = ""; // Limpa a mensagem de erro
                document.getElementById('btnAgendar').disabled = false; // Habilita o botão de agendar
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Lashes Cílios da Rê 💖</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="agendamento">
        <div class="container">
            <h2>Agendar Atendimento</h2>
            <form action="form_agendamento.php" method="POST">
                <label for="nome_cliente">Nome:</label>
                <input type="text" id="nome_cliente" name="nome_cliente" required>

                <label for="telefone_cliente">Telefone:</label>
                <input type="text" id="telefone_cliente" name="telefone_cliente" required oninput="mascaraTelefone(this)" maxlength="15">

                <label for="email_cliente">E-mail:</label>
                <input type="email" id="email_cliente" name="email_cliente">

                <label for="procedimento">Procedimento:</label>
                <select id="procedimento" name="procedimento" required>
                    <option value="alongamento">Alongamento de Cílios</option>
                    <option value="design">Design de Sobrancelhas</option>
                    <option value="limpeza">Limpeza de Pele</option>
                </select>

                <label for="data_agendamento">Data:</label>
                <input type="date" id="data_agendamento" name="data_agendamento" required onchange="validarData()">
                <p id="erroData"></p>

                <label for="hora_agendamento">Hora:</label>
                <select id="hora_agendamento" name="hora_agendamento" required>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="13:00">13:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                </select>

                <button type="submit" id="btnAgendar">Agendar</button>
            </form>

            <?php if (isset($mensagem)) { echo "<p class='mensagem'>$mensagem</p>"; } ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Lashes Cílios da Rê - Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
