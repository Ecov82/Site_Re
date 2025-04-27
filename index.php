<?php
// Inclui as regras de atendimento
include 'regras_atendimento.php';  

// Exibe as regras de atendimento
echo "<h2>Regras de Atendimento:</h2><ul>";
foreach ($regras_atendimento as $regra) {
    echo "<li>" . $regra . "</li>";
}
echo "</ul>";
?>
<!-- root	127.0.0.1
root	::1
pma	localhost
root	localhost -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lashes Cílios da Rê</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Lashes Cílios da Rê 💖</h1>
            <nav>
                <ul>
                    <li><a href="#agendamento">Agende seu Horário</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="agendamento">
        <div class="container">
            <h2>Agende seu horário!</h2>
            <p>Para agendar um horário, preencha o formulário abaixo e agende seu atendimento de forma rápida e fácil! 💅</p>
            <a href="form_agendamento.php" class="button">Agendar Agora</a>
        </div>
    </section>

    <section id="contato">
        <div class="container">
            <h2>Entre em contato</h2>
            <p>Quer falar com a Rê diretamente? Mande um WhatsApp agora mesmo! 📱</p>
            <a href="https://wa.me/SEUNUMERO" target="_blank" class="button">Clique aqui para WhatsApp</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Lashes Cílios da Rê - Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
