@extends('layouts.web')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Statistikalar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Statistikalar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">

                {{-- Export so'rovlari --}}
                <div class="card card-outline card-primary shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-file-export mr-2 text-primary"></i>Hisobotlar
                        </h5>
                        <small class="text-muted">Yuklab olish navbatga qo'shiladi — sahifa kutilmaydi.</small>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            @php($ci = 1)
                            @foreach($stats as $index => $stat)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                    style="border-radius:8px; margin-bottom:5px;">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-primary mr-3 p-2" style="font-size:13px;">#{{ $ci++ }}</span>
                                        <div class="font-weight-bold @if($stat['disabled']) text-muted @else text-dark @endif"
                                             style="font-size:15px;">
                                            {{ $stat['name'] }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-nowrap">
                                        @if($stat['disabled'])
                                            <span class="btn btn-secondary btn-sm rounded-pill px-3 disabled">
                                                <i class="fa fa-ban mr-1"></i> Mavjud emas
                                            </span>
                                        @else
                                            <form action="{{ route('downloads.store') }}" method="POST"
                                                  class="d-flex align-items-center m-0">
                                                @csrf
                                                <input type="hidden" name="type" value="{{ $stat['type'] }}">
                                                @if($stat['optional']['status'])
                                                    <select name="optional_id"
                                                            class="form-control form-control-sm mr-2 shadow-none"
                                                            style="min-width:180px; border-radius:6px;">
                                                        @foreach($stat['optional']['values'] as $value)
                                                            <option value="{{ $value['id'] }}"
                                                                    @if($value['status'] == '1') selected @endif>
                                                                {{ $value['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <button type="submit"
                                                        class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                                    <i class="fa fa-cloud-upload-alt mr-1"></i> Navbatga qo'shish
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- So'nggi yuklanmalar --}}
                <div class="card card-outline card-success shadow-sm rounded-lg">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-download mr-2 text-success"></i>So'nggi yuklanmalar
                        </h5>
                        <small class="text-muted">Tayyor fayllar 24 soat saqlanadi.</small>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $downloads = \App\Models\Download::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();
                        @endphp
                        @if($downloads->isEmpty())
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Hech qanday yuklanma yo'q. Yuqoridan hisobot tanlang.
                            </div>
                        @else
                            <table class="table table-hover text-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nomi</th>
                                        <th style="width:120px">Holati</th>
                                        <th style="width:140px">Vaqti</th>
                                        <th style="width:120px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($downloads as $dl)
                                        <tr id="dl-row-{{ $dl->id }}">
                                            <td>#{{ $dl->id }}</td>
                                            <td>{{ $dl->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $dl->statusBadge() }} dl-status-badge"
                                                      data-id="{{ $dl->id }}"
                                                      data-status="{{ $dl->status }}">
                                                    @if($dl->isPending())
                                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                                    @endif
                                                    {{ $dl->statusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $dl->created_at->format('d.m.Y H:i') }}</td>
                                            <td class="text-nowrap">
                                                @if($dl->isReady())
                                                    <a href="{{ route('downloads.file', $dl->id) }}"
                                                       class="btn btn-success btn-xs mr-1">
                                                        <i class="fas fa-file-excel mr-1"></i> Yuklab olish
                                                    </a>
                                                @elseif($dl->status === 'failed')
                                                    <span class="text-danger small" title="{{ $dl->reason }}">
                                                        <i class="fas fa-exclamation-triangle"></i> Xatolik
                                                    </span>
                                                @endif
                                                <form action="{{ route('downloads.destroy', $dl->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs"
                                                            onclick="return confirm('O\'chirilsinmi?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script>
// Pending/processing statuslarni har 3 soniyada tekshirish
(function pollDownloads() {
    var pendingBadges = document.querySelectorAll('.dl-status-badge');
    var pending = Array.from(pendingBadges).filter(function(b) {
        return b.dataset.status === 'pending' || b.dataset.status === 'processing';
    });

    if (pending.length === 0) return;

    pending.forEach(function(badge) {
        var id = badge.dataset.id;
        fetch('/home/downloads/' + id + '/status', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            badge.dataset.status = data.status;
            badge.className = 'badge badge-' + data.badge + ' dl-status-badge';
            badge.innerHTML = (data.status === 'pending' || data.status === 'processing')
                ? '<i class="fas fa-spinner fa-spin mr-1"></i>' + data.label
                : data.label;

            // Tayyor bo'lsa "Yuklab olish" tugmasini qo'shish
            if (data.ready) {
                var row = document.getElementById('dl-row-' + id);
                var cell = row.querySelector('td:last-child');
                var dlBtn = document.createElement('a');
                dlBtn.href = '/home/downloads/' + id + '/file';
                dlBtn.className = 'btn btn-success btn-xs mr-1';
                dlBtn.innerHTML = '<i class="fas fa-file-excel mr-1"></i> Yuklab olish';
                cell.insertBefore(dlBtn, cell.firstChild);
            }
        })
        .catch(function() {});
    });

    setTimeout(pollDownloads, 3000);
})();
</script>
@endsection
