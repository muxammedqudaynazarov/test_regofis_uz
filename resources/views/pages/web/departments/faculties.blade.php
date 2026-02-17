@extends('layouts.web')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Fakultetlar ro‘yxati</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Fakultetlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="text-align: left;">Fakultet nomi</th>
                                <th style="width: 35%">Kafedralar</th>
                                <th style="width: 7%;">O‘quv rejalar</th>
                                <th style="width: 7%;">Talabalar</th>
                                <th style="width: 5%;">Ruxsat</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($faculties as $faculty)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $faculty->id }}</td>
                                    <td style="text-align: left; vertical-align: middle" class="font-weight-bold">
                                        {{ $faculty->name }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        @foreach($faculty->children as $child)
                                            <div class="badge badge-info">{{ $child->name }}</div>
                                        @endforeach
                                    </td>
                                    <td style="vertical-align: middle">{{ $faculty->curricula->count() }}</td>
                                    <td style="vertical-align: middle">0</td>
                                    <td style="vertical-align: middle">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox"
                                                   class="custom-control-input access-toggle"
                                                   id="accessSwitch{{ $faculty->id }}"
                                                   data-id="{{ $faculty->id }}"
                                                   data-url="{{ route('departments.update', $faculty->id) }}"
                                                {{ $faculty->access == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                   for="accessSwitch{{ $faculty->id }}"></label>
                                        </div>
                                    </td>

                                    <td style="vertical-align: middle">
                                        <a href="#" class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-cloud-download-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Fakultetlar topilmadi.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $faculties->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('change', '.access-toggle', function () {
                var checkbox = $(this);
                var url = checkbox.data('url');
                var access = checkbox.is(':checked') ? 1 : 0;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        access: access,
                        _method: 'PUT'
                    },
                    success: function (response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });

                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });

                    },
                    error: function (xhr) {
                    }
                });
            });
        });
    </script>
@endsection
