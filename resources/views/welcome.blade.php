<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tizimga kirish</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <style>
        body.login-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
        }

        .login-box {
            width: 400px;
        }

        @media (max-width: 576px) {
            .login-box {
                width: 90%;
            }
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn-portal {
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 10px;
            text-decoration: none !important;
        }

        .btn-teacher {
            background-color: #4e73df;
            color: white !important;
        }

        .btn-teacher:hover {
            background-color: #2e59d9;
            transform: translateY(-2px);
        }

        .btn-student {
            background-color: #1cc88a;
            color: white !important;
        }

        .btn-student:hover {
            background-color: #17a673;
            transform: translateY(-2px);
        }

        .portal-icon {
            margin-right: 12px;
            font-size: 1.4rem;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>TEST.regofis.UZ</b></a>
    </div>
    <div class="card shadow-lg">
        <div class="card-body ">
            <div class="mt-4">
                <a href="{{ route('login.user') }}" class="btn-portal btn-teacher shadow-sm">
                    <i class="fas fa-chalkboard-teacher portal-icon"></i>
                    O‘qituvchi sahifasi
                </a>

                <a href="{{ route('login.student') }}" class="btn-portal btn-student shadow-sm">
                    <i class="fas fa-user-graduate portal-icon"></i>
                    Talaba sahifasi
                </a>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>
</html>
