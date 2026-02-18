@extends('layouts.web')

@section('style')
    <style>
        .table-custom {
            border-collapse: collapse !important;
            width: 100%;
            border: 1px solid #dee2e6;
        }

        .table-custom th, .table-custom td {
            border: 1px solid #eee !important;
            padding: 4px 5px !important;
            vertical-align: middle !important;
            font-size: 14px;
        }

        .table-custom thead th {
            color: #495057;
        }

        .no-teacher-row td {
            color: #e03131;
        }

        .table-custom tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Kafedra statistikasi (Savollar bazasi)</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title">Fanlar va o‘qituvchilar kesimida</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-middle text-sm text-center table-custom">
                            <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Fan nomi</th>
                                <th rowspan="2">Biriktirilgan o‘qituvchi</th>
                                <th colspan="{{ $languages->count() }}">Kiritilgan resurslar (tillar kesimida)</th>
                            </tr>
                            <tr>
                                @foreach($languages as $lang)
                                    <th style="width: 7%">{{ $lang->name }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody class="table-compact">
                            @forelse($subjects as $index => $item)
                                @php
                                    $teacherCount = $item->teachers->count();
                                @endphp
                                @if($teacherCount > 0)
                                    @foreach($item->teachers as $tIndex => $teacher)
                                        <tr>
                                            @if($tIndex === 0)
                                                <td rowspan="{{ $teacherCount }}">
                                                    #{{ $item->id }}
                                                </td>
                                                <td rowspan="{{ $teacherCount }}" class="text-left">
                                                    <div class="font-weight-bold">
                                                        {{ $item->subject->name ?? '---' }}
                                                    </div>
                                                    <div class="small">
                                                        {{ $item->curriculum->name }}
                                                    </div>
                                                    <div class="small">
                                                        {{ $item->semester->name }}
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="p-0 m-0" style="text-transform: capitalize">
                                                {{ strtolower(json_decode($teacher->name)->short_name ?? $teacher->name) }}
                                            </td>
                                            @foreach($languages as $lang)
                                                @php
                                                    $count = $item->getQuestionCountByTeacherAndLang($teacher->id, $lang->id);
                                                @endphp
                                                <td>
                                                    {{ $count ?? 0 }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            #{{ $item->id }}
                                        </td>
                                        <td class="text-left">
                                            <div class="font-weight-bold">
                                                {{ $item->subject->name ?? '---' }}
                                            </div>
                                            <div class="small">
                                                {{ $item->curriculum->name }}
                                            </div>
                                            <div class="small">
                                                {{ $item->semester->name }}
                                            </div>
                                        </td>
                                        <td class="text-danger small">-</td>
                                        @foreach($languages as $lang)
                                            <td class="text-muted">0</td>
                                        @endforeach
                                    </tr>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="{{ 3 + $languages->count() }}" class="text-center py-4 text-muted">
                                        Ma'lumotlar topilmadi.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
