<table>
    <thead>
    <tr>
        <th>S. No</th>
        <th>Subject</th>
        <th>Letter No</th>
        <th>Letter Addressee (Main)</th>
        <th>Letter Addressee (Copy To)</th>
        <th>Letter Date</th>
        <th>Cell</th>
        <th>Issue Date</th>
    </tr>
    </thead>

    <tbody>
    @foreach($issues as $issue)
        <tr>
            <td>{{ $issue->id }}</td>
            <td>{{ $issue->subject }}</td>
            <td>{{ $issue->letter_no ?? '—' }}</td>
            <td>{{ $issue->letter_addressee_main }}</td>
            <td>{{ $issue->letter_addressee_copy_to }}</td>
            <td>{{ $issue->letter_date ? \Carbon\Carbon::parse($issue->letter_date)->format('d/m/Y') : '—' }}</td>
            <td>{{ $issue->cell?->name }}</td>
            <td>{{ $issue->created_at ? \Carbon\Carbon::parse($issue->created_at)->format('d/m/Y') : '—' }}</td>


        </tr>
    @endforeach
    </tbody>
</table>
