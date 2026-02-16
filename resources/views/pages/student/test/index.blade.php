@extends('layouts.app') {{-- Yoki layouts.web (qaysi birini ishlatsangiz) --}}

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold text-dark">Imtihonlar va testlar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Testlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @forelse($subjects as $item)
                        @php
                            $subject = $item->subject;
                            $test = $subject->test;
                            $activeExam = \App\Models\Exam::where('test_id', $test->id)
                                ->where('student_id', auth()->id())->where('status', '2')->first();
                        @endphp

                        <div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch">
                            <div class="card card-outline card-primary w-100 shadow-sm hover-shadow">
                                <div class="card-header border-bottom-0">
                                    <h3 class="card-title font-weight-bold text-primary">
                                        <i class="fas fa-book-open mr-2"></i>
                                        {{ $subject->name }}
                                    </h3>
                                    <div class="card-tools">
                                        <span class="badge badge-success">Faol</span>
                                    </div>
                                </div>

                                <div class="card-body pb-0">
                                    <p class="text-muted text-sm mb-3">
                                        {{ $test->name ?? 'Fan bo‘yicha test sinovi' }}
                                    </p>

                                    <div class="row text-center mb-3">
                                        <div class="col-4 border-right">
                                            <h6 class="font-weight-bold mb-0 text-dark">{{ $test->durations }}</h6>
                                            <small class="text-muted">Daqiqa</small>
                                        </div>
                                        <div class="col-4 border-right">
                                            <h6 class="font-weight-bold mb-0 text-dark">{{ $test->questions }}</h6>
                                            <small class="text-muted">Savol</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="font-weight-bold mb-0 text-dark">{{ $test->points }}</h6>
                                            <small class="text-muted">Ball</small>
                                        </div>
                                    </div>
                                    @php($oldx = $test->exams()->orderBy('created_at', 'desc'))
                                    @php($old = $oldx->first())
                                    @php($attmp = $oldx->count())

                                    <ul class="list-group list-group-unbordered mb-3 text-sm">
                                        <li class="list-group-item">
                                            <b>Boshlash vaqti:</b>
                                            <span class="float-right text-muted">
                                                {{ $test->started_at->format('d.m.Y H:i') }}
                                            </span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Tugash vaqti:</b>
                                            <span class="float-right text-muted">
                                                {{ $test->finished_at->format('d.m.Y H:i') }}
                                            </span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Urinishlar soni:</b>
                                            <span class="float-right badge badge-info">
                                                {{ $test->attempts }} ta
                                            </span>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Qayta topshirish:</b>
                                            <span class="float-right badge badge-danger">
                                                {{ ($test->retest == 'y') ? 'Ha' : 'Yo‘q' }}
                                            </span>
                                        </li>
                                        @if($old)
                                            <li class="list-group-item">
                                                <b>Toplangan ball:</b>
                                                <span class="float-right">
                                                {{ $old->result->point }} / {{ $test->points }}
                                            </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                @if ($attmp < $test->attempts || $old->status == '3')
                                    <div class="card-footer bg-white border-top-0">
                                        @if(now()->between($test->started_at, $test->finished_at))
                                            @if($activeExam)
                                                <a href="{{ route('exams.show', $test->id) }}"
                                                   class="btn btn-warning btn-block shadow-sm font-weight-bold">
                                                <span class="text-light">
                                                    Testni davom ettirish
                                                </span>
                                                </a>
                                            @else
                                                <a href="{{ route('exams.show', $test->id) }}"
                                                   onclick="return confirm('Testni boshlashga tayyormisiz? Vaqt ketishni boshlaydi!')"
                                                   class="btn btn-success btn-block shadow-sm">
                                                <span class="text-light">
                                                    @if($old && $old->status == '4')
                                                        Testni qayta topshirish
                                                    @else
                                                        Testni boshlash
                                                    @endif
                                                </span>
                                                </a>
                                            @endif

                                        @elseif(now() < $test->started_at)
                                            <button class="btn btn-secondary btn-block disabled" disabled>
                                                <i class="fas fa-clock mr-2"></i>
                                                Hali boshlanmadi
                                            </button>
                                        @else
                                            <button class="btn btn-danger btn-block disabled" disabled>
                                                <i class="fas fa-lock mr-2"></i>
                                                Muddat tugadi
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="callout callout-info text-center py-5">
                                <i class="fas fa-clipboard-check fa-4x text-info mb-3"></i>
                                <h5>Hozircha testlar mavjud emas</h5>
                                <p>Sizga biriktirilgan fanlardan hali test sinovlari yaratilmagan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <style>
        /* Kartochka ustiga sichqoncha borganda chiroyli soya berish */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        }
    </style>
@endsection
