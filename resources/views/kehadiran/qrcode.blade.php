@extends('layouts.home')
@section('title_page','QR Code Santri')
@section('content')

    <div class="text-center">
        <h4>{{ $santri->name }}</h4>
        <p>Tunjukkan QR ini kepada petugas untuk melakukan absensi.</p>

        <div style="display:inline-block; padding:10px; background:var(--bg-card); border:1px solid var(--border-color); border-radius:12px;" id="qrcode-container">
            @php
                $qrText = rawurlencode($santri->id);
                // Use QuickChart QR endpoint to avoid deprecated Google Charts API
                $qr300 = "https://quickchart.io/qr?text={$qrText}&size=300&ecLevel=H";
                $qr500 = "https://quickchart.io/qr?text={$qrText}&size=500&ecLevel=H";
            @endphp
            <img src="{{ $qr300 }}" alt="QR Code" id="qr-image">
        </div>

        <div class="mt-3">
            <a class="btn btn-primary" href="{{ $qr500 }}" download="{{ $santri->id }}-qrcode.png">Download QR</a>
        </div>
    </div>

@endsection
