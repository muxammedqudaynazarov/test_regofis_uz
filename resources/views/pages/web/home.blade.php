@extends('layouts.web')

@section('style')
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f6f9;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(118, 75, 162, 0.3);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Schedule Timeline */
        .schedule-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .lesson-item {
            border-left: 4px solid transparent;
            transition: all 0.2s;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        .lesson-item:hover {
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: scale(1.01);
        }

        .lesson-item.active {
            border-left-color: #ffc107;
            background: #fffdf0;
        }

        .lesson-item.done {
            border-left-color: #28a745;
            opacity: 0.8;
        }

        .lesson-item.upcoming {
            border-left-color: #17a2b8;
        }

        /* Action Buttons */
        .action-btn {
            border-radius: 12px;
            transition: all 0.3s;
            font-weight: 600;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .quote-card {
            background: #fff;
            border-radius: 15px;
            border-left: 5px solid #667eea;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper" style="background-color: #f4f6f9;">
        <section class="content pt-4">
            <div class="container-fluid">

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card welcome-card p-4 border-0">
                            <div class="d-flex justify-content-between align-items-center position-relative"
                                 style="z-index: 1;">
                                <div>
                                    <h5 class="mb-1" style="opacity: 0.9;">
                                        <i class="far fa-calendar-alt mr-2"></i> {{ date('d.m.Y') }} | <span
                                            id="clock">{{ date('H:i') }}</span>
                                    </h5>
                                    <h1 class="font-weight-bold display-5 mb-1">
                                        Assalomu alaykum, {{ json_decode(auth('web')->user()->name)->first_name }}! 👋
                                    </h1>
                                    {{--<p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                                        Bugungi mashg'ulotlaringizda omad tilaymiz. Sizda bugun <b>3 ta dars</b> bor.
                                    </p>--}}
                                </div>
                                <div class="d-none d-md-block text-right">
                                    <img src="{{ auth('web')->user()->picture }}"
                                         class="img-circle border border-white shadow"
                                         style="width: 80px; height: 80px; border-width: 3px !important;"
                                         alt="User Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--<div class="row mb-4">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="card stat-card p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-light text-primary mr-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">120</h5>
                                    <small class="text-muted">Jami talabalar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="card stat-card p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-light text-success mr-3">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">95%</h5>
                                    <small class="text-muted">O'rtacha davomat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="card stat-card p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-light text-warning mr-3">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">4.9</h5>
                                    <small class="text-muted">O'qituvchi reytingi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="card stat-card p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-light text-danger mr-3">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0">2 ta</h5>
                                    <small class="text-muted">Tekshirish kutyapti</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
                {{--<div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="card schedule-card mb-4">
                            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                                <h4 class="card-title font-weight-bold text-dark">
                                    <i class="fas fa-chalkboard mr-2 text-primary"></i>
                                    Mening fanlarim
                                </h4>
                            </div>
                            <div class="card-body px-4 pb-4">

                                <div class="lesson-item done d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 text-center" style="min-width: 80px;">
                                            <h6 class="font-weight-bold mb-0">08:30</h6>
                                            <small class="text-muted">09:50</small>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-0">PHP Laravel asoslari</h6>
                                            <small class="text-muted"><i class="fas fa-users mr-1"></i> IF-201
                                                guruhi</small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-light text-muted px-3 py-2">Xona: A-204</span>
                                        <div class="text-success small mt-1"><i class="fas fa-check-circle"></i> O'tildi
                                        </div>
                                    </div>
                                </div>

                                <div class="lesson-item active d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 text-center" style="min-width: 80px;">
                                            <h6 class="font-weight-bold text-primary mb-0">10:00</h6>
                                            <small class="text-primary font-weight-bold">11:20</small>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-0 text-dark">MySQL ma'lumotlar bazasi</h6>
                                            <small class="text-primary"><i class="fas fa-users mr-1"></i> IF-202 guruhi</small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="badge badge-warning text-white px-3 py-2 shadow-sm">Xona: B-105</span>
                                        <div class="text-warning font-weight-bold small mt-1">
                                            <span class="spinner-grow spinner-grow-sm" role="status"
                                                  aria-hidden="true"></span>
                                            Dars jarayoni
                                        </div>
                                    </div>
                                </div>

                                <div class="lesson-item upcoming d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 text-center" style="min-width: 80px;">
                                            <h6 class="font-weight-bold mb-0">11:30</h6>
                                            <small class="text-muted">12:50</small>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-0">HTML/CSS UI dizayn</h6>
                                            <small class="text-muted"><i class="fas fa-users mr-1"></i> IF-105
                                                guruhi</small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-light text-muted px-3 py-2">Xona: C-301</span>
                                        <div class="text-info small mt-1"><i class="far fa-clock"></i> Kutilmoqda</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">

                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h5 class="font-weight-bold mb-3">Tezkor amallar</h5>
                                <div class="row">
                                    <div class="col-6 mb-2 pr-1">
                                        <a href="#"
                                           class="btn btn-light btn-block action-btn py-3 text-primary shadow-sm h-100">
                                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                            Material
                                        </a>
                                    </div>
                                    <div class="col-6 mb-2 pl-1">
                                        <a href="#"
                                           class="btn btn-light btn-block action-btn py-3 text-success shadow-sm h-100">
                                            <i class="fas fa-user-check fa-2x mb-2"></i><br>
                                            Yo'qlama
                                        </a>
                                    </div>
                                    <div class="col-6 pr-1">
                                        <a href="#"
                                           class="btn btn-light btn-block action-btn py-3 text-warning shadow-sm h-100">
                                            <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                                            Baholash
                                        </a>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <a href="#"
                                           class="btn btn-light btn-block action-btn py-3 text-danger shadow-sm h-100">
                                            <i class="fas fa-clipboard-list fa-2x mb-2"></i><br>
                                            Jurnal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card quote-card shadow-sm p-4">
                            <h6 class="text-uppercase text-muted font-weight-bold"
                                style="font-size: 0.75rem; letter-spacing: 1px;">Kun hikmati</h6>
                            <blockquote class="blockquote mb-0 mt-2">
                                <p class="mb-2 font-italic" style="font-size: 1rem;">"Talaba bu to'ldirilishi kerak
                                    bo'lgan idish emas, balki yoqilishi kerak bo'lgan mash'aladir."</p>
                                <footer class="blockquote-footer mt-1">Plutarx</footer>
                            </blockquote>
                        </div>

                    </div>
                </div>--}}

            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        // Oddiy soat skripti
        setInterval(function () {
            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').textContent = hours + ':' + minutes;
        }, 1000);
    </script>
@endsection
