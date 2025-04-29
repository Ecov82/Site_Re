<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cliente = htmlspecialchars($_POST['nome_cliente']);
    $telefone_cliente = htmlspecialchars($_POST['telefone_cliente']);
    $email_cliente = filter_var($_POST['email_cliente'], FILTER_SANITIZE_EMAIL);
    $procedimento = htmlspecialchars($_POST['procedimento']);
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];

    $data_atual = date('Y-m-d');
    if ($data_agendamento < $data_atual) {
        $mensagem = "A data do agendamento não pode ser menor que hoje.";
    } else {
        $conn = new mysqli('localhost', 'root', '', 'banco');
        
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        $query = "SELECT * FROM agendamentos_re WHERE email_cliente = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('Erro ao preparar a consulta: ' . $conn->error);
        }

        $stmt->bind_param("s", $email_cliente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mensagem = "Que bom que você voltou, $nome_cliente! Seu agendamento foi confirmado.";
        } else {
            $mensagem = "Bem-vinda, $nome_cliente! Seu agendamento foi confirmado e, em breve, você receberá uma confirmação no WhatsApp.";
        }

        $query_insert = "INSERT INTO agendamentos_re (nome_cliente, telefone_cliente, email_cliente, procedimento, data_agendamento, hora_agendamento) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);

        if ($stmt_insert === false) {
            die('Erro ao preparar a consulta de inserção: ' . $conn->error);
        }

        $stmt_insert->bind_param("ssssss", $nome_cliente, $telefone_cliente, $email_cliente, $procedimento, $data_agendamento, $hora_agendamento);

        if ($stmt_insert->execute()) {
            $mensagem = "Seu agendamento foi confirmado com sucesso!";
            $nome_cliente = '';
            $telefone_cliente = '';
            $email_cliente = '';
            $procedimento = '';
            $data_agendamento = '';
            $hora_agendamento = '';
            echo '<script>limparFormulario();</script>';
        } else {
            $mensagem = "Ocorreu um erro ao agendar. Tente novamente mais tarde.";
        }

        $conn->close();
    }

    echo $mensagem;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lashes Cílios da Rê - Extensão de Cílios Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6b98;
            --secondary-color: #ffb6c1;
            --dark-color: #3a3a3a;
            --light-color: #fff9fa;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark-color);
            background-color: var(--light-color);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1595478542166-6d438d1069e1');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #e55a7c;
            border-color: #e55a7c;
        }
        
        .service-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
        }
        
        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        #agendamento {
            background-color: white;
            padding: 60px 0;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 30px 0;
            margin-top: 60px;
        }
        
        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
        }
        
        .before-after {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }
        
        .before-after img {
            transition: transform 0.5s;
        }
        
        .before-after:hover img {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-eye"></i> Lashes Cílios da Rê
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicos">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio">Portfólio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#agendamento">Agendamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Descubra o poder de um olhar marcante</h1>
            <p class="lead mb-5">Extensão de cílios premium para realçar sua beleza natural</p>
            <a href="#agendamento" class="btn btn-primary btn-lg px-4">Agende Seu Horário</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5" id="servicos">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nossos Serviços</h2>
                <p class="lead text-muted">Técnicas exclusivas para cada tipo de olhar</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Volume Brasileiro</h3>
                        <p>Cílios perfeitos com efeito dramático e cheio de volume, ideal para quem deseja um olhar marcante.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h3>Híbrido</h3>
                        <p>Combinação perfeita entre volume e naturalidade, para quem quer um efeito sofisticado.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card service-card text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Fio a Fio</h3>
                        <p>Resultado super natural, perfeito para quem busca um efeito discreto e elegante.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="py-5 bg-light" id="portfolio">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nossos Trabalhos</h2>
                <p class="lead text-muted">Veja a transformação que nossos cílios podem fazer</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="before-after">
                        <img src="https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1" class="img-fluid rounded" alt="Antes e Depois 1">
                        <div class="overlay">
                            <h4>Volume Brasileiro</h4>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="before-after">
                        <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9" class="img-fluid rounded" alt="Antes e Depois 2">
                        <div class="overlay">
                            <h4>Cílios Híbridos</h4>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="before-after">
                        <img src="https://images.unsplash.com/photo-1596464716127-f2a82984de30" class="img-fluid rounded" alt="Antes e Depois 3">
                        <div class="overlay">
                            <h4>Fio a Fio</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="agendamento">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Agende Seu Horário</h2>
                    <p class="lead mb-4">Preencha o formulário e reserve seu horário para um olhar deslumbrante</p>
                    
                    <form action="index.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nome_cliente" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefone_cliente" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone_cliente" name="telefone_cliente" required oninput="mascaraTelefone(this)" maxlength="15">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_cliente" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email_cliente" name="email_cliente">
                        </div>
                        
                        <div class="mb-3">
                            <label for="procedimento" class="form-label">Procedimento</label>
                            <select class="form-select" id="procedimento" name="procedimento" required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="volume_brasileiro">Volume Brasileiro</option>
                                <option value="hibrido">Cílios Híbridos</option>
                                <option value="fio_a_fio">Fio a Fio</option>
                                <option value="manutencao">Manutenção</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_agendamento" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data_agendamento" name="data_agendamento" required onchange="validarData()">
                                <div id="erroData" class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="hora_agendamento" class="form-label">Horário</label>
                                <select class="form-select" id="hora_agendamento" name="hora_agendamento" required>
                                    <option value="" selected disabled>Selecione...</option>
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
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2" id="btnAgendar">Confirmar Agendamento</button>
                    </form>
                    
                    <?php if (isset($mensagem)): ?>
                        <div class="alert alert-success mt-3" role="alert">
                            <?php echo $mensagem; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1595478542166-6d438d1069e1" class="img-fluid rounded" alt="Cílios perfeitos">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contato">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Entre em Contato</h2>
                <p class="lead text-muted">Tire suas dúvidas ou agende pelo WhatsApp</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div class="service-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Endereço</h4>
                    <p>Avenida Rosalvo Marques Bonfim <br>Bloco 09 Apt 103 - Residencial Trancoso</p>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="service-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4>Telefone</h4>
                    <p>(43) 98459-7334</p>
                    <a href="https://wa.me/5543984597334" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="service-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4>Horário</h4>
                    <p>Segunda a Sexta: 9h às 18h<br>Sábado: 9h às 14h</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4><i class="fas fa-eye"></i> Lashes Cílios da Rê</h4>
                    <p>Especialista em extensão de cílios e beleza ocular</p>
                </div>
                
                <div class="col-md-6 text-end">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            
            <hr class="mt-4 mb-4" style="border-color: rgba(255,255,255,0.1);">
            
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Lashes Cílios da Rê. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para aplicar a máscara de telefone
        function mascaraTelefone(input) {
            let valor = input.value.replace(/\D/g, '');
            valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
            valor = valor.replace(/(\d)(\d{4})$/, "$1-$2");
            input.value = valor;
        }

        // Função para validar a data de agendamento
        function validarData() {
            const dataAgendamento = document.getElementById('data_agendamento').value;
            const dataAtual = new Date().toISOString().split('T')[0];
            const erroData = document.getElementById('erroData');
            
            if (dataAgendamento < dataAtual) {
                erroData.innerText = "A data do agendamento não pode ser menor que hoje!";
                document.getElementById('btnAgendar').disabled = true;
            } else {
                erroData.innerText = "";
                document.getElementById('btnAgendar').disabled = false;
            }
        }

        // Validação do formulário
        (function() {
            'use strict';
            
            var forms = document.querySelectorAll('.needs-validation');
            
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>
</html>