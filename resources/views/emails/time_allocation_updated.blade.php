<h2>Bonjour {{ $employee->firstName }} {{ $employee->lastName }},</h2>

<p>Votre répartition de temps a été mise à jour.</p>

{{-- <h3>Année : {{ $timeAllocations[0]['Year'] ?? 'Non spécifiée' }}</h3> --}}

@php
    $percentageColumns = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Total'];
@endphp

<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; border: 1px solid #ccc;">
    <thead>
        <tr>
            @foreach(array_keys($timeAllocations[0]) as $key)
                @if($key !== 'Year')
                    <th style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 8px; text-align: left;">
                        {{ $key }}
                    </th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($timeAllocations as $row)
            <tr>
                @foreach($row as $key => $value)
                    @if($key !== 'Year')
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            @if(in_array($key, $percentageColumns) && is_numeric($value))
                                {{ rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.') }}%
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top: 20px;">Merci.</p>
