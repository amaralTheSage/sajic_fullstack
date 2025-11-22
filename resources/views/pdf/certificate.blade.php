<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Certificado de conclusão</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .gradient-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .icons-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.25;
            pointer-events: none;
            overflow: hidden;
        }

        .icons-bg img {
            position: absolute;
            opacity: 0.25;
            width: 300px;
            height: 300px;
        }

        .gradient-bg img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            display: block;
            margin: 0;
            padding: 0;
        }

        .content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100vh;
            display: table;
            padding: 60px 80px;
        }

        .content-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .logo {
            width: 280px;
            margin: 0 auto 50px;
            display: block;
        }

        .title {
            font-size: 42px;
            font-weight: bold;
            text-align: center;
            color: #ffffff;
            margin-bottom: 40px;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
        }

        .description {
            font-size: 20px;
            text-align: center;
            color: #ffffff;
            line-height: 2;
            margin: 0 auto;
            max-width: 900px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .description strong {
            font-weight: bold;
            color: #ffffff;
        }

        .link {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #ffffff;
            font-size: 12px;
            text-decoration: none;
            z-index: 2;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body>
    <!-- Gradient Background Image -->
    <div class="gradient-bg">
        <img src="{{ public_path('assets/certificate-gradient.png') }}" alt="" />
    </div>

    <!-- Icons Background Pattern - Repeated -->
    <div class="icons-bg">
        @for($i = 0; $i
        < 20; $i++)
            @for($j=0; $j < 15; $j++)
            <img src="{{ public_path('assets/icons-background.png') }}" alt="" style="top: {{ $i * 300 }}px; left: {{ $j * 300 }}px;" />
        @endfor
        @endfor
    </div>

    <div class="content">
        <div class="content-inner">
            <img class="logo" src="{{ public_path('assets/logo_nova-branca.png') }}" alt="Logo Sathon" />
            <h1 class="title">Certificado de participação</h1>
            <p class="description">
                Declaramos, para os devidos fins, que o(a) aluno(a) <strong>{{ $name }}</strong>
                participou da <strong>Semana Acadêmica e Jornada de Iniciação Científica - SAJIC</strong>,
                totalizando <strong>10 horas</strong> horas de participação.
            </p>
        </div>
    </div>

    <a class="link" href="https://sathon.com.br/storage/certificates/{{ $file_name }}">
        https://sathon.com.br/storage/certificates/{{ $file_name }}
    </a>

</body>

</html>
