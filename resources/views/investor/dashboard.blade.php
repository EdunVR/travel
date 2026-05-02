@extends('investor.layouts.app')

@section('title', 'Dashboard Investor')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;500;600;700&display=swap');
        .profit-up { color: #10B981; }
        .profit-down { color: #EF4444; }
        .filter-select {
            background-color: #F3F4F6;
            border-radius: 0.375rem;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #D1D5DB;
        }
        .progress-bar { transition: width 0.6s ease; }
        .font-dongle {
            font-family: 'Dongle', sans-serif;
        }
        body {
            font-family: 'Dongle', sans-serif;
            line-height: 1;
        }

        /* Text Sizes yang lebih compact */
        .text-xs { font-size: 0.9rem; line-height: 1; }
        .text-sm { font-size: 1.0rem; line-height: 1; }
        .text-base { font-size: 1.2rem; line-height: 1; }
        .text-lg { font-size: 1.4rem; line-height: 1; }
        .text-xl { font-size: 1.6rem; line-height: 1; }
        .text-2xl { font-size: 1.8rem; line-height: 1; }
        .text-3xl { font-size: 2.0rem; line-height: 1; }
        
        /* Font Weights */
        .font-light { font-weight: 300; }
        .font-normal { font-weight: 400; }
        .font-bold { font-weight: 700; }

        /* Spacing adjustments */
        .compact-gap {
            gap: 0.5rem; /* Mengurangi gap default */
        }

        .compact-p {
            padding: 0.75rem; /* Mengurangi padding card */
        }

        .compact-mt {
            margin-top: 0.25rem; /* Mengurangi margin top */
        }

        .compact-text {
            font-size: 1.0rem; /* Ukuran text lebih kecil */
        }
    </style>
@endpush

@section('content')
    <main class="p-3"> <!-- Mengurangi padding main -->
        <!-- Dashboard Header -->
        <div>
            <p class="text-gray-500 text-center mb-2 text-sm">Ringkasan performa</p> <!-- Mengurangi margin bottom -->
        </div>

        <!-- Stats Cards dengan spacing lebih compact -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-3 compact-gap"> <!-- Mengurangi gap -->
            <!-- Card Total Investasi Aktif -->
            <div class="bg-white rounded-[50px] shadow compact-p hover:shadow-md transition-shadow">
                <div class="flex items-start gap-2">
                    <!-- Icon lebih kecil -->
                    <div class="p-1.5 bg-amber-100 rounded-full text-amber-600 flex-shrink-0">
                        <i class="fas fa-coins text-base"></i> <!-- text-lg -> text-base -->
                    </div>
                    
                    <!-- Konten tengah -->
                    <div class="flex-1 min-w-0 pr-2">
                        <h3 class="text-lg font-bold compact-mt font-dongle" style="font-size: 1.1rem; line-height: 1;">
                            Total<br>Investasi<br>Aktif
                        </h3>
                    </div>
                    
                    <!-- Nilai di sebelah kanan -->
                    <div class="w-[55%]">
                        <div class="text-xl font-bold font-dongle" style="font-size: 1.6rem; text-align: right;">
                            Rp{{ number_format($totalInvestment, 0, ',', '.') }}
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="compact-mt">
                            <div class="flex justify-between text-xs text-gray-500"> <!-- text-sm -> text-xs -->
                                <span class="whitespace-nowrap">dari total</span>
                                <span class="whitespace-nowrap">Rp{{ number_format($totalInvestmentRAW, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-amber-500 h-1.5 rounded-full progress-bar" 
                                    style="width: {{ $totalInvestmentRAW > 0 ? min(($activeInvestment / $totalInvestmentRAW) * 100, 100) : 0 }}%">
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 compact-mt"> <!-- text-sm -> text-xs -->
                                <span class="whitespace-nowrap">sudah ditarik</span>
                                <span class="whitespace-nowrap">Rp{{ number_format($totalWithdrawn, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Rata-rata Keuntungan Bulanan -->
            <div class="bg-white rounded-[50px] shadow compact-p hover:shadow-md transition-shadow">
                <div class="flex items-start gap-2">
                    <!-- Icon -->
                    <div class="p-1.5 bg-amber-100 rounded-full text-amber-600 flex-shrink-0">
                        <i class="fas fa-chart-line text-base"></i> <!-- text-lg -> text-base -->
                    </div>
                    
                    <!-- Konten tengah -->
                    <div class="flex-1 min-w-0 pr-2">
                        <h3 class="text-lg font-bold compact-mt font-dongle" style="font-size: 1.1rem; line-height: 1;">
                            Rata-rata<br>Keuntungan<br>Bulanan
                        </h3>
                    </div>
                    
                    <!-- Nilai di sebelah kanan -->
                    <div class="w-[55%]">
                        <div class="text-xl font-bold font-dongle" style="font-size: 1.6rem; text-align: right;">
                            Rp{{ number_format($averageMonthlyProfit, 0, ',', '.') }}
                        </div>
                        @if($profitChangePercentage > 0)
                            <span class="text-xs text-green-500 font-dongle block whitespace-nowrap" style="font-size: 1.0rem;"> <!-- 1.2rem -> 1.0rem -->
                                +{{ number_format($profitChangePercentage, 2) }}% <i class="fas fa-arrow-up"></i>
                            </span>
                        @elseif($profitChangePercentage < 0)
                            <span class="text-xs text-red-500 font-dongle block whitespace-nowrap" style="font-size: 1.0rem;"> <!-- 1.2rem -> 1.0rem -->
                                {{ number_format($profitChangePercentage, 2) }}% <i class="fas fa-arrow-down"></i>
                            </span>
                        @endif
                        
                        <!-- Progress bar -->
                        <div class="compact-mt">
                            <div class="flex justify-between text-xs text-gray-500 compact-mt"> <!-- text-sm -> text-xs -->
                                <span class="whitespace-nowrap">Bulan Ini</span>
                                <span class="whitespace-nowrap">Rp{{ number_format($currentMonthProfit, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-amber-500 h-1.5 rounded-full progress-bar" 
                                    style="width: {{ min(($currentMonthProfit / max($averageMonthlyProfit, 1)) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Status Pencairan -->
            <div class="bg-white rounded-[50px] shadow compact-p hover:shadow-md transition-shadow">
                <div class="flex items-start gap-2">
                    <!-- Icon -->
                    <div class="p-1.5 bg-amber-100 rounded-full text-amber-600 flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-base"></i> <!-- text-lg -> text-base -->
                    </div>
                    
                    <!-- Konten tengah -->
                    <div class="flex-1 min-w-0 pr-2">
                        <h3 class="text-lg font-bold compact-mt font-dongle" style="font-size: 1.1rem; line-height: 1;">
                            Status<br>Pencairan<br>Saat Ini
                        </h3>
                    </div>
                    
                    <!-- Nilai di sebelah kanan -->
                    <div class="w-[55%]">
                        
                        <!-- Info pencairan -->
                        <div>
                            <div class="flex justify-between text-xs"> <!-- text-sm -> text-xs -->
                                <span class="text-gray-500 whitespace-nowrap">Pencairan Bulan Ini</span>
                                <span class="font-medium whitespace-nowrap">Rp{{ number_format($currentMonthWithdrawal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs"> <!-- text-sm -> text-xs -->
                                <span class="text-gray-500 whitespace-nowrap">Total Pencairan</span>
                                <span class="font-medium whitespace-nowrap">Rp{{ number_format($totalWithdrawals, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="text-right compact-mt">
                            <a href="{{ route('investor.withdrawals') }}" class="text-xs text-purple-600 hover:text-purple-800 font-medium whitespace-nowrap"> <!-- text-sm -> text-xs -->
                                Lihat Selengkapnya <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section dengan spacing lebih compact -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3 compact-gap">
            <!-- Grafik Bagi Hasil dan Pencairan Bulanan -->
            <div class="bg-white rounded-lg shadow compact-p hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold" style="font-size: 1.2rem; line-height: 1;">Bagi Hasil & Pencairan Bulanan</h3>
                    <div class="text-xs bg-gray-100 px-2 py-0.5 rounded-full"> <!-- text-sm -> text-xs -->
                        Rata-rata: {{ number_format($averagePercentage, 2) }}%
                    </div>
                </div>
                <div class="h-40"> <!-- Mengurangi tinggi chart -->
                    <canvas id="profitWithdrawalChart"></canvas>
                </div>
                <div class="compact-mt flex justify-center space-x-3">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                        <span class="text-xs">Bagi Hasil</span> <!-- text-sm -> text-xs -->
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-1"></div>
                        <span class="text-xs">Pencairan</span> <!-- text-sm -> text-xs -->
                    </div>
                </div>
            </div>

            <!-- Grafik Perkembangan Investasi -->
            <div class="bg-white rounded-lg shadow compact-p hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold mb-2" style="font-size: 1.2rem; line-height: 1;">Perkembangan Investasi</h3>
                <div class="h-40"> <!-- Mengurangi tinggi chart -->
                    <canvas id="investmentGrowthChart"></canvas>
                </div>
                <div class="compact-mt text-center text-xs text-gray-500"> <!-- text-sm -> text-xs -->
                    Perkembangan nilai investasi dalam 12 bulan terakhir
                </div>
            </div>
        </div>

        <!-- Aktivitas Terakhir dengan spacing lebih compact -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-3 py-2 border-b flex justify-between items-center compact-p"> <!-- Mengurangi padding -->
                <h3 class="text-lg font-semibold" style="font-size: 1.2rem;">Aktivitas Terakhir</h3>
                <a href="{{ route('investor.activities') }}" class="text-xs text-green-600 hover:text-green-800 font-medium"> <!-- text-sm -> text-xs -->
                    Lihat Semua <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y">
                @forelse($recentActivities as $activity)
                <div class="ml-3 mr-3 hover:bg-gray-50 compact-p"> <!-- Mengurangi margin dan padding -->
                    <div class="flex items-start">
                        <div class="p-1 rounded-full mr-2 compact-text 
                            @if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit') bg-green-100 text-green-600
                            @elseif($activity instanceof \App\Models\InvestorWithdrawal) bg-blue-100 text-blue-600
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit')
                                <i class="fas fa-hand-holding-usd text-xs"></i> <!-- text-sm -> text-xs -->
                            @elseif($activity instanceof \App\Models\InvestorWithdrawal)
                                <i class="fas fa-money-bill-wave text-xs"></i> <!-- text-sm -> text-xs -->
                            @else
                                <i class="fas fa-info-circle text-xs"></i> <!-- text-sm -> text-xs -->
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h4 class="text-base" style="font-size: 1.1rem;"> <!-- text-lg -> text-base -->
                                    @if($activity instanceof \App\Models\AccountInvestment)
                                        {{ $activity->description }}
                                    @else
                                        Pencairan Dana
                                    @endif
                                </h4>
                                <span class="text-2xs text-gray-500"> <!-- text-xs -> text-2xs (lebih kecil) -->
                                    @if($activity instanceof \App\Models\AccountInvestment)
                                        {{ $activity->date->format('d M Y') }}
                                    @else
                                        {{ $activity->requested_at->format('d M Y') }}
                                    @endif
                                    @if($activity instanceof \App\Models\InvestorWithdrawal)
                                        • {{ ucfirst($activity->status) }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 compact-mt"> <!-- text-sm -> text-xs -->
                                @if($activity->amount)
                                    Rp{{ number_format($activity->amount, 0, ',', '.') }} • 
                                @endif
                                {{ $activity->account->bank_name }} ({{ $activity->account->account_number }})
                            </p>
                            @if($activity->notes)
                            <div class="p-1 bg-gray-50 rounded text-2xs text-gray-600 compact-mt"> <!-- text-xs -> text-2xs -->
                                <i class="fas fa-info-circle mr-1"></i> {{ $activity->notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="compact-p text-center text-xs text-gray-500"> <!-- text-sm -> text-xs -->
                    Tidak ada aktivitas terakhir
                </div>
                @endforelse
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Profit & Withdrawal Chart
            const profitWithdrawalCtx = document.getElementById('profitWithdrawalChart').getContext('2d');
            new Chart(profitWithdrawalCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [
                        {
                            label: 'Bagi Hasil',
                            data: @json($monthlyProfits),
                            backgroundColor: '#10B981',
                            borderRadius: 4
                        },
                        {
                            label: 'Pencairan',
                            data: @json($monthlyWithdrawals),
                            backgroundColor: '#3B82F6',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10 // Ukuran font label sumbu x lebih kecil
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    size: 10 // Ukuran font label sumbu y lebih kecil
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 10 // Ukuran font legend lebih kecil
                                }
                            }
                        },
                        tooltip: {
                            bodyFont: {
                                size: 10 // Ukuran font tooltip lebih kecil
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp' + context.raw.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Investment Growth Chart
            const investmentGrowthCtx = document.getElementById('investmentGrowthChart').getContext('2d');
            new Chart(investmentGrowthCtx, {
                type: 'line',
                data: {
                    labels: @json($investmentGrowthLabels),
                    datasets: [{
                        label: 'Nilai Investasi',
                        data: @json($investmentGrowthData),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10 // Ukuran font label sumbu x lebih kecil
                                }
                            }
                        },
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    size: 10 // Ukuran font label sumbu y lebih kecil
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 10 // Ukuran font legend lebih kecil
                                }
                            }
                        },
                        tooltip: {
                            bodyFont: {
                                size: 10 // Ukuran font tooltip lebih kecil
                            },
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp' + context.raw.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
