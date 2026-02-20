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
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-book text-success mr-2"></i>
                                    Mening fanlarim
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @forelse($lessons as $lesson)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">
                                                    {{ $lesson->failed_subject->subject_name }}
                                                </h6>
                                                <small class="text-muted">
                                                    <div class="badge badge-primary">
                                                        <i class="far fa-clock"></i>
                                                        {{ $lesson->created_at->format('d.m.Y H:i') }}
                                                    </div>
                                                    <div class="badge badge-success">
                                                        @forelse($lesson->subject->subject->teachers as $teacher)
                                                            {{ json_decode($teacher->name)->short_name }}
                                                        @empty
                                                            O‘qituvchi biriktirilmagan
                                                        @endforelse
                                                    </div>
                                                    @if($lesson->finished == '1')
                                                        <div class="badge badge-info">
                                                            Yakunlangan
                                                        </div>
                                                    @endif
                                                </small>
                                            </div>
                                            <span class="badge badge-success badge-pill px-3 py-2">
                                                @if($lesson->finished == '1')
                                                    {{ $lesson->results->first()->point }} ball
                                                @else
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                @endif
                                            </span>
                                        </li>
                                    @empty
                                        <li class="list-group-item py-3">
                                            <div class="text-muted text-center small">
                                                «Qarzdor fanlar» bo‘limiga o‘ting va «Arizalarni yangilash» tugmasi
                                                orqali fanlarni yuklab oling.
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <div class="card-header border-0 bg-white py-3">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-bullhorn text-danger mr-2"></i>
                                    Yangi bildirishnomalar
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info border-0 shadow-sm"
                                     style="border-radius: 12px; background-color: #e3f2fd; color: #0d47a1;">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    @if(auth()->user()->specialty->department->access == '1')
                                        <strong>Nazorat ishi!</strong> Hozirgi vaqtda «Qarzdor fanlar» bo‘limiga o‘tish
                                        orqali akademik qarzdoorligingiz bor bo‘lgan fanlardan yakuniy nazoratlarni
                                        topshirishingiz mumkin.
                                    @else
                                        <strong>Nazorat ishi!</strong> Yakuniy nazoratga qatnashish uchun Tizim
                                        administrator tomonidan ruxsat berilmagan.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

@endsection
