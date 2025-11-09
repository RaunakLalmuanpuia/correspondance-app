<table>
    <thead>
    <tr>
        <th>S. No</th>
        <th>Subject</th>
        <th>Letter No</th>
        <th>Letter Date</th>
        <th>Received From</th>
        <th>Cell</th>
        <th>Name of DA</th>
        <th>Received Date</th>
    </tr>
    </thead>

    <tbody>
    @foreach($receipts as $receipt)
        <tr>
            <td>{{ $receipt->id }}</td>
            <td>{{ $receipt->subject }}</td>
            <td>{{ $receipt->letter_no ?? '—' }}</td>
            <td>{{ $receipt->letter_date ? \Carbon\Carbon::parse($receipt->letter_date)->format('d/m/Y') : '—' }}</td>
            <td>{{ $receipt->received_from }}</td>
            <td>{{ $receipt->cell?->name }}</td>
            <td>{{ $receipt->name_of_da }}</td>
            <td>{{ $receipt->created_at ? \Carbon\Carbon::parse($receipt->created_at)->format('d/m/Y') : '—' }}</td>

        </tr>
    @endforeach
    </tbody>
</table>
