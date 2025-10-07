@if ($receiver)
    <h3>Hello {{ $employee->lastName }} {{ $employee->name }},</h3>
    <p>Your <strong>{{ date('Y') }}</strong>  Activity Contribution  has been updated.</p>
@else
    <h2>Hello {{ $employee->supervisor->lastName }} {{ $employee->supervisor->name }},</h2>
    <p>The <strong>{{ date('Y') }}</strong> activity contribution   of your staff <strong>{{ $employee->lastName }} {{ $employee->name }}</strong> has been updated.</p>
@endif
{{-- <h3>Année : {{ $timeAllocations[0]['Year'] ?? 'Non spécifiée' }}</h3> --}}
{{-- 
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
</table> --}}

@php
    $percentageColumns = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Total'];

    // Initialisation des totaux
    $totals = array_fill_keys($percentageColumns, 0);

    foreach ($timeAllocations as $row) {
        foreach ($percentageColumns as $col) {
            if (isset($row[$col]) && is_numeric($row[$col])) {
                $totals[$col] += $row[$col];
            }
        }
    }

    // Obtenir les clés pour les colonnes à afficher
    $columns = array_keys($timeAllocations[0]);
@endphp

<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; border: 1px solid #ccc;">
    <thead>
        <tr>
            @foreach($columns as $key)
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

   
        <tr   style="font-weight: bold; background-color: #f0f0f0;">
            @foreach($columns as $key)
                @if($key !== 'Year')
                        @if($key === 'Agreement')
                            <td colspan="2">Total</td>
                        @elseif($key === 'Bus')
                          
                        @elseif(in_array($key, $percentageColumns))
                           <td> {{ rtrim(rtrim(number_format($totals[$key], 2, '.', ''), '0'), '.') }}%</td>
                        @endif
                @endif
            @endforeach
        </tr>
    </tbody>
</table>


{{-- <p style="margin-top: 20px;">Merci.</p> --}}
