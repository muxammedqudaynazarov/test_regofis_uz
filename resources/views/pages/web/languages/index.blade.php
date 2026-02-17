@extends('layouts.web')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Tillar ro‘yxati</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Tizim tillari</li>
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
                                <th style="width: 7%">#</th>
                                <th style="text-align: left">Nomi</th>
                                <th class="text-right" style="width: 7%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($languages as $language)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $language->id }}</td>
                                    <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                        {{ $language->name }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox"
                                                   class="custom-control-input status-toggle"
                                                   id="customSwitch{{ $language->id }}"
                                                   data-id="{{ $language->id }}"
                                                   data-url="{{ route('languages.update', $language->id) }}"
                                                   @if($language->status == '1') checked @endif>
                                            <label class="custom-control-label"
                                                   for="customSwitch{{ $language->id }}"></label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tillar ro‘yxati bo‘sh.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('change', '.status-toggle', function () {
                var checkbox = $(this);
                var id = checkbox.data('id');
                var url = checkbox.data('url');
                var status = checkbox.is(':checked') ? 1 : 0;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        status: status,
                        _method: 'PUT'
                    },
                    success: function (response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    },
                    error: function (xhr, status, error) {
                    }
                });
            });
        });
    </script>
@endsection

