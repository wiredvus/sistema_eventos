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
            $linha = $titulo . "|" . $data . "|" . $hora . "|" . $local . "|" . $descricao . "|" . $caminhoImagem . PHP_EOL;

            // Salvar os valores no bloco de notas
            file_put_contents(__DIR__ . "/eventos.txt", $linha, FILE_APPEND | LOCK_EX);

            echo "<script>alert('Registrado com sucesso')</script>";
        }
    }
}

// Carregar Lista

$eventos = [];
$arquivo = __DIR__ . "/eventos.txt";
// Verifica se o arquivo  existe

if (is_file($arquivo)) {
    // Ler arquivo por linha
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Faz o reverso puff... para mostrar os primeiros registros mais recentes
    $linhas = array_reverse($linhas);

    // Percorre cada Linha e quebramos por | em diferentes elementos

    foreach ($linhas as $linha) {
        // Explode na |
        $p = explode('|', $linha);
        // Mostra os pedaços de forma organizada
        $eventos[] = [
            'titulo' => $p[0],
            'data' => $p[1],
            'hora' => $p[2],
            'local' => $p[3],
            'descricao' => $p[4],
            'imagem' => $p[5]
        ];
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
                <?php if (!empty($erro)) : ?>
                    <div class="aviso erro"><?= $erro ?> </div>
                <?php endif; ?>
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
            <div class="card">
                <h2 class="card_titulo">Atividades Cadastradas</h2>
                <?php if (empty($eventos)) : ?>
                    <p class="muted"> Sem Registro ! , Quer ver o que ?</p>
                <?php else: ?>
                    <div class="lista_eventos">
                        <?php foreach ($eventos as $e):
                            $titulo = $e['titulo'];
                            $data = $e['data'];
                            $hora = $e['hora'];
                            $local = $e['local'];
                            $descricao = nl2br($e['descricao']);
                            $imagem = $e['imagem'];
                        ?>
                            <article class="evento">
                                <img class=" thumb" src="<?= $imagem ?>"
                                    <h3 class=" ev_titulo"><?= $titulo ?> </h3>
                                <p class="ev_meta">
                                    Data : <?= $data ?> |
                                    Hora : <?= $hora ?> |
                                    Local : <?= $local ?>
                                </p>
                                <p><?= $descricao ?></p>

                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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