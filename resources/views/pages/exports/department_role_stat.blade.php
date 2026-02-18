<table>
    <thead>
    <tr>
        <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">#</th>
        <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">Fan nomi</th>
        <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">O‘qituvchi</th>
        <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">O‘quv reja</th>
        <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle;">Semestr</th>
        <th colspan="{{ $languages->count() }}" style="font-weight: bold; text-align: center;">Kiritilgan resurslar</th>
    </tr>
    <tr>
        @foreach($languages as $lang)
            <th style="font-weight: bold; text-align: center;">{{ $lang->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($subjects as $index => $item)
        @php
            $teacherCount = $item->teachers->count();
        @endphp

        @if($teacherCount > 0)
            @foreach($item->teachers as $tIndex => $teacher)
                <tr>
                    @if($tIndex === 0)
                        <td rowspan="{{ $teacherCount }}" style="vertical-align: middle; text-align: center;">
                            #{{ $item->id }}
                        </td>
                        <td rowspan="{{ $teacherCount }}" style="vertical-align: middle;">
                            {{ $item->subject->name ?? '-' }}
                        </td>
                    @endif
                    <td>
                        {{ json_decode($teacher->name)->short_name ?? $teacher->name }}
                    </td>
                    <td>
                        {{ $item->curriculum->name }}
                    </td>
                    <td>
                        {{ $item->semester->name }}
                    </td>
                    @foreach($languages as $lang)
                        @php
                            $count = $item->getQuestionCountByTeacherAndLang($teacher->id, $lang->id);
                        @endphp
                        <td style="text-align: center;">
                            {{ $count > 0 ? $count : 0 }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @else
            <tr>
                <td style="vertical-align: middle; text-align: center;">#{{ $item->id }}</td>
                <td style="vertical-align: middle;">{{ $item->subject->name ?? '-' }}</td>
                <td style="color: red; font-style: italic;">-</td>
                <td>
                    {{ $item->curriculum->name }}
                </td>
                <td>
                    {{ $item->semester->name }}
                </td>
                @foreach($languages as $lang)
                    <td style="text-align: center;">0</td>
                @endforeach
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
