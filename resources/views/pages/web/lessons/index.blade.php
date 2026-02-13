@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Mening fanlarim</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
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
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title text-sm">Biriktirilgan fanlar ro'yxati</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap text-sm text-center">
                                    <thead>
                                    <tr>
                                        <th style="width: 50px">#</th>
                                        <th>Fan nomi</th>
                                        <th>Guruh va semestri</th>
                                        <th>Ma’sul o‘qituvchilar</th>
                                        <th class="text-right">Amallar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($subjects as $index => $subject)
                                        <tr>
                                            <td>#{{ $subject->subject_id }}</td>
                                            <td class="font-weight-bold">{{ $subject->name }}</td>
                                            <td>
                                                @foreach($subject->groups as $group)
                                                    {{ $group->group->name ?? 'Nomalum' }} /
                                                    {{ $group->semester->name ?? 'Nomalum' }}
                                                @endforeach
                                            </td>
                                            <td></td>
                                            <td class="text-right">
                                                <a href="{{ route('lessons.show', $subject->id) }}"
                                                   class="btn btn-primary btn-xs px-3">
                                                    <i class="fas fa-folder-open mr-1"></i> Resurslar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Sizga hali fanlar biriktirilmagan.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
