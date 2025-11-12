@extends('layouts.app')

@section('title', isset($qoldiq) ? 'Қолдиқни таҳрирлаш' : 'Янги қолдиқ қўшиш')

@section('content')
<div class="min-h-screen bg-gray-100 py-6 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 border-t-4 border-blue-600">
            <div class="px-6 py-5">
                <h1 class="text-2xl font-bold text-blue-800">
                    {{ isset($qoldiq) ? 'Қолдиқни таҳрирлаш' : 'Янги қолдиқ қўшиш' }}
                </h1>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ isset($qoldiq) ? route('qoldiq.update', $qoldiq->id) : route('qoldiq.store') }}"
                  method="POST"
                  class="p-6 space-y-6">
                @csrf
                @if(isset($qoldiq))
                    @method('PUT')
                @endif

                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Қолдиқ санаси <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="sana"
                           value="{{ old('sana', $qoldiq->sana ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('sana') border-red-500 @enderror"
                           required>
                    @error('sana')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Мисол: 2024-01-01 (йил бошлангич холати учун)
                    </p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Сумма (сўм) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="summa"
                           value="{{ old('summa', $qoldiq->summa ?? '') }}"
                           step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('summa') border-red-500 @enderror"
                           required>
                    @error('summa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Мисол: 48754073412.35
                    </p>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Қолдиқ тури <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio"
                                   name="tur"
                                   value="plus"
                                   {{ old('tur', $qoldiq->tur ?? 'plus') === 'plus' ? 'checked' : '' }}
                                   class="mr-2">
                            <span class="text-sm">
                                <span class="font-semibold text-green-700">Plus (+)</span> - Факт тўловларга қўшилади
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio"
                                   name="tur"
                                   value="minus"
                                   {{ old('tur', $qoldiq->tur ?? '') === 'minus' ? 'checked' : '' }}
                                   class="mr-2">
                            <span class="text-sm">
                                <span class="font-semibold text-red-700">Minus (-)</span> - Факт тўловлардан айрилади
                            </span>
                        </label>
                    </div>
                    @error('tur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Изоҳ
                    </label>
                    <textarea name="izoh"
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('izoh') border-red-500 @enderror"
                              placeholder="Қолдиқ ҳақида қўшимча маълумот...">{{ old('izoh', $qoldiq->izoh ?? '') }}</textarea>
                    @error('izoh')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Example Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">📝 Мисол:</h4>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><strong>Сценария 1:</strong> 2024 йил 1-январь холатига 48,754,073,412.35 сўм тушган</p>
                        <p class="ml-4">→ Сана: 2024-01-01, Сумма: 48754073412.35, Тур: Plus (+)</p>

                        <p class="mt-3"><strong>Сценария 2:</strong> 2025 йил 1-нояброгача 56,564,353,036.04 сўм тўланган</p>
                        <p class="ml-4">→ Сана: 2025-11-01, Сумма: 56564353036.04, Тур: Minus (-)</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('qoldiq.index') }}"
                       class="text-gray-600 hover:text-gray-800">
                        ← Бекор қилиш
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded transition-colors">
                        {{ isset($qoldiq) ? 'Сақлаш' : 'Қўшиш' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
