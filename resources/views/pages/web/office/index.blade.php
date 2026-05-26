@php use App\Models\Exam;use App\Models\Subject;use App\Models\SubjectList; @endphp
@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="font-weight-bold">Ariza qidirish</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Ariza qidirish</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary mb-4">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="student_id" class="form-control"
                                       placeholder="Talaba ID raqamini kiriting (masalan: 346221100092)..."
                                       value="{{ request('student_id') }}" required>
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Qidirish
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                @if($student)
                    <div class="card">
                        <div class="card-header font-weight-bold bg-info">
                            Talabaning umumiy arizalari ro‘yxati
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th style="width: 10%;">Talaba F.I.Sh.</th>
                                    <td>{{ json_decode($student->name)->full_name ?? $student->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mutaxassislik</th>
                                    <td>
                                        {{ $student->specialty->code ?? '-' }} –
                                        {{ $student->specialty->name ?? '-' }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @forelse($applications as $app)
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <div>
                                        Ariza raqami: {{ $app['application_number'] }}
                                    </div>
                                    <div class="small">
                                        Holati:
                                        @if($app['status'] == 'approved')
                                            tasdiqlangan
                                        @elseif($app['status'] == 'rejected')
                                            bekor qilingan
                                        @endif
                                    </div>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            @if($app['status'] == 'rejected')
                                <div class="overlay-wrapper">
                                    <div class="overlay"></div>
                                </div>
                            @endif
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-center table-custom">
                                    <thead>
                                    <tr>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Fan nomi</th>
                                        <th>Fan guruhi</th>
                                        <th>Semestri</th>
                                        <th>Kredit miqdori</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($app['details'] as $detail)
                                        <tr>
                                            <td class="align-middle">#{{ $detail['id'] }}</td>
                                            <td style="text-align: left" class="align-middle">
                                                {{ $detail['subject_name'] }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $detail['student_group']['name'] ?? 'Guruhga biriktirilmagan' }}
                                            </td>
                                            <td class="align-middle">{{ $detail['semester_name'] }}</td>
                                            <td class="align-middle">{{ $detail['credit'] }}</td>
                                            <td class="align-middle">
                                                @php
                                                    $groupId = $detail['student_group']['id'] ?? null;

                                                    $inExam = Exam::where('failed_subject_id', $detail['failed_subject_id'])
                                                    ->where('group_id', $groupId)->exists();

                                                    $subject_id = null;
                                                    $accessCreate = false;

                                                    if (!$inExam) {
                                                        $subjects = Subject::where('name', $detail['subject_name'])->get();

                                                        foreach ($subjects as $subject) {
                                                            $accessCreate = SubjectList::where('subject_id', $subject->id)
                                                                    ->where('semester_id', $detail['semester_code'])
                                                                    ->where('curriculum_id', $student->curriculum_id)->exists();

                                                            if ($accessCreate) {
                                                                $subject_id = $subject->id;
                                                                break;
                                                            }
                                                        }

                                                        if (empty($detail['student_group'])) {
                                                            $accessCreate = false;
                                                        }
                                                    }
                                                @endphp

                                                @if($accessCreate)
                                                    <form action="{{ route('office_applications.store') }}"
                                                          method="POST">
                                                        @csrf
                                                        <input type="hidden" name="detail"
                                                               value="{{ json_encode($detail) }}">
                                                        <input type="hidden" name="app"
                                                               value="{{ json_encode(['id' => $app['id'], 'number' => $app['application_number'], 'education_year' => $app['education_year'], 'semester_id' => $detail['semester_code'], 'created_at' => $app['created_at'], 'lesson_id' => $subject_id, 'student_id' => $student->id]) }}">
                                                        <button class="btn btn-primary btn-sm" type="submit"
                                                                onclick="return confirm('Talabaga ushbu fandan imtihon yaratmoqchimisiz?')">
                                                            Qo‘shish
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="#" class="btn btn-success btn-sm disabled">
                                                        Qo‘shish
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                    @endforelse
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-search fa-3x mb-3 text-light"></i>
                        <p>
                            Ma’lumotlarni ko‘rish uchun yuqoridagi maydonga talaba ID raqamini
                            kiritib qidirish tugmasini bosing.
                        </p>
                    </div>
                @endif

            </div>
        </section>
    </div>
@endsection
