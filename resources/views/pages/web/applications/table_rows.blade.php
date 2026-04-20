@forelse($applications as $item)
    <tr>
        <td>
            <code>{{ $item->application_number }}</code>
        </td>
        <td style="text-align: left">
            <div class="font-weight-bold">
                {{ json_decode($item->student->name)->full_name ?? 'Ism topilmadi' }}
            </div>
            <div class="small text-muted">{{ $item->student->id }}</div>
        </td>
        <td>
            {{ optional($item->student->specialty)->code }} - {{ optional($item->student->specialty)->name }}
        </td>
        <td>
            {{ optional($item->edu_year)->name }}
        </td>
        <td>
            @foreach($item->exams as $subject)
                <div class="badge {{ $subject->finished == '1' ? 'badge-success' : 'badge-primary' }}">
                    {{ optional(optional($subject->subject)->subject)->name ?? 'Fan topilmadi' }}
                </div>
            @endforeach
        </td>
        <td class="text-nowrap">
            <a href="{{ route('applications.show', $item->application_number) }}"
               class="btn btn-default btn-sm shadow-sm">
                <i class="fa fa-eye text-primary"></i> Ko'rish
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-box-open fa-2x mb-2 text-light"></i><br>
            Arizalar topilmadi.
        </td>
    </tr>
@endforelse
