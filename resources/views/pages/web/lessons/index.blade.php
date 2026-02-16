@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Mening fanlarim</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Fanlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-outline card-primary shadow-sm">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-sm text-center">
                                    <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px" class="text-center">#</th>
                                        <th>Fan nomi</th>
                                        <th>Guruh / Semestr</th>
                                        <th>Ma’sul o‘qituvchilar</th>
                                        <th>Savollar</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($subjects as $index => $subject)
                                        <tr>
                                            <td class="text-center text-muted">
                                                #{{ $subject->subject_id }}
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                {{ $subject->name }}
                                            </td>
                                            <td>
                                                @forelse($subject->groups as $group)
                                                    <span class="badge badge-success">
                                                        {{ $group->group->name ?? 'N/A' }} / {{ $group->semester->name ?? 'N/A' }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted small">Guruh biriktirilmagan</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @foreach($subject->teachers as $teacher)
                                                    <span class="badge badge-primary" style="font-weight: normal;">
                                                        {{ json_decode($teacher->name)->short_name ?? $teacher->short_name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $subject->test?->questions_list->count() ?? 0 }}
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('lessons.show', $subject->id) }}"
                                                   class="btn btn-primary btn-xs px-3 shadow-sm">
                                                    <i class="fas fa-folder-open mr-1"></i> Resurslar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-info-circle mb-2"></i><br>
                                                Sizga hali fanlar biriktirilmagan.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($subjects->hasPages())
                                <div class="card-footer bg-white">
                                    <div class="float-right">
                                        {{ $subjects->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
