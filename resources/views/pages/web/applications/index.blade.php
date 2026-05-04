@extends('layouts.web')

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #6f42c1 !important;
            border-color: #643ab0 !important;
            color: #fff !important;
        }

        .table-custom th, .table-custom td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="font-weight-bold">Arizalar ro‘yxati</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Arizalar ro‘yxati</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="input-group input-group-sm my-3">
                    <input type="text" id="searchInput" class="form-control form-control-lg"
                           placeholder="Ariza № / F.I.Sh / Talaba ID yozib Enter bosing..."
                           value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="button" id="searchBtn" class="btn btn-primary">
                            <i class="fas fa-search"></i> Qidirish
                        </button>
                    </div>
                </div>
               {{-- <div class="text-right mb-3">
                    <button type="button" id="searchBtn" class="btn btn-primary">
                        <i class="fas fa-search"></i> Qidirish
                    </button>
                </div>--}}

                <div class="card card-outline card-primary shadow-sm position-relative">
                    <div id="tableLoader" class="overlay" style="display: none;">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center table-custom">
                            <thead>
                            <tr>
                                <th style="width: 8%">#</th>
                                <th style="text-align: left; width: 25%">Ariza beruvchi</th>
                                <th>Mutaxassislik / Yo‘nalish</th>
                                <th>O‘quv yili</th>
                                <th class="text-nowrap">Qayta o‘qish</th>
                                <th style="width: 30%;">Fanlari</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody id="tableBody">
                            @include('pages.web.applications.table_rows', ['applications' => $applications])
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right" id="paginationContainer">
                                {{ $applications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            function fetchApplications() {
                let searchQuery = $('#searchInput').val();
                let loader = $('#tableLoader');
                loader.show();
                $.ajax({
                    url: "{{ route('applications.index') }}",
                    type: "GET",
                    data: {search: searchQuery},
                    success: function (response) {
                        if (response.table_html) {
                            $('#tableBody').html(response.table_html);
                            $('#paginationContainer').html(response.pagination_html);
                        }
                        loader.hide();
                    },
                    error: function () {
                        loader.hide();
                        toastr.error('Ma’lumotlarni yuklashda xatolik yuz berdi!');
                    }
                });
            }

            $('#searchInput').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    fetchApplications();
                }
            });
            $('#searchBtn').on('click', function () {
                fetchApplications();
            });
            $(document).on('click', '#paginationContainer a', function (e) {
                e.preventDefault();
                let pageUrl = $(this).attr('href');
                let loader = $('#tableLoader');
                loader.show();
                $.ajax({
                    url: pageUrl,
                    type: "GET",
                    success: function (response) {
                        if (response.table_html) {
                            $('#tableBody').html(response.table_html);
                            $('#paginationContainer').html(response.pagination_html);
                        }
                        loader.hide();
                    }
                });
            });
        });
    </script>
@endsection
