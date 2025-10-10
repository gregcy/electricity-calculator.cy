<div class="w-full bg-white p-6">
    <h2 class="text-xl font-bold py-4">{{ __('Your Electricity Cost') }}</h2>
    <div class="flex flex-wrap pb-6">
        <div class="w-full md:w-auto pr-4"><span class="font-bold">{{ __('Tariff') }}:</span><span class="font-normal pl-1">{{ $values['tariff'] }}</span></div>
        @if($values['tariff'] === '01' || $values['tariff'] === '08')
        <div class="w-full md:w-auto pr-4"><span class="font-bold">{{ __('Consumption') }}:</span><span class="font-normal pl-1">{{ $values['consumption'] }} kWh</span></div>
        <div class="w-full md:w-auto pr-4"><span class="font-bold">{{ __('Returned Solar Power') }}:</span><span class="font-normal pl-1">{{ $values['credit-amount'] }} kWh</span></div>
        @elseif($values['tariff'] === '02')
        <div class="w-full md:w-auto pr-4"><span class="font-bold pl-4">{{ __('Consumption During Standard Period') }}:</span><span class="font-normal pl-1">{{ $values['consumption-standard'] }} kWh</span></div>
        <div class="w-full md:w-auto pr-4"><span class="font-bold pl-4">{{ __('Consumption During Economy Period') }}:</span><span class="font-normal pl-1">{{ $values['consumption-economy'] }} kWh</span></div>
        @endif
        @php
            $totalValue = floatval(trim($cost['total']->value, "€"));
            if ($values['tariff'] == 02) {
                $consumption = floatval($values['consumption-standard']) + floatval($values['consumption-economy']);
            } else {
                $consumption = floatval($values['consumption']);
            }
            if ($consumption > 0) {
                $costPerKwh = $totalValue / $consumption;
            } else {
                $costPerKwh = 0;
            }
        @endphp
        <div class="w-full md:w-auto pr-4"><span class="font-bold">{{ __('Your cost per kWh') }}:</span><span class="font-normal pl-1">€{{ number_format($costPerKwh, 2) }}</span></div>
    </div>
</div>
<div class="flex flex-wrap bg-white p-6">
    <div class="w-full lg:w-96 mr-0 lg:mr-12 inline-flex items-center justify-center">
        <canvas id="myChart"></canvas>
    </div>
    <div class="w-full lg:w-96 ml-0 lg:ml-16 mt-8 inline-flex items-center justify-center">
        <table id="costBreakdown">
            @foreach($cost as $key => $value)
                <tr>
                    <td class="px-2">
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100%" height="100%" fill="{{ $value->color }}" />
                        </svg>
                    </td>
                    @if($key != 'total')
                        <td class="px-2">{{ __($value->description) }}
                    @else
                    <td class="px-2 font-bold">{{ __($value->description) }}:
                    @endif
                    @if(isset($value->source))
                        <sup class="pl-2">{{ $value->source['superscript'] }}</sup>
                    @endif
                    </td>
                    @if($key != 'total')
                    <td class="px-2 font-mono text-right text-sm">{{ $value->value }}</td>
                    @else
                    <td class="px-2 font-mono font-bold text-right text-sm">{{ __($value->value) }}</td>
                    @endif
                </tr>
            @endforeach
            <tr><td colspan="3"></td></tr>
            @foreach ($sources as $key => $value)
                <tr>
                    <td colspan="3" class="text-sm">{{ $key}}.<a class="text-blue-600" href="{{ __($value['link']) }}" target="_blank"> {{ __($value['description']) }}</a></td>
                </tr>
            @endforeach
        </table>
    </div>
    <script type="module">
        const total = {{ trim($cost['total']->value, "€") }};
        const data = {
            labels: [
                @foreach($cost as $key => $value)
                    @if($key != 'total')
                    decodeHtmlEntities('{!! $value->description !!}'),
                    @endif
                @endforeach
            ],
            datasets: [{
                label: '{!! __('Cost') !!}',
                data: [
                    @foreach($cost as $key => $value)
                    @if($key != 'total')
                        '{{ trim($value->value, "€") }}',
                    @endif
                @endforeach
                ],
                backgroundColor: [
                    @foreach($cost as $key => $value)
                        @if($key != 'total')
                            '{{ $value->color }}',
                        @endif
                    @endforeach
                ],
                borderWidth: 1
            }],
        };
        const doughnutLabel = {
            id: 'doughnutLabel',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data} = chart;

                ctx.save();
                const xCoor = chart.getDatasetMeta(0).data[0].x;
                const yCoor = chart.getDatasetMeta(0).data[0].y;
                ctx.font = 'bold 25px sans-serif';
                ctx.fillStyle = 'rgba(54,162,235,1)';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                let graphTotal = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'EUR' }).format(total);
                ctx.fillText(graphTotal, xCoor, yCoor);
            }
        };
        const config = {
            type: 'doughnut',
            data,
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';

                                if (label) {
                                    label = decodeHtmlEntities(label);
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'EUR' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            },
            plugins: [doughnutLabel]
        };
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        function decodeHtmlEntities(html) {
            var doc = new DOMParser().parseFromString(html, "text/html");
            return doc.documentElement.textContent;
        }


    </script>

</div>
