{{-- Isian urutan & status, sama di ketiga modal Ngobrol Statistik. --}}
<div class="col-md-4">
    <label class="form-label">Urutan</label>
    <input type="number" name="urutan" min="0" max="9999" value="{{ old('urutan', 0) }}" class="form-control">
    <div class="form-text">{{ $catatan }}</div>
</div>
<div class="col-md-4">
    <label class="form-label">Status</label>
    <select name="tampil" class="form-select">
        <option value="1" @selected(old('tampil', '1') === '1')>Tampil</option>
        <option value="0" @selected(old('tampil') === '0')>Disembunyikan</option>
    </select>
</div>
