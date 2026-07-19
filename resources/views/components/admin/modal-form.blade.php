@props([
    'id',                 // id modal, mis. "modalKependudukan"
    'title' => 'Data',    // judul default (tombol Edit bisa menimpanya)
    'action',             // URL store; saat edit diisi ulang oleh JS
    'size' => '',         // '', 'modal-lg', 'modal-xl'
])

@php
    // Setelah validasi gagal Laravel redirect balik. Modal yang error dibuka
    // lagi lengkap dengan action & method aslinya supaya isian tak hilang.
    $reopen = old('_form_id') === $id;
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" @if ($reopen) data-modal-autoopen @endif>
    <div class="modal-dialog {{ $size }}">
        <form method="POST" action="{{ $reopen ? old('_form_action', $action) : $action }}" class="modal-content">
            @csrf
            <input type="hidden" name="_form_id" value="{{ $id }}">
            <input type="hidden" name="_form_action" value="{{ $action }}">
            <input type="hidden" name="_form_method" value="{{ $reopen ? old('_form_method') : '' }}">
            @if ($reopen && old('_form_method'))
                <input type="hidden" name="_method" value="{{ old('_form_method') }}">
            @endif

            <div class="modal-header">
                <h6 class="modal-title" data-modal-title>{{ $title }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
