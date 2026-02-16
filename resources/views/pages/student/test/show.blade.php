@extends('layouts.app')

@section('title', 'Test jarayoni: ' . $test->name)

@section('content')
    <div class="content-wrapper">
        <section class="content-header pb-1">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h4 class="font-weight-bold">{{ $test->name }}</h4>
                        <p class="text-muted text-sm mb-0">Fan: {{ $test->subject->name ?? 'Nomalum' }} | Jami
                            savollar: {{ $questions_pos->count() }} ta</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form id="examForm" action="{{ route('results.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
                    <div class="row">
                        <div class="col-md-9">
                            @foreach($questions_pos as $index => $question)
                                <div class="card card-outline card-primary mb-3 shadow-sm" id="q_{{ $question->id }}">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title font-weight-bold text-dark" style="line-height: 1.6;">
                                            <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                            {!! $question->question->question ?? $question->question !!}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-0">
                                            @foreach($question->answers as $answer)
                                                <div class="custom-control custom-radio p-1 m-1 mx-3">
                                                    <input class="custom-control-input answer-input"
                                                           type="radio"
                                                           @if(array_key_exists($question->id, $attempts) && $attempts[$question->id] == $answer->id) checked
                                                           @endif
                                                           id="opt_{{ $answer->id }}"
                                                           name="answers[{{ $question->question->id }}]"
                                                           value="{{ $answer->answer->id }}"
                                                           data-question-id="{{ $question->id }}"
                                                           data-index="{{ $index + 1 }}"
                                                           onchange="saveAnswer(this)">
                                                    <label for="opt_{{ $answer->id }}"
                                                           class="custom-control-label w-100" style="cursor: pointer;">
                                                        {{ $answer->answer->text ?? $answer->text }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-3">
                            <div class="sticky-top" style="top: 20px; z-index: 100;">
                                <div class="card card-outline card-danger shadow">
                                    <div class="card-header text-center">
                                        <h3 class="card-title float-none font-weight-bold">
                                            Qolgan vaqt
                                        </h3>
                                    </div>
                                    <div class="card-body text-center py-3">
                                        <h2 class="font-weight-bold text-danger mb-0" id="timerDisplay">
                                            00:00:00
                                        </h2>
                                    </div>
                                </div>

                                <div class="card shadow-sm d-none d-md-block">
                                    <div class="card-header">
                                        <h3 class="card-title text-sm">Savollar xaritasi</h3>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            @foreach($questions_pos as $index => $q)
                                                @php
                                                    $hasAnswer = isset($attempts[$q->id]);
                                                    $btnClass = $hasAnswer ? 'btn-answered' : 'btn-outline-secondary';
                                                @endphp

                                                <a href="#q_{{ $q->id }}"
                                                   id="map_btn_{{ $index + 1 }}"
                                                   class="btn {{ $btnClass }} btn-xs m-1"
                                                   style="width: 30px;">
                                                    {{ $index + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm">
                                    <div class="card-body p-2">
                                        <button type="submit" class="btn btn-success btn-block font-weight-bold"
                                                onclick="finishExam()">
                                            <i class="fas fa-check-circle mr-2"></i> Testni yakunlash
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <style>
        .hover-effect:hover {
            background-color: #f4f6f9;
        }

        .custom-control-label::before {
            top: 0.25rem;
        }

        .custom-control-label::after {
            top: 0.25rem;
        }

        .card-title, .custom-control-label {
            user-select: none;
        }

        /* Savol belgilanganda xaritadagi tugma rangi */
        .btn-answered {
            background-color: #007bff !important;
            color: #fff !important;
            border-color: #007bff !important;
        }
    </style>

    @push('scripts')
        <script>
            // --- AJAX JAVOB YUKLASH QISMI ---
            function saveAnswer(element) {
                const examId = document.getElementById('exam_id').value;
                const questionId = element.getAttribute('data-question-id');
                const answerId = element.value;
                const index = element.getAttribute('data-index');

                // Xaritadagi tugmani rangini o'zgartirish (Javob berildi degan ma'noda)
                const mapBtn = document.getElementById('map_btn_' + index);
                if (mapBtn) {
                    mapBtn.classList.remove('btn-outline-secondary');
                    mapBtn.classList.add('btn-answered');
                }

                // Serverga yuborish
                fetch('/home/exams/answer/upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        exam_id: examId,
                        question_id: questionId,
                        answer_id: answerId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Konsolga chiqarib turamiz (debug uchun)
                            console.log('Javob saqlandi: ' + questionId);
                        } else {
                            console.error('Xatolik:', data.message);
                        }
                    })
                    .catch((error) => {
                        console.error('Internet xatoligi:', error);
                    });
            }

            // --- TIMER VA BOŞQA KODLAR (O'zgarmadi) ---
            let endTime = new Date("{{ $exam->finished_at }}").getTime();
            let serverTime = {{ time() * 1000 }};
            let localTime = new Date().getTime();
            let timeDiff = serverTime - localTime;

            let timerInterval = setInterval(function () {
                let now = new Date().getTime() + timeDiff;
                let distance = endTime - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    document.getElementById("timerDisplay").innerHTML = "00:00:00";
                    submitExam(true);
                    return;
                }
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                document.getElementById("timerDisplay").innerHTML = hours + ":" + minutes + ":" + seconds;
                if (distance < 60000) {
                    document.getElementById("timerDisplay").classList.add("text-blink");
                }
            }, 1000);

            function finishExam() {
                Swal.fire({
                    title: 'Testni yakunlaysizmi?',
                    text: "Barcha javoblar allaqachon saqlab bo'lingan. Natijani hisoblash uchun tasdiqlang.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ha, yakunlash',
                    cancelButtonText: 'Yo‘q, davom etish'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitExam(false);
                    }
                })
            }

            function submitExam(isAuto) {
                window.onbeforeunload = null;
                if (isAuto) {
                    Swal.fire({
                        title: '00:00:00',
                        text: 'Vaqt tugadi! Natija hisoblanmoqda...',
                        icon: 'warning',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                }
                document.getElementById('examForm').submit();
            }

            window.onbeforeunload = function () {
                return "Sahifadan chiqsangiz test to'xtatiladi. Ishonchingiz komilmi?";
            };
            document.addEventListener('contextmenu', event => event.preventDefault());
        </script>

        <style>
            .text-blink {
                animation: blinker 1s linear infinite;
                color: red !important;
            }

            @keyframes blinker {
                50% {
                    opacity: 0;
                }
            }
        </style>
    @endpush

@endsection
