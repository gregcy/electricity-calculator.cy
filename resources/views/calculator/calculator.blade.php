<div class="p-5">
    <form id="eac-calculator" class="mb-2" method="POST" action="{{ route('calculator.calculate',['lang' => app()->getLocale()]) }}">
        @csrf
        <fieldset class="py-4 w-full">
            <label for="tariff-select" id="tariff" class="text-lg font-medium pr-2 w-full block md:inline-block md:w-24">{{ __('Tariff') }}:</label>
            <select id="tariff-select" name="tariff"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:w-[340px]">
                @php
                    $options = [
                        '01' => __('01 - Single Rate Domestic Use'),
                        '02' => __('02 - Two Rate Domestic Use'),
                        '08' => __('08 - Special for Vulnerable Customers'),
                    ];
                    $selectedValue = old('tariff', $values['tariff'] ?? '01');
                @endphp

                @foreach($options as $value => $label)
                    <option value="{{ $value }}" @if($selectedValue == $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            <label for="period" class="text-lg font-medium pr-2 pl-2 mt-4 w-full block md:inline md:w-auto">{{ __('Month') }}:</label>
            <select id="period" name="period"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:inline md:w-44">
                @php
                    $selectedValue = old('period', $values['period'] ?? array_key_last($periods));
                @endphp
  
                @foreach($periods as $value => $label)
                    <option value="{{ $value }}" @if($selectedValue == $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset id="tariff01" class="{{ (isset($values) && in_array($values['tariff'], ['01', '08'])) || !isset($values) ? 'block' : 'hidden' }}">
            <div class="w-full">
                    <label for="consumption" class="text-lg font-medium pr-20 w-full block md:inline-block md:w-96">{{ __('Consumption') }} (kWh):</label>
                <input id="consumption" type="number" name="consumption" step="0.01" min="0" placeholder="0" value="{{ old('consumption', $values['consumption'] ?? 0) }}"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:w-[311px]">
            </div>
            <div class="w-full py-4">
                    <label for="credit-amount" class="text-lg font-medium pr-4 w-full block md:inline-block md:w-96">{{ __('Returned Solar Power') }} (kWh):</label>
                <input id="credit-amount" type="number" name="credit-amount" step="0.01" min="0" placeholder="0" value="{{ old('credit-amount', $values['credit-amount'] ?? 0) }}"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:w-[311px]">
            </div>
        </fieldset>
        <fieldset id="tariff02" class="{{ (isset($values) && $values['tariff'] === '02') ? 'block' : 'hidden' }}">
            <div class="w-full">
                    <label for="consumption-standard" class="text-lg font-medium pr-5 w-full block md:inline-block md:w-[495px]">{{ __('Consumption During Standard Period') }} 09:00-23:00 (kWh):</label>
                <input id="consumption-standard" type="number" name="consumption-standard" step="0.01" min="0" placeholder="0" value="{{ old('consumption-standard', $values['consumption-standard'] ?? 0) }}"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:w-[200px]">
            </div>
            <div class="w-full py-4">
                    <label for="consumption-economy" class="text-lg font-medium pr-4 w-full block md:inline-block md:w-[495px]">{{ __('Consumption During Economy Period') }} 23:00-09:00 (kWh):</label>
                <input id="consumption-economy" type="number" name="consumption-economy" step="0.01" min="0" placeholder="0" value="{{ old('consumption-economy', $values['consumption-economy'] ?? 0) }}"
                class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-md w-full md:w-[200px]">
            </div>
        </fieldset>
        <fieldset class="py-1">
            <label for="include-fixed" class="text-lg font-medium pr-4 inline-block">{{ __("Include Fixed Costs") }}:</label>
            <input id="include-fixed" type="checkbox" name="include-fixed" value="1" @if(old('include-fixed', $values['include-fixed'] ?? 1)) checked @endif
            class="inline-block grow border-gray-300 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50 shadow-md">
        </fieldset>
        <x-primary-button class="mt-4">{{ __('Calculate') }}</x-primary-button>
    </form>
    <script type="module">
            const tour = introJs();
            tour.setOptions({
                nextLabel: '{{ __('Next') }}',
                prevLabel: '{{ __('Back') }}',
                doneLabel: '{{ __('Done') }}',
                steps: [
                    {
                        element: document.querySelector('#tariff-select'),
                        title: '{{ __('Tariff') }}',
                        intro: '{{ __('The tariff is in the top left corner of your bill') }}: <br/><img src="{{ __('/images/EAC-Tariff-en.png') }}" alt="{{ __('Tariff') }}" class="mt-4"/>'
                    },
                    {
                        title: '{{ __('Month') }}',
                        element: document.querySelector('#period'),
                        intro: '{{ __('Select the month your bill was issued. This is the second date of the period') }}:<br/><img src="{{ __('/images/EAC-Period-en.png') }}" alt="{{ __('Date') }}"/>'
                    },
                    {
                        title: '{{ __('Consumption') }}',
                        element: document.querySelector('#consumption'),
                        intro: '{{ __('Consumption is on the left hand side of the bill under the Meter Readings (kWh) heading.  If you have solar panels you should input the third number on the IMP line. Without Solar Panels you should input the Total Consumption') }}:<br/><img src="{{ __('/images/EAC-Consumption-en.png') }}" alt="{{ __('Date') }}"/>'
                    },
                    {
                        title: '{{ __('Returned Solar Power') }}',
                        element: document.querySelector('#credit-amount'),
                        intro: '{{ __('Your total returned power is the sum of credit units brought forward and the third number on the EXP line') }}:<br/><img src="{{ __('/images/EAC-Credit-en.png') }}" alt="{{ __('Date') }}"/>'
                    },
                    {
                        title: '{{ __('Include Fixed Costs') }}',
                        element: document.querySelector('#include-fixed'),
                        intro: '{{ __('Select to include the fixed costs charged by EAC in the calculation') }}'
                    },
                ]
            });
            document.querySelector('#tour').addEventListener('click', () => {
                tour.start();
            });
        </script>
    <div id="tour" class="cursor-pointer mt-4">
        <div class="font-bold text-white bg-blue-700 inline-block px-2 py-1 px-3 rounded-full text-xl shadow-md ml-0 mb-2 md:mb-0 leading-none">?</div>
        <span class="text-lg text-blue-700 font-bold underline pl-2">{{ __('How to use the Calculator') }}</span>
    </div>
</div>
<script>
    const selectElement = document.getElementById('tariff-select');
    const tariff01 = document.getElementById('tariff01');
    const tariff02 = document.getElementById('tariff02');
    const inputs = document.getElementsByTagName('input');
    selectElement.addEventListener('change', function() {
        let selectedValue = selectElement.value;
        if (selectedValue == '01') {
            tariff01.classList.replace('hidden', 'block');
            tariff02.classList.replace('block', 'hidden');
        }
        else if (selectedValue == '02') {
            tariff01.classList.replace('block', 'hidden');
            tariff02.classList.replace('hidden', 'block');
        }
        else if (selectedValue == '08') {
            tariff01.classList.replace('hidden', 'block');
            tariff02.classList.replace('block','hidden');
        }
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].type === 'number') {
                inputs[i].value = '';
            }
        }
    });
</script>
