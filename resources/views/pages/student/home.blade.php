@extends('layouts.app')
@section('style')
    <style>
        .small-box {
            transition: all 0.3s;
        }

        .small-box:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .list-group-item {
            transition: background 0.2s;
            border-left: 4px solid transparent;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left: 4px solid #1cc88a;
        }

        .badge-pill {
            font-size: 0.8rem;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper" style="background-color: #f0f2f5; padding-bottom: 20px;">

        <div class="content-header">
            <div class="container-fluid">
                <div class="font-weight-bold h3">
                    Talaba portali
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row mt-3">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                            <div class="card-header border-0 bg-white py-3">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-book text-success mr-2"></i>
                                    Mening darslarim</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">Algoritmlar</h6>
                                            <small class="text-muted"><i class="far fa-clock"></i> 09:00 - 10:20 |
                                                Domla: A. Toshmatov</small>
                                        </div>
                                        <span class="badge badge-success badge-pill px-3 py-2">Xonada</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">Ma'lumotlar bazasi</h6>
                                            <small class="text-muted"><i class="far fa-clock"></i> 10:30 - 11:50 |
                                                Domla: B. Eshmatov</small>
                                        </div>
                                        <span
                                            class="badge badge-warning text-white badge-pill px-3 py-2">Video dars</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <div class="card-header border-0 bg-white py-3">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-bullhorn text-danger mr-2"></i>
                                    Yangi bildirishnomalar</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info border-0 shadow-sm"
                                     style="border-radius: 12px; background-color: #e3f2fd; color: #0d47a1;">
                                    <i class="fas fa-info-circle mr-2"></i> <strong>Nazorat ishi!</strong> Ertaga soat
                                    10:00 da matematika fanidan test bo'ladi.
                                </div>
                                <button class="btn btn-block btn-success py-2 font-weight-bold"
                                        style="border-radius: 10px;">
                                    <i class="fas fa-upload mr-2"></i> Vazifani yuborish
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

@endsection
