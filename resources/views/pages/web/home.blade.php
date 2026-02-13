@extends('layouts.app')
@section('style')
    <style>
        .small-box {
            transition: transform .3s ease, box-shadow .3s ease;
            cursor: pointer;
        }

        .small-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .card {
            transition: all 0.3s ease;
        }

        .table td {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem;
                text-align: center;
            }

            .breadcrumb {
                display: none;
            }
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper" style="background-color: #f4f6f9; padding-bottom: 20px;">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-dark" style="font-family: 'Source Sans Pro', sans-serif;">
                            <i class="fas fa-chalkboard-teacher mr-2 text-primary"></i> O‘qituvchi Kabineti
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-white p-2 shadow-sm" style="border-radius: 8px;">
                            <li class="breadcrumb-item"><a href="#">Bosh sahifa</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box shadow-sm border-0"
                             style="border-radius: 15px; background: linear-gradient(45deg, #4e73df, #224abe); color: white;">
                            <div class="inner p-4">
                                <h3>150</h3>
                                <p>Jami talabalar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-graduate" style="opacity: 0.3;"></i>
                            </div>
                            <a href="#" class="small-box-footer"
                               style="background: rgba(0,0,0,0.1); border-radius: 0 0 15px 15px;">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box shadow-sm border-0"
                             style="border-radius: 15px; background: linear-gradient(45deg, #1cc88a, #13855c); color: white;">
                            <div class="inner p-4">
                                <h3>12</h3>
                                <p>Faol guruhlar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-layer-group" style="opacity: 0.3;"></i>
                            </div>
                            <a href="#" class="small-box-footer"
                               style="background: rgba(0,0,0,0.1); border-radius: 0 0 15px 15px;">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box shadow-sm border-0"
                             style="border-radius: 15px; background: linear-gradient(45deg, #f6c23e, #dda20a); color: white;">
                            <div class="inner p-4">
                                <h3>28</h3>
                                <p>Topshiriqlar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-tasks" style="opacity: 0.3;"></i>
                            </div>
                            <a href="#" class="small-box-footer"
                               style="background: rgba(0,0,0,0.1); border-radius: 0 0 15px 15px;">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box shadow-sm border-0"
                             style="border-radius: 15px; background: linear-gradient(45deg, #e74a3b, #be2617); color: white;">
                            <div class="inner p-4">
                                <h3>5</h3>
                                <p>Yangi xabarlar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-bell" style="opacity: 0.3;"></i>
                            </div>
                            <a href="#" class="small-box-footer"
                               style="background: rgba(0,0,0,0.1); border-radius: 0 0 15px 15px;">Batafsil <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">

                    <div class="col-lg-8 col-md-12">
                        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                            <div class="card-header border-0 bg-white py-3">
                                <h3 class="card-title font-weight-bold text-primary">
                                    <i class="far fa-calendar-check mr-2"></i> Bugungi darslar jadvali
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Vaqt</th>
                                            <th class="border-0">Guruh</th>
                                            <th class="border-0">Mavzu</th>
                                            <th class="border-0 text-center">Xona</th>
                                            <th class="border-0 text-center">Holat</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>08:30 - 09:50</td>
                                            <td><span class="badge badge-info shadow-sm">IF-201</span></td>
                                            <td>PHP Laravel asoslari</td>
                                            <td class="text-center text-muted">A-204</td>
                                            <td class="text-center text-success"><i class="fas fa-check-circle"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>10:00 - 11:20</td>
                                            <td><span class="badge badge-info shadow-sm">IF-202</span></td>
                                            <td>MySQL ma'lumotlar bazasi</td>
                                            <td class="text-center text-muted">B-105</td>
                                            <td class="text-center text-warning font-weight-bold">Davom etmoqda</td>
                                        </tr>
                                        <tr>
                                            <td>11:30 - 12:50</td>
                                            <td><span class="badge badge-info shadow-sm">IF-105</span></td>
                                            <td>HTML/CSS UI dizayn</td>
                                            <td class="text-center text-muted">C-301</td>
                                            <td class="text-center text-secondary">Kutilmoqda</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <div class="card-body">
                                <h5 class="font-weight-bold mb-4">Tezkor amallar</h5>
                                <button class="btn btn-primary btn-block mb-3 py-2 shadow-sm"
                                        style="border-radius: 10px;">
                                    <i class="fas fa-plus mr-2"></i> Yangi dars materialini yuklash
                                </button>
                                <button class="btn btn-outline-success btn-block mb-2 py-2"
                                        style="border-radius: 10px;">
                                    <i class="fas fa-user-check mr-2"></i> Guruh yo'qlamasini olish
                                </button>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0" style="border-radius: 15px; background-color: #ffffff;">
                            <div class="card-body text-center p-4">
                                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2 mb-3"
                                     style="width: 80px;" alt="O'qituvchi">
                                <h5 class="mb-0 font-weight-bold">Muxammed Qudaynazarov</h5>
                                <p class="text-muted">Katta o'qituvchi</p>
                                <hr>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="font-weight-bold">4.9</h6>
                                        <small class="text-muted">Reyting</small>
                                    </div>
                                    <div class="col-4 border-left border-right">
                                        <h6 class="font-weight-bold">120</h6>
                                        <small class="text-muted">Soat</small>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="font-weight-bold">95%</h6>
                                        <small class="text-muted">Faollik</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
