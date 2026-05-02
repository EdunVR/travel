
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $package->package_name }} | HM Tour</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{'green-brand':'#2E7D32','green-mid':'#4CAF50','green-pale':'#E8F5E9'}}}}</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*{font-family:'Nunito',sans-serif}
.font-playfair{font-family:'Playfair Display',serif}
html{scroll-behavior:smooth}
#navbar{background:rgba(255,255,255,.97);backdrop-filter:blur(12px);border-bottom:1px solid rgba(46,125,50,.1);box-shadow:0 2px 16px rgba(46,125,50,.08)}
.bg-green-gradient{background:linear-gradient(135deg,#2E7D32,#4CAF50)}
.text-green-gradient{background:linear-gradient(135deg,#2E7D32,#4CAF50);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.stats-bar{background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.10);border:1px solid rgba(46,125,50,.1)}
.stats-item{border-right:1px solid #e5e7eb}
.stats-item:last-child{border-right:none}
.gallery-main{border-radius:16px;overflow:hidden}
.gallery-thumb{border-radius:8px;overflow:hidden;cursor:pointer;transition:all .2s;border:2px solid transparent}
.gallery-thumb.active,.gallery-thumb:hover{border-color:#2E7D32}
.sticky-form{position:sticky;top:88px}
.card-hover{transition:all .3s ease}
.card-hover:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(46,125,50,.15)}
.price-pkg-btn{border:2px solid #e5e7eb;border-radius:12px;padding:12px;cursor:pointer;transition:all .2s;background:#fff}
.price-pkg-btn.selected,.price-pkg-btn:hover{border-color:#2E7D32;background:#E8F5E9}
.variant-btn{border:2px solid #e5e7eb;border-radius:8px;padding:8px 14px;cursor:pointer;transition:all .2s;font-size:.8rem;font-weight:700}
.variant-btn.selected,.variant-btn:hover{border-color:#2E7D32;background:#E8F5E9;color:#2E7D32}
.breadcrumb-item+.breadcrumb-item::before{content:'/';margin:0 8px;color:#9ca3af}
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 py-3">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
<a href="/"><img src="{{ url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png') }}" alt="HM Tour" class="h-11 w-auto object-contain" onerror="this.style.display='none'"></a>
<div class="hidden lg:flex items-center gap-6">
<a href="/" class="text-gray-600 hover:text-green-brand text-sm font-medium">Beranda</a>
<a href="/#paket" class="text-gray-600 hover:text-green-brand text-sm font-medium">Paket</a>
<a href="/#kontak" class="text-gray-600 hover:text-green-brand text-sm font-medium">Kontak</a>
</div>
<a href="https://wa.me/628976688800" target="_blank" class="bg-green-gradient text-white font-semibold px-5 py-2.5 rounded-full text-sm hover:opacity-90 shadow-md"><i class="fab fa-whatsapp mr-1.5"></i> Konsultasi</a>
</div>
</nav>

<div class="pt-20">
<div class="bg-white border-b border-gray-100 py-3">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center text-sm text-gray-500">
<a href="/" class="hover:text-green-brand breadcrumb-item">Beranda</a>
<a href="/#paket" class="hover:text-green-brand breadcrumb-item">Paket</a>
<span class="breadcrumb-item text-gray-800 font-semibold">{{ Str::limit($package->package_name,40) }}</span>
</div>
</div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div class="grid lg:grid-cols-3 gap-8">

{{-- LEFT --}}
<div class="lg:col-span-2 space-y-6">

{{-- Gallery --}}
<div>
<div class="gallery-main mb-3 bg-gray-100" style="height:420px;cursor:pointer" onclick="openImageModal('{{ $package->image_path ? asset('storage/'.$package->image_path) : '' }}')">
@if($package->image_path)
<img id="gallery-main-img" src="{{ asset('storage/'.$package->image_path) }}" alt="{{ $package->package_name }}" class="w-full h-full object-cover" onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)'">
@elseif(is_array($package->package_photos) && count($package->package_photos)>0)
<img id="gallery-main-img" src="{{ asset('storage/'.$package->package_photos[0]) }}" alt="{{ $package->package_name }}" class="w-full h-full object-cover">
@else
<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-100 to-green-200"><i class="fas fa-kaaba text-green-400 text-6xl"></i></div>
@endif
</div>
@if(is_array($package->package_photos) && count($package->package_photos)>1)
<div class="flex gap-2 overflow-x-auto pb-1">
@foreach($package->package_photos as $i=>$photo)
<div class="gallery-thumb flex-shrink-0 w-20 h-16 {{ $i===0?'active':'' }}" onclick="switchImg('{{ asset('storage/'.$photo) }}',this)">
<img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover">
</div>
@endforeach
</div>
@endif
</div>

{{-- Title --}}
<div>
<div class="flex flex-wrap items-center gap-2 mb-3">
<span class="bg-green-gradient text-white text-xs font-bold px-3 py-1 rounded-full">{{ ucwords(str_replace('_',' ',$package->package_type)) }}</span>
@if($package->departure_date && \Carbon\Carbon::parse($package->departure_date)->isFuture())
<span class="bg-green-pale text-green-brand text-xs font-semibold px-3 py-1 rounded-full border border-green-200"><i class="fas fa-check-circle mr-1"></i>Tersedia</span>
@endif
@if($package->package_code)<span class="text-gray-400 text-xs">Kode: {{ $package->package_code }}</span>@endif
</div>
<h1 class="font-playfair text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $package->package_name }}</h1>
@if($package->outlet)<p class="text-gray-500 text-sm"><i class="fas fa-building text-green-brand mr-1"></i>{{ $package->outlet->nama_outlet }}@if($package->outlet->kota)  {{ $package->outlet->kota }}@endif</p>@endif
</div>

{{-- Stats Bar --}}
<div class="stats-bar p-4">
<div class="grid grid-cols-2 sm:grid-cols-4 gap-0">
<div class="stats-item px-4 py-3">
<div class="flex items-center gap-2 mb-1"><i class="fas fa-clock text-green-brand text-xs"></i><span class="font-bold text-gray-800 text-sm">Durasi</span></div>
<p class="text-gray-700 text-sm font-semibold">{{ $package->duration_days ? $package->duration_days.' Hari' : '-' }}</p>
</div>
<div class="stats-item px-4 py-3">
<div class="flex items-center gap-2 mb-1"><i class="fas fa-calendar text-green-brand text-xs"></i><span class="font-bold text-gray-800 text-sm">Keberangkatan</span></div>
<p class="text-gray-700 text-sm font-semibold">{{ $package->departure_date ? \Carbon\Carbon::parse($package->departure_date)->format('d M Y') : '-' }}</p>
</div>
<div class="stats-item px-4 py-3">
<div class="flex items-center gap-2 mb-1"><i class="fas fa-users text-green-brand text-xs"></i><span class="font-bold text-gray-800 text-sm">Sisa Seat</span></div>
@php
    $availableSeats = $package->getAvailableSeats();
    $capacityPercentage = ($availableSeats / max($package->capacity, 1)) * 100;
    $colorClass = $capacityPercentage <= 20 ? 'text-red-600' : ($capacityPercentage <= 50 ? 'text-orange-600' : 'text-green-600');
@endphp
<p class="text-sm font-semibold {{ $colorClass }}">{{ $availableSeats }} Orang</p>
</div>
<div class="px-4 py-3">
<div class="flex items-center gap-2 mb-1"><i class="fas fa-user-tie text-green-brand text-xs"></i><span class="font-bold text-gray-800 text-sm">Ustadz</span></div>
<p class="text-gray-700 text-sm font-semibold">{{ $package->ustadz_name ?? '-' }}</p>
</div>
</div>
</div>

{{-- Penerbangan Info --}}
@if($package->flightDeparture || $package->flightReturn || $package->hotelMakkah || $package->hotelMadinah)
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
<h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2"><i class="fas fa-plane text-green-brand"></i> Info Penerbangan & Hotel</h2>
<div class="grid sm:grid-cols-2 gap-4">
@if($package->flightDeparture)
<div class="bg-blue-50 rounded-xl p-4">
<div class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-2"> Penerbangan Berangkat</div>
<div class="font-bold text-gray-900">{{ $package->flightDeparture->airline_name }}</div>
<div class="text-xs text-gray-600 mt-1">{{ $package->flightDeparture->departure_airport }}  {{ $package->flightDeparture->arrival_airport }}</div>
@if($package->departure_datetime)<div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($package->departure_datetime)->format('d M Y H:i') }}</div>@endif
</div>
@endif
@if($package->flightReturn)
<div class="bg-blue-50 rounded-xl p-4">
<div class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-2"> Penerbangan Pulang</div>
<div class="font-bold text-gray-900">{{ $package->flightReturn->airline_name }}</div>
<div class="text-xs text-gray-600 mt-1">{{ $package->flightReturn->departure_airport }}  {{ $package->flightReturn->arrival_airport }}</div>
@if($package->return_datetime)<div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($package->return_datetime)->format('d M Y H:i') }}</div>@endif
</div>
@endif
@if($package->hotelMakkah)
<div class="bg-green-pale rounded-xl p-4">
<div class="text-xs text-green-brand font-bold uppercase tracking-wider mb-2"> Hotel Makkah</div>
<div class="font-bold text-gray-900">{{ $package->hotelMakkah->hotel_name ?? $package->hotelMakkah->name ?? '-' }}</div>
@if($package->makkah_check_in && $package->makkah_check_out)
<div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($package->makkah_check_in)->format('d M') }}  {{ \Carbon\Carbon::parse($package->makkah_check_out)->format('d M Y') }}</div>
@endif
</div>
@endif
@if($package->hotelMadinah)
<div class="bg-green-pale rounded-xl p-4">
<div class="text-xs text-green-brand font-bold uppercase tracking-wider mb-2"> Hotel Madinah</div>
<div class="font-bold text-gray-900">{{ $package->hotelMadinah->hotel_name ?? $package->hotelMadinah->name ?? '-' }}</div>
@if($package->madinah_check_in && $package->madinah_check_out)
<div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($package->madinah_check_in)->format('d M') }}  {{ \Carbon\Carbon::parse($package->madinah_check_out)->format('d M Y') }}</div>
@endif
</div>
@endif
</div>
</div>
@endif

{{-- Deskripsi --}}
@if($package->description)
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
<h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-green-brand"></i> Deskripsi Paket</h2>
<div class="text-gray-600 leading-relaxed text-sm">{!! nl2br(e($package->description)) !!}</div>
</div>
@endif

{{-- Inclusions --}}
@if($package->inclusions)
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
<h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2"><i class="fas fa-check-circle text-green-brand"></i> Sudah Termasuk</h2>
<ul class="space-y-2">
@foreach($package->getInclusionsArray() as $item)
<li class="flex items-start gap-2 text-gray-700 text-sm"><span class="text-green-brand font-bold mt-0.5">✓</span>{{ $item }}</li>
@endforeach
</ul>
</div>
@endif

{{-- Tour Plan --}}
@if($package->tourPlans && $package->tourPlans->count() > 0)
<div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
<h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-green-brand"></i> Rencana Perjalanan</h2>
<div class="space-y-4">
@foreach($package->tourPlans as $day)
<div class="border border-gray-200 rounded-xl overflow-hidden">
<div class="bg-gradient-to-r from-green-pale to-green-100 px-4 py-3 border-b border-green-200 cursor-pointer hover:bg-green-100 transition-colors" onclick="toggleTourDay({{ $day->day_number }})">
<div class="flex items-center gap-3">
<span class="inline-flex items-center justify-center w-10 h-10 bg-green-brand text-white rounded-full text-sm font-bold">{{ $day->day_number }}</span>
<div class="flex-1">
<h3 class="font-bold text-gray-900">{{ $day->day_title }}</h3>
<p class="text-xs text-gray-600 mt-0.5"><i class="fas fa-calendar text-green-brand mr-1"></i>{{ \Carbon\Carbon::parse($day->day_date)->format('d F Y') }}</p>
</div>
<i id="toggle-icon-{{ $day->day_number }}" class="fas fa-chevron-{{ $day->day_number === 1 ? 'up' : 'down' }} text-green-brand transition-transform"></i>
</div>
@if($day->description)
<p class="text-sm text-gray-700 mt-2">{{ $day->description }}</p>
@endif
</div>
@if($day->activities && $day->activities->count() > 0)
<div id="tour-day-{{ $day->day_number }}" class="p-4 space-y-3 transition-all duration-300" style="{{ $day->day_number === 1 ? '' : 'display: none;' }}">
@foreach($day->activities as $activity)
<div class="flex gap-3 items-start">
<div class="flex-shrink-0 w-14 text-center">
<span class="inline-block px-2 py-1 bg-green-100 text-green-brand rounded-lg text-xs font-bold">{{ \Carbon\Carbon::parse($activity->activity_time)->format('H:i') }}</span>
</div>
<div class="flex-1">
<h4 class="font-semibold text-gray-900 text-sm">{{ $activity->activity_title }}</h4>
@if($activity->activity_description)
<p class="text-xs text-gray-600 mt-1">{{ $activity->activity_description }}</p>
@endif
</div>
</div>
@endforeach
</div>
@endif
</div>
@endforeach
</div>
</div>
@endif

{{-- Paket Terkait --}}
@if($relatedPackages->count()>0)
<div>
<h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2"><i class="fas fa-th-large text-green-brand"></i> Paket Terkait</h2>
<div class="grid sm:grid-cols-3 gap-4">
@foreach($relatedPackages as $rel)
<a href="{{ route('public.paket.show',$rel->id) }}" class="card-hover bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm group block">
<div class="h-32 overflow-hidden bg-green-pale">
@if($rel->image_path)
<img src="{{ asset('storage/'.$rel->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
@else
<div class="w-full h-full flex items-center justify-center"><i class="fas fa-kaaba text-green-300 text-3xl"></i></div>
@endif
</div>
<div class="p-3">
<p class="font-semibold text-gray-900 text-sm line-clamp-2">{{ $rel->package_name }}</p>
@if($rel->price)<p class="text-green-brand font-bold text-xs mt-1">Rp {{ number_format($rel->price,0,',','.') }}</p>@endif
</div>
</a>
@endforeach
</div>
</div>
@endif

</div>{{-- end left --}}

{{-- RIGHT: Form Pemesanan --}}
<div class="lg:col-span-1">
<div class="sticky-form space-y-4">

{{-- Harga & Form --}}
<div class="bg-white rounded-2xl border border-green-200 shadow-lg p-6">

{{-- Pilih Paket Harga --}}
@if(count($pricePackages)>0)
<div class="mb-5">
<p class="text-xs font-bold text-gray-700 mb-3 uppercase tracking-wider">Pilih Paket Harga</p>
<div class="space-y-2" id="pkg-list">
@foreach($pricePackages as $pi=>$pkg)
<div class="price-pkg-btn {{ $pi===0?'selected':'' }}"
     data-pkg="{{ $pi }}"
     data-name="{{ $pkg['name'] ?? 'Paket '.($pi+1) }}"
     onclick="selectPkg(this)">
<div class="flex items-center justify-between">
<span class="font-bold text-gray-900 text-sm">{{ $pkg['name'] ?? 'Paket '.($pi+1) }}</span>
@if(!empty($pkg['variants']))
<span class="text-xs text-gray-400">{{ count($pkg['variants']) }} varian</span>
@endif
</div>
@if(!empty($pkg['variants']))
<div class="flex flex-wrap gap-2 mt-2 variant-group" id="variants-{{ $pi }}" style="{{ $pi===0?'':'display:none' }}">
@foreach($pkg['variants'] as $vi=>$v)
<button type="button"
        class="variant-btn {{ $vi===0?'selected':'' }}"
        data-type="{{ $v['type'] ?? '' }}"
        data-price="{{ $v['price'] ?? 0 }}"
        onclick="selectVariant(this,event)">
{{ $v['type'] ?? '-' }}
@if(!empty($v['price']))
<span class="block text-green-brand text-xs font-bold">Rp {{ number_format($v['price'],0,',','.') }}</span>
@endif
</button>
@endforeach
</div>
@endif
</div>
@endforeach
</div>
</div>
@else
{{-- Tidak ada price_packages: tampilkan harga tunggal dari field price --}}
@if($package->price)
<div class="mb-5 p-3 bg-green-pale rounded-xl border border-green-200">
<p class="text-xs text-green-brand font-bold">Harga Paket</p>
<p class="text-2xl font-black text-green-brand mt-1">Rp {{ number_format($package->price,0,',','.') }}</p>
<p class="text-xs text-gray-500 mt-0.5">per orang</p>
</div>
@endif
@endif

{{-- Harga Terpilih --}}
<div class="text-center mb-5 p-4 bg-green-pale rounded-xl">
<div class="text-xs text-gray-500 mb-1">Harga per orang</div>
<div id="selected-price-display" class="text-3xl font-black text-green-brand">
@php
  $initPrice = 0;
  if(count($pricePackages)>0 && !empty($pricePackages[0]['variants'][0]['price'])) {
    $initPrice = $pricePackages[0]['variants'][0]['price'];
  } elseif($package->price) {
    $initPrice = $package->price;
  }
@endphp
@if($initPrice > 0)
Rp {{ number_format($initPrice,0,',','.') }}
@else
Hubungi Kami
@endif
</div>
<div id="selected-pkg-label" class="text-xs text-gray-500 mt-1">
@if(count($pricePackages)>0)
{{ $pricePackages[0]['name'] ?? '' }}
@if(!empty($pricePackages[0]['variants'][0]['type']))
&mdash; {{ $pricePackages[0]['variants'][0]['type'] }}
@endif
@endif
</div>
</div>

{{-- Form --}}
<form action="{{ route('public.booking.submit') }}" method="POST" id="order-form" onsubmit="return prepareFormSubmit(event)">
@csrf
<input type="hidden" name="package_id" id="f_package_id" value="{{ $package->id }}">
<input type="hidden" name="jamaah_name" id="f_jamaah_name" value="">
<input type="hidden" name="jamaah_phone" id="f_jamaah_phone" value="">
<input type="hidden" name="jamaah_email" id="f_jamaah_email" value="">
<input type="hidden" name="room_type" id="f_room_type" value="">
<input type="hidden" name="total_price" id="f_total_price" value="0">
<input type="hidden" name="equipment" id="f_equipment" value="[]">
<input type="hidden" name="price_package_name" id="f_pkg_name" value="{{ count($pricePackages)>0 ? ($pricePackages[0]['name'] ?? '') : '' }}">
<input type="hidden" name="price_variant" id="f_variant" value="{{ count($pricePackages)>0 ? ($pricePackages[0]['variants'][0]['type'] ?? '') : '' }}">
<input type="hidden" name="selected_price" id="f_price" value="{{ count($pricePackages)>0 ? ($pricePackages[0]['variants'][0]['price'] ?? $package->price ?? 0) : ($package->price ?? 0) }}">

<div class="space-y-3">
<div>
<label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap *</label>
<input type="text" name="nama" id="f_nama" required placeholder="Nama lengkap Anda"
       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-green-brand focus:ring-1 focus:ring-green-brand">
</div>
<div>
<label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp *</label>
<input type="tel" name="telepon" id="f_telepon" required placeholder="08xxxxxxxxxx"
       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-green-brand focus:ring-1 focus:ring-green-brand">
</div>
<div>
<label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
<input type="email" name="email" placeholder="email@contoh.com"
       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-green-brand focus:ring-1 focus:ring-green-brand">
</div>

{{-- Pilih Keberangkatan (opsional) --}}
@if($keberangkatanList->count()>0)
<div>
<label class="block text-xs font-bold text-gray-700 mb-1">Pilih Keberangkatan <span class="text-gray-400 font-normal">(opsional)</span></label>
<select name="id_keberangkatan"
        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-green-brand appearance-none">
<option value="">-- Pilih jadwal keberangkatan --</option>
@foreach($keberangkatanList as $kb)
<option value="{{ $kb->id }}">
{{ \Carbon\Carbon::parse($kb->departure_date)->format('d M Y') }}
{{ $kb->keberangkatan_name ? '— '.$kb->keberangkatan_name : '' }}
(Sisa: {{ $kb->getAvailableCapacity() }} kursi)
</option>
@endforeach
</select>
</div>
@endif

{{-- Anggota Keluarga --}}
<div>
<div class="flex items-center justify-between mb-2">
<label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Anggota Keluarga</label>
<button type="button" onclick="addFamilyRow()"
        class="text-xs bg-green-pale text-green-brand font-bold px-3 py-1.5 rounded-full hover:bg-green-100 transition-all flex items-center gap-1">
<i class="fas fa-plus text-xs"></i> Tambah
</button>
</div>
<div id="family-rows" class="space-y-2"></div>
<p class="text-xs text-gray-400 mt-1">Isi tanggal lahir untuk kalkulasi harga usia (infant/anak)</p>
</div>

{{-- Tambah Perlengkapan --}}
<div>
<div class="flex items-center justify-between mb-2">
<label class="text-xs font-bold text-gray-700 uppercase tracking-wider">Perlengkapan</label>
<button type="button" onclick="openEquipmentModal()"
        class="text-xs bg-green-pale text-green-brand font-bold px-3 py-1.5 rounded-full hover:bg-green-100 transition-all flex items-center gap-1">
<i class="fas fa-shopping-bag text-xs"></i> Tambah Perlengkapan
</button>
</div>
<div id="selected-equipment-list" class="space-y-2" style="display:none;"></div>
<p class="text-xs text-gray-400 mt-1">Tambahkan perlengkapan umrah/haji sesuai kebutuhan</p>
</div>

<div>
<label class="block text-xs font-bold text-gray-700 mb-2">Opsi Pembayaran *</label>
<div class="grid grid-cols-2 gap-2" id="payment-options">
<label class="payment-opt-btn cursor-pointer">
<input type="radio" name="payment_type" value="full" class="sr-only" onchange="updatePaymentDisplay()">
<div class="payment-opt-card border-2 border-gray-200 rounded-xl p-3 text-center transition-all hover:border-green-brand">
<div class="text-xs font-bold text-gray-700">Bayar Penuh</div>
<div class="text-xs text-gray-400 mt-0.5">Lunas sekarang</div>
</div>
</label>
<label class="payment-opt-btn cursor-pointer">
<input type="radio" name="payment_type" value="dp" class="sr-only" checked onchange="updatePaymentDisplay()">
<div class="payment-opt-card border-2 border-green-brand bg-green-pale rounded-xl p-3 text-center transition-all">
<div class="text-xs font-bold text-green-brand">Bayar DP</div>
<div class="text-xs text-green-600 mt-0.5">Nominal akan dibahas via WA</div>
</div>
</label>
</div>

<!-- Hidden input for dp_option, default to 10 million -->
<input type="hidden" name="dp_option" value="10_million">

<div id="payment-amount-info" class="mt-2 p-2 bg-yellow-50 rounded-lg border border-yellow-200 text-xs text-yellow-800 hidden">
<i class="fas fa-info-circle mr-1"></i>
<span id="payment-amount-text"></span>
</div>
</div>

<div>
<label class="block text-xs font-bold text-gray-700 mb-1">Catatan</label>
<textarea name="catatan" rows="2" placeholder="Pertanyaan atau permintaan khusus..."
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-green-brand resize-none"></textarea>
</div>
</div>

{{-- Preview Rincian Harga --}}
<div id="price-preview" class="mt-4 border border-green-100 rounded-xl overflow-hidden hidden">
<div class="bg-green-pale px-4 py-2 text-xs font-bold text-green-brand uppercase tracking-wider">Rincian Harga</div>
<div id="price-rows" class="divide-y divide-gray-50"></div>
<div class="flex items-center justify-between px-4 py-3 bg-green-50">
<span class="font-bold text-gray-900 text-sm">Total</span>
<span id="total-display" class="font-black text-green-brand text-xl">-</span>
</div>
</div>

<button type="submit"
        class="w-full mt-4 bg-green-gradient text-white font-bold py-3.5 rounded-xl text-sm hover:opacity-90 transition-all shadow-lg shadow-green-200 flex items-center justify-center gap-2">
<i class="fab fa-whatsapp text-lg"></i> Pesan dan Konfirmasi
</button>
</form>

<p class="text-center text-gray-400 text-xs mt-3"><i class="fas fa-lock mr-1"></i>Data Anda aman & terlindungi</p>
</div>

{{-- Kontak --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<p class="text-xs font-bold text-gray-700 mb-3">Butuh bantuan?</p>
<a href="https://wa.me/628976688800" target="_blank" class="flex items-center gap-3 p-3 bg-green-pale rounded-xl hover:bg-green-100 transition-all mb-2">
<i class="fab fa-whatsapp text-green-500 text-xl"></i>
<div><div class="text-xs font-bold text-gray-800">WhatsApp</div><div class="text-xs text-gray-500">+62 897-668-8800</div></div>
</a>
</div>

</div>
</div>{{-- end right --}}

</div>{{-- end grid --}}
</div>{{-- end max-w --}}
</div>{{-- end pt-20 --}}

<footer class="bg-green-brand text-white py-8 mt-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
<img src="{{ url('WEB_HMTour/wp-content/uploads/2025/10/Logo-HM_UMRAH-3-WHITE.png') }}" alt="HM Tour" class="h-10 w-auto object-contain" onerror="this.style.display='none'">
<p class="text-green-200 text-xs">&copy; {{ date('Y') }} HM Tour & Travel. Berizin Kemenag RI.</p>
<a href="/" class="text-green-200 hover:text-white text-xs"> Kembali ke Beranda</a>
</div>
</footer>

<a href="https://wa.me/628976688800" target="_blank" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/40 transition-all">
<i class="fab fa-whatsapp text-white text-2xl"></i>
</a>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-90 flex items-center justify-center p-4" onclick="closeImageModal()">
<div class="relative max-w-7xl max-h-full">
<button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-bold">&times;</button>
<img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
</div>
</div>

{{-- Equipment Modal --}}
<div id="equipmentModal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-50 flex items-center justify-center p-4" onclick="closeEquipmentModal(event)">
<div class="relative bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
<div class="bg-green-gradient text-white px-6 py-4 flex items-center justify-between">
<h3 class="text-lg font-bold">Pilih Perlengkapan</h3>
<button onclick="closeEquipmentModal()" class="text-white hover:text-gray-200 text-2xl font-bold">&times;</button>
</div>
<div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
<!-- Search -->
<input type="text" id="equipmentSearch" 
       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-brand mb-4" 
       placeholder="Cari perlengkapan..." onkeyup="searchEquipment()">

<!-- Loading -->
<div id="equipmentLoading" class="text-center py-8">
<i class="fas fa-spinner fa-spin text-green-brand text-2xl"></i>
<p class="text-gray-500 text-sm mt-2">Memuat produk...</p>
</div>

<!-- Table Produk -->
<div id="equipmentTable" class="hidden">
<table class="w-full">
<thead class="bg-gray-50">
<tr>
<th class="px-4 py-3 text-left text-xs font-bold text-gray-700">Produk</th>
<th class="px-4 py-3 text-right text-xs font-bold text-gray-700">Harga</th>
<th class="px-4 py-3 text-center text-xs font-bold text-gray-700">Stok</th>
<th class="px-4 py-3 text-center text-xs font-bold text-gray-700">Qty</th>
<th class="px-4 py-3 text-center text-xs font-bold text-gray-700">Aksi</th>
</tr>
</thead>
<tbody id="equipmentTableBody" class="divide-y divide-gray-100">
<!-- Loaded via AJAX -->
</tbody>
</table>
</div>

<!-- Empty State -->
<div id="equipmentEmpty" class="hidden text-center py-8">
<i class="fas fa-box-open text-gray-300 text-4xl mb-2"></i>
<p class="text-gray-500 text-sm">Tidak ada produk ditemukan</p>
</div>
</div>
<div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
<div class="text-sm text-gray-600">
<span id="selectedEquipmentCount">0</span> item dipilih
</div>
<button type="button" onclick="saveEquipment()" 
        class="bg-green-gradient text-white font-bold px-6 py-2.5 rounded-xl text-sm hover:opacity-90 transition-all">
<i class="fas fa-check mr-2"></i>
Simpan
</button>
</div>
</div>
</div>

<script>
// ===== Gallery =====
function switchImg(src,el){
  document.getElementById('gallery-main-img').src=src;
  document.querySelectorAll('.gallery-thumb').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  // Update main gallery click handler
  document.querySelector('.gallery-main').setAttribute('onclick', `openImageModal('${src}')`);
}

// ===== Image Modal =====
function openImageModal(src) {
  if (!src) return;
  document.getElementById('modalImage').src = src;
  document.getElementById('imageModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeImageModal() {
  document.getElementById('imageModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeImageModal();
  }
});

// ===== Tour Plan Collapsible =====
function toggleTourDay(dayNumber) {
  const content = document.getElementById('tour-day-' + dayNumber);
  const icon = document.getElementById('toggle-icon-' + dayNumber);
  
  if (content.style.display === 'none') {
    content.style.display = 'block';
    icon.classList.remove('fa-chevron-down');
    icon.classList.add('fa-chevron-up');
  } else {
    content.style.display = 'none';
    icon.classList.remove('fa-chevron-up');
    icon.classList.add('fa-chevron-down');
  }
}

// ===== Price data dari Blade =====
var pricePackagesData = @json($pricePackages);
var currentPrice = parseFloat(document.getElementById('f_price').value)||0;
var familyRowCount = 0;

// ===== Price Package Selection =====
function selectPkg(el){
  document.querySelectorAll('.price-pkg-btn').forEach(b=>b.classList.remove('selected'));
  el.classList.add('selected');
  var pi = el.dataset.pkg;
  document.getElementById('f_pkg_name').value = el.dataset.name;
  document.querySelectorAll('.variant-group').forEach(g=>g.style.display='none');
  var vg = document.getElementById('variants-'+pi);
  if(vg){ vg.style.display='flex'; }
  var firstV = el.querySelector('.variant-btn');
  if(firstV){ selectVariant(firstV,{stopPropagation:function(){}}); }
}

function selectVariant(el,e){
  if(e && e.stopPropagation) e.stopPropagation();
  var parent = el.closest('.variant-group');
  if(parent) parent.querySelectorAll('.variant-btn').forEach(b=>b.classList.remove('selected'));
  el.classList.add('selected');
  var price = parseFloat(el.dataset.price)||0;
  var type  = el.dataset.type||'';
  document.getElementById('f_variant').value = type;
  document.getElementById('f_price').value   = price;
  currentPrice = price;
  if(price>0){
    document.getElementById('selected-price-display').textContent = 'Rp '+price.toLocaleString('id-ID');
  }
  var pkgName = document.getElementById('f_pkg_name').value;
  document.getElementById('selected-pkg-label').textContent = pkgName + (type?' — '+type:'');
  updatePricePreview();
}

// ===== Anggota Keluarga =====
function addFamilyRow(){
  var idx = familyRowCount++;
  var row = document.createElement('div');
  row.className = 'family-row bg-gray-50 rounded-xl p-3 border border-gray-100';
  row.id = 'family-row-'+idx;
  row.innerHTML =
    '<div class="flex items-center gap-2 mb-2">' +
      '<span class="text-xs font-bold text-gray-600">Anggota '+(idx+1)+'</span>' +
      '<button type="button" onclick="removeFamilyRow('+idx+')" class="ml-auto text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>' +
    '</div>' +
    '<div class="grid grid-cols-2 gap-2">' +
      '<div>' +
        '<label class="text-xs text-gray-500 mb-0.5 block">Nama *</label>' +
        '<input type="text" name="family_members['+idx+'][nama]" required placeholder="Nama lengkap"' +
               ' class="w-full border border-gray-200 rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-green-brand">' +
      '</div>' +
      '<div>' +
        '<label class="text-xs text-gray-500 mb-0.5 block">Hubungan</label>' +
        '<select name="family_members['+idx+'][hubungan]" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-green-brand appearance-none">' +
          '<option value="">Pilih</option>' +
          '<option value="Suami">Suami</option>' +
          '<option value="Istri">Istri</option>' +
          '<option value="Anak">Anak</option>' +
          '<option value="Orang Tua">Orang Tua</option>' +
          '<option value="Saudara">Saudara</option>' +
          '<option value="Lainnya">Lainnya</option>' +
        '</select>' +
      '</div>' +
      '<div class="col-span-2">' +
        '<label class="text-xs text-gray-500 mb-0.5 block">Tanggal Lahir <span class="text-gray-400">(untuk kalkulasi harga usia)</span></label>' +
        '<input type="date" name="family_members['+idx+'][tanggal_lahir]"' +
               ' onchange="updatePricePreview()"' +
               ' class="w-full border border-gray-200 rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-green-brand">' +
      '</div>' +
    '</div>' +
    '<div class="age-info-'+idx+' mt-1 text-xs text-green-700 font-semibold hidden"></div>';
  document.getElementById('family-rows').appendChild(row);
  updatePricePreview();
}

function removeFamilyRow(idx){
  var row = document.getElementById('family-row-'+idx);
  if(row) row.remove();
  updatePricePreview();
}

// ===== Kalkulasi Usia =====
function calcAge(dob){
  if(!dob) return null;
  var today = new Date();
  var birth = new Date(dob);
  var age = today.getFullYear() - birth.getFullYear();
  var m = today.getMonth() - birth.getMonth();
  if(m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
  return age;
}

function getAgeCategory(age){
  if(age === null) return {label:'Dewasa', price: currentPrice, badge:'', color:'text-gray-600'};
  if(age < 2)  return {label:'Infant (<2th)', price: 18000000, badge:'Flat Rp 18jt', color:'text-blue-600'};
  if(age <= 8) return {label:'Anak ('+age+'th)', price: Math.round(currentPrice*0.85), badge:'Diskon 15%', color:'text-orange-600'};
  return {label:'Dewasa ('+age+'th)', price: currentPrice, badge:'', color:'text-gray-600'};
}

// ===== Update Preview Harga =====
function updatePricePreview(){
  if(currentPrice <= 0){ document.getElementById('price-preview').classList.add('hidden'); return; }

  var rows = [];
  var total = 0;

  // Jamaah utama
  rows.push({label:'Jamaah Utama (Dewasa)', amount: currentPrice});
  total += currentPrice;

  // Anggota keluarga
  document.querySelectorAll('.family-row').forEach(function(row){
    var namaEl = row.querySelector('input[name*="[nama]"]');
    var dobEl  = row.querySelector('input[name*="[tanggal_lahir]"]');
    var nama   = namaEl ? namaEl.value.trim() : '';
    var dob    = dobEl  ? dobEl.value : '';
    if(!nama) return;
    var age = calcAge(dob);
    var cat = getAgeCategory(age);
    var label = (nama||'Anggota') + ' — ' + cat.label;
    if(cat.badge) label += ' ('+cat.badge+')';
    rows.push({label: label, amount: cat.price, color: cat.color});
    total += cat.price;
  });

  // Render rows
  var html = '';
  rows.forEach(function(r){
    html += '<div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-50 last:border-0">' +
              '<span class="text-xs '+(r.color||'text-gray-700')+'">'+r.label+'</span>' +
              '<span class="text-xs font-semibold text-gray-900">Rp '+r.amount.toLocaleString('id-ID')+'</span>' +
            '</div>';
  });
  document.getElementById('price-rows').innerHTML = html;
  document.getElementById('total-display').textContent = 'Rp '+total.toLocaleString('id-ID');
  document.getElementById('price-preview').classList.remove('hidden');
}

// Init on load
document.addEventListener('DOMContentLoaded', function(){
  updatePricePreview();
  // Auto-select first pkg/variant if exists
  var firstPkg = document.querySelector('.price-pkg-btn');
  if(firstPkg && currentPrice <= 0){
    var firstVariant = firstPkg.querySelector('.variant-btn');
    if(firstVariant){
      currentPrice = parseFloat(firstVariant.dataset.price)||0;
      document.getElementById('f_price').value = currentPrice;
      updatePricePreview();
    }
  }
  // Init payment type
  updatePaymentDisplay();
});

// ===== Payment Type =====
function updatePaymentDisplay(){
  var radios = document.querySelectorAll('input[name="payment_type"]');
  var selected = 'dp';
  radios.forEach(function(r){ if(r.checked) selected = r.value; });

  // Update card styles
  radios.forEach(function(r){
    var card = r.nextElementSibling;
    if(r.checked){
      card.style.borderColor = '#2E7D32';
      card.style.background  = '#E8F5E9';
    } else {
      card.style.borderColor = '#e5e7eb';
      card.style.background  = '#fff';
    }
  });

  // Show/hide DP options
  // Payment type selection - simplified (no DP amount display)
  // DP amount will be discussed via WhatsApp
  
  // Show info - only show total price
  var infoEl = document.getElementById('payment-amount-info');
  var textEl = document.getElementById('payment-amount-text');
  if(currentPrice > 0){
    if(selected === 'full'){
      textEl.textContent = 'Total yang harus dibayar: Rp ' + currentPrice.toLocaleString('id-ID');
      infoEl.classList.remove('hidden');
    } else {
      textEl.textContent = 'Total harga paket: Rp ' + currentPrice.toLocaleString('id-ID') + ' (Nominal DP akan dibahas via WhatsApp)';
      infoEl.classList.remove('hidden');
    }
  } else {
    infoEl.classList.add('hidden');
  }
}

// ===== EQUIPMENT MANAGEMENT =====
var selectedEquipment = [];
var allProducts = [];

// Open equipment modal
function openEquipmentModal() {
  document.getElementById('equipmentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  loadProducts();
}

// Close equipment modal
function closeEquipmentModal(event) {
  if (event && event.target.id !== 'equipmentModal') return;
  document.getElementById('equipmentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

// Load products from API
function loadProducts() {
  document.getElementById('equipmentLoading').classList.remove('hidden');
  document.getElementById('equipmentTable').classList.add('hidden');
  document.getElementById('equipmentEmpty').classList.add('hidden');

  const baseUrl = '{{ url('/') }}';
  fetch(baseUrl + '/api/products')
    .then(response => response.json())
    .then(data => {
      allProducts = data;
      renderProducts(data);
      document.getElementById('equipmentLoading').classList.add('hidden');
      if (data.length > 0) {
        document.getElementById('equipmentTable').classList.remove('hidden');
      } else {
        document.getElementById('equipmentEmpty').classList.remove('hidden');
      }
    })
    .catch(error => {
      console.error('Error loading products:', error);
      document.getElementById('equipmentLoading').classList.add('hidden');
      document.getElementById('equipmentEmpty').classList.remove('hidden');
    });
}

// Render products table
function renderProducts(products) {
  var tbody = document.getElementById('equipmentTableBody');
  tbody.innerHTML = '';

  products.forEach(function(product) {
    var existing = selectedEquipment.find(e => e.id == product.id_produk);
    var qty = existing ? existing.qty : 0;

    var row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.innerHTML = 
      '<td class="px-4 py-3 text-sm text-gray-900">' + product.nama_produk + '</td>' +
      '<td class="px-4 py-3 text-sm text-gray-900 text-right">Rp ' + parseFloat(product.harga_jual).toLocaleString('id-ID') + '</td>' +
      '<td class="px-4 py-3 text-sm text-gray-600 text-center">' + product.stok + '</td>' +
      '<td class="px-4 py-3 text-center">' +
        '<input type="number" id="qty-' + product.id_produk + '" value="' + qty + '" min="0" max="' + product.stok + '" ' +
        'class="w-20 border border-gray-200 rounded-lg px-2 py-1 text-sm text-center focus:outline-none focus:border-green-brand">' +
      '</td>' +
      '<td class="px-4 py-3 text-center">' +
        '<button type="button" onclick="addEquipment(' + product.id_produk + ', \'' + product.nama_produk.replace(/'/g, "\\'") + '\', ' + product.harga_jual + ')" ' +
        'class="bg-green-pale text-green-brand font-semibold px-3 py-1 rounded-lg text-xs hover:bg-green-100 transition-all">' +
        '<i class="fas fa-plus mr-1"></i>Tambah' +
        '</button>' +
      '</td>';
    tbody.appendChild(row);
  });
}

// Search equipment
function searchEquipment() {
  var search = document.getElementById('equipmentSearch').value.toLowerCase();
  var filtered = allProducts.filter(function(p) {
    return p.nama_produk.toLowerCase().includes(search);
  });
  renderProducts(filtered);
}

// Add equipment to selection
function addEquipment(id, name, price) {
  var qtyInput = document.getElementById('qty-' + id);
  var qty = parseInt(qtyInput.value) || 0;

  if (qty <= 0) {
    alert('Masukkan jumlah yang valid');
    return;
  }

  var existing = selectedEquipment.find(e => e.id == id);
  if (existing) {
    existing.qty = qty;
    existing.subtotal = qty * price;
  } else {
    selectedEquipment.push({
      id: id,
      name: name,
      price: price,
      qty: qty,
      subtotal: qty * price
    });
  }

  updateSelectedEquipmentCount();
  alert('Ditambahkan: ' + name + ' x' + qty);
}

// Update selected equipment count
function updateSelectedEquipmentCount() {
  var count = selectedEquipment.reduce((sum, e) => sum + e.qty, 0);
  document.getElementById('selectedEquipmentCount').textContent = count;
}

// Save equipment and close modal
function saveEquipment() {
  // Remove items with qty = 0
  selectedEquipment = selectedEquipment.filter(e => e.qty > 0);
  
  // Update display
  renderSelectedEquipment();
  updatePricePreviewWithEquipment();
  
  // Close modal
  closeEquipmentModal();
}

// Render selected equipment list
function renderSelectedEquipment() {
  var container = document.getElementById('selected-equipment-list');
  
  if (selectedEquipment.length === 0) {
    container.style.display = 'none';
    return;
  }

  container.style.display = 'block';
  container.innerHTML = '';

  selectedEquipment.forEach(function(eq, index) {
    var item = document.createElement('div');
    item.className = 'bg-green-pale rounded-lg p-3 border border-green-200';
    item.innerHTML =
      '<div class="flex items-center justify-between">' +
        '<div class="flex-1">' +
          '<div class="text-xs font-bold text-gray-900">' + eq.name + '</div>' +
          '<div class="text-xs text-gray-600 mt-0.5">Rp ' + eq.price.toLocaleString('id-ID') + ' x ' + eq.qty + ' = Rp ' + eq.subtotal.toLocaleString('id-ID') + '</div>' +
        '</div>' +
        '<button type="button" onclick="removeEquipment(' + index + ')" class="text-red-400 hover:text-red-600 ml-2">' +
          '<i class="fas fa-times"></i>' +
        '</button>' +
      '</div>';
    container.appendChild(item);
  });
}

// Remove equipment
function removeEquipment(index) {
  selectedEquipment.splice(index, 1);
  renderSelectedEquipment();
  updatePricePreviewWithEquipment();
}

// Update price preview with equipment
function updatePricePreviewWithEquipment() {
  if(currentPrice <= 0){ document.getElementById('price-preview').classList.add('hidden'); return; }

  var rows = [];
  var total = 0;

  // Jamaah utama
  rows.push({label:'Jamaah Utama (Dewasa)', amount: currentPrice});
  total += currentPrice;

  // Anggota keluarga
  document.querySelectorAll('.family-row').forEach(function(row){
    var namaEl = row.querySelector('input[name*="[nama]"]');
    var dobEl  = row.querySelector('input[name*="[tanggal_lahir]"]');
    var nama   = namaEl ? namaEl.value.trim() : '';
    var dob    = dobEl  ? dobEl.value : '';
    if(!nama) return;
    var age = calcAge(dob);
    var cat = getAgeCategory(age);
    var label = (nama||'Anggota') + ' — ' + cat.label;
    if(cat.badge) label += ' ('+cat.badge+')';
    rows.push({label: label, amount: cat.price, color: cat.color});
    total += cat.price;
  });

  // Equipment
  selectedEquipment.forEach(function(eq) {
    rows.push({
      label: eq.name + ' x' + eq.qty,
      amount: eq.subtotal,
      color: 'text-green-700'
    });
    total += eq.subtotal;
  });

  // Render rows
  var html = '';
  rows.forEach(function(r){
    html += '<div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-50 last:border-0">' +
              '<span class="text-xs '+(r.color||'text-gray-700')+'">'+r.label+'</span>' +
              '<span class="text-xs font-semibold text-gray-900">Rp '+r.amount.toLocaleString('id-ID')+'</span>' +
            '</div>';
  });
  document.getElementById('price-rows').innerHTML = html;
  document.getElementById('total-display').textContent = 'Rp '+total.toLocaleString('id-ID');
  document.getElementById('price-preview').classList.remove('hidden');
}

// Override updatePricePreview to use new function with equipment
updatePricePreview = updatePricePreviewWithEquipment;

// Prepare form submit
function prepareFormSubmit(event) {
  // Prevent default form submission
  event.preventDefault();
  
  // Get form values
  var nama = document.getElementById('f_nama').value;
  var telepon = document.getElementById('f_telepon').value;
  var emailEl = document.querySelector('input[name="email"]');
  var email = emailEl ? emailEl.value : '';
  var roomType = document.getElementById('f_variant').value || 'double';
  var paymentType = document.querySelector('input[name="payment_type"]:checked').value;
  var dpOption = document.querySelector('input[name="dp_option"]') ? document.querySelector('input[name="dp_option"]').value : '10_million';

  // Validate required fields (email is optional)
  if (!nama || !telepon) {
    alert('Mohon lengkapi Nama dan Nomor Telepon');
    return false;
  }

  // Calculate total
  var total = 0;
  total += currentPrice; // Main jamaah

  // Family members
  var familyMembers = [];
  document.querySelectorAll('.family-row').forEach(function(row){
    var namaEl = row.querySelector('input[name*="[nama]"]');
    var dobEl = row.querySelector('input[name*="[tanggal_lahir]"]');
    var dob = dobEl ? dobEl.value : '';
    var age = calcAge(dob);
    var cat = getAgeCategory(age);
    total += cat.price;
    
    if (namaEl && namaEl.value) {
      familyMembers.push({
        nama: namaEl.value,
        tanggal_lahir: dob,
        kategori: cat.label
      });
    }
  });

  // Equipment
  var equipmentList = [];
  selectedEquipment.forEach(function(eq) {
    total += eq.subtotal;
    equipmentList.push({
      id: eq.id,           // Keep id for backend processing
      name: eq.name,       // Use 'name' to match booking-payment.blade.php
      price: eq.price,     // Use 'price' to match booking-payment.blade.php
      qty: eq.qty,
      subtotal: eq.subtotal
    });
  });

  // Set hidden fields BEFORE creating FormData
  document.getElementById('f_jamaah_name').value = nama;
  document.getElementById('f_jamaah_phone').value = telepon;
  document.getElementById('f_jamaah_email').value = email;
  document.getElementById('f_room_type').value = roomType;
  document.getElementById('f_total_price').value = total;
  document.getElementById('f_price').value = currentPrice;  // Fixed: use f_price instead of f_selected_price

  // Create FormData from form (now hidden fields are populated)
  var formData = new FormData(document.getElementById('order-form'));
  // Override with JSON strings for complex data
  formData.set('family_members', JSON.stringify(familyMembers));
  formData.set('equipment', JSON.stringify(equipmentList));

  // Get CSRF token
  var csrfToken = document.querySelector('input[name="_token"]');
  if (!csrfToken) {
    csrfToken = document.querySelector('meta[name="csrf-token"]');
  }
  var tokenValue = csrfToken ? (csrfToken.value || csrfToken.content) : '';
  
  var baseUrl = '{{ url('/') }}';

  fetch(baseUrl + '/booking/submit', {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': tokenValue
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (data.whatsapp_sent) {
        // WhatsApp sent successfully via OpenWA from admin to jamaah
        alert('Pemesanan berhasil! Notifikasi WhatsApp telah dikirim ke ' + telepon);
        window.location.href = data.redirect_url;
      } else if (data.fallback_url) {
        // OpenWA failed, use fallback wa.me (admin sends to jamaah manually)
        alert('Pemesanan berhasil! Silakan kirim pesan WhatsApp ke jamaah.');
        window.location.href = data.fallback_url;
      } else {
        // No WhatsApp, just redirect to invoice
        window.location.href = baseUrl + '/paket/' + {{ $package->id }} + '/invoice/' + data.booking_id;
      }
    } else {
      alert('Terjadi kesalahan: ' + (data.message || 'Silakan coba lagi'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
  });

  return false; // Prevent form submission
}

</script>
</body>
</html>
