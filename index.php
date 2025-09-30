<?php
// Armazenar as mensagens no sistema
$ok = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $local = trim($_POST['local'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // Validação se algum campo obrigatório estiver vázio Exiba mensagem

    if (!$titulo || !$data || !$hora || !$local || !$descricao) {
        $erro = "Preencha todos os campos !";
    }

    // Upload Imagem
    $caminhoImagem = '';
    if (empty($erro) && !empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

        // Tamanho maximo de uma imagem 2mb
        $tamMax = 2 * 1024 * 1024;
        // Se o arquivo for > Que o limite
        if ($_FILES['imagem']['size'] > $tamMax) {
            $erro = "Imagem ta grande em !! (Máx. 2mb)";
        } else {
            // Criamos uma pasta para armazenar as imagens caso não exista
            // Migramos para esta pasta

            $nomeOriginal = basename((string)$_FILES['imagem']['name']);
            $nomeOriginal = preg_replace('/[^A-Za-z0-9_.-]/', '_', $nomeOriginal);
            $nomeFinal = time() . '_' . $nomeOriginal;
            $destino = __DIR__ . '/uploads/' . $nomeFinal;

            // Mover arquivo para a pasta
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $caminhoImagem = 'uploads/' . $nomeFinal;
            } else {
                $erro = "Falha ao salvar a imagem";
            }
        }
        if (empty($erro)) {
            echo "DEU TUDO CERTOOOO";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sistema</title>
</head>

<body>
    <div class="bg">
    </div>
    <div class="container">
        <header class="topo">
            <h1 class="logo">Eventos</h1>
            <p class="sub">Todos os Eventos em São José do Rio Preto</p>
        </header>
        <section class="grid">
            <div class="card">
                <h2 class="card_titulo">Cadastrar Atividade</h2>
                <form class="form" method="post" enctype="multipart/form-data">
                    <label for="campo" class="campo">
                        <span>Título</span>
                        <input type="text" name="titulo" id="titulo" required>
                    </label>
                    <div class="dupla">
                        <label for="campo" class="campo">
                            <span>Data</span>
                            <input type="date" name="data" id="data" required>
                        </label>
                        <label for="campo" class="campo">
                            <span>Hora</span>
                            <input type="time" name="hora" id="hora" required>
                        </label>
                    </div>
                    <label for="campo" class="campo">
                        <span>Local</span>
                        <input type="text" name="local" id="local" required>
                    </label>
                    <label class="campo">
                        <span>Descrição</span>
                        <textarea name="descricao" rows="5" required></textarea>
                    </label>
                    <label class="campo">
                        <span>Imagem do Evento (Opcional)</span>
                        <input type="file" name="imagem" accept=".jpg,.jpeg,.png,.webp">
                    </label>
                    <button type="submit" class="botao_primario">Salvar</button>
                </form>
            </div>
        </section>
    </div>
    <footer class="rodape">
        <div class="container">
            <p>Feito em PHP pelo <a href="https://github.com/wiredvus">Vus</a> 🩷</p>
        </div>
    </footer>
</body>

</html>
