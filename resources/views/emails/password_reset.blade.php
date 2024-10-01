<!-- resources/views/emails/password_reset.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinição de Senha</title>
</head>
<body>
    <h1>Redefinição de Senha</h1>
    <p>Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.</p>
    <p>Clique no link abaixo para redefinir sua senha:</p>
    <a href="{{ $url }}">{{ $url }}</a>
    <p>Se você não solicitou a redefinição de senha, não é necessário realizar nenhuma ação.</p>
</body>
</html>
