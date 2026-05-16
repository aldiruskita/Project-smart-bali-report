@extends('layouts.app')
@section('title', 'Buat Laporan')
@section('content')
<div class="container" style="max-width:800px;">
    <h1 style="font-family:'Noto Serif',serif;font-size:1.75rem;font-weight:700;margin-bottom:24px;color:var(--primary);display:flex;align-items:center;gap:12px;"><span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">add_circle</span> Buat Laporan Baru</h1>

    @if($errors->any())
        <div class="alert alert-error">
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="glass-card" style="padding:32px;margin-bottom:24px;">
            <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:20px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">edit_note</span> Informasi Laporan</h2>

            <div class="form-group">
                <label class="form-label">Judul Laporan *</label>
                <input type="text" name="title" id="reportTitle" class="form-input" value="{{ old('title') }}" placeholder="Contoh: Jalan berlubang di Jl. Merdeka" required>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Detail *</label>
                <textarea name="description" id="reportDescription" class="form-textarea" placeholder="Jelaskan masalah secara detail..." required>{{ old('description') }}</textarea>
            </div>

            {{-- AI Auto-Detect Section --}}
            <div class="glass-card" style="padding:20px;margin-bottom:20px;background:rgba(99,102,241,0.08);border-color:rgba(99,102,241,0.3);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="material-symbols-rounded" style="font-size:24px;color:#818cf8;">smart_toy</span>
                        <div>
                            <div style="font-weight:700;font-size:14px;">AI Smart Classification</div>
                            <div style="font-size:12px;color:#94a3b8;">Biarkan AI mendeteksi kategori otomatis dari deskripsi</div>
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="auto_detect" value="1" id="autoDetect" style="accent-color:var(--primary);width:18px;height:18px;" {{ old('auto_detect') ? 'checked' : '' }}>
                        <span style="font-size:13px;font-weight:600;color:var(--primary-light);">Aktifkan</span>
                    </label>
                </div>

                {{-- AI Preview Result --}}
                <div id="aiPreview" style="display:none;">
                    <div style="border-top:1px solid rgba(99,102,241,0.2);padding-top:12px;margin-top:8px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:var(--success);animation:pulse-glow 2s infinite;"></div>
                            <span style="font-size:13px;font-weight:600;color:#94a3b8;">Hasil Deteksi AI:</span>
                        </div>
                        <div id="aiResultContent" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                            <span id="aiCategoryBadge" class="badge" style="font-size:14px;padding:8px 16px;background:rgba(99,102,241,0.2);color:var(--primary-light);">-</span>
                            <span id="aiConfidence" style="font-size:13px;color:#94a3b8;">Confidence: -</span>
                        </div>
                        <div id="aiKeywords" style="margin-top:8px;font-size:12px;color:#64748b;"></div>
                        <div id="aiAllScores" style="margin-top:12px;"></div>
                    </div>
                </div>

                <div id="aiLoading" style="display:none;text-align:center;padding:12px;">
                    <span style="color:var(--text-muted);font-size:13px;display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">hourglass_top</span> Menganalisis teks...</span>
                </div>
            </div>

            <div class="form-group" id="categoryGroup">
                <label class="form-label">Kategori <span id="categoryRequired">*</span></label>
                <select name="category_id" id="categorySelect" class="form-select">
                    <option value="">Pilih Kategori (atau gunakan AI)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
                <div id="aiCategoryNote" style="display:none;margin-top:6px;font-size:12px;color:var(--primary-light);">
                    <span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">smart_toy</span> Kategori akan dideteksi otomatis oleh AI
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto / Video (opsional, maks 10MB per file)</label>
                <input type="file" name="media[]" multiple accept="image/*,video/*" class="form-input" style="padding:10px;">
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_anonymous" value="1" id="is_anonymous" style="accent-color:var(--primary);" {{ old('is_anonymous')?'checked':'' }}>
                <label for="is_anonymous" style="font-size:14px;color:#94a3b8;">Laporkan secara anonim</label>
            </div>
        </div>

        <div class="glass-card" style="padding:32px;margin-bottom:24px;">
            <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:20px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">location_on</span> Lokasi</h2>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <input type="text" name="address" id="address" class="form-input" value="{{ old('address') }}" placeholder="Jl. Merdeka No. 45, Jakarta Pusat">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Latitude</label>
                    <input type="number" name="latitude" id="latitude" class="form-input" value="{{ old('latitude') }}" step="any" placeholder="-6.2088">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude</label>
                    <input type="number" name="longitude" id="longitude" class="form-input" value="{{ old('longitude') }}" step="any" placeholder="106.8456">
                </div>
            </div>

            <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">touch_app</span> Klik pada peta untuk menentukan lokasi:</p>
            <div class="map-container">
                <div id="map" style="height:350px;"></div>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:16px;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;">send</span> Kirim Laporan</button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Map ───────────────────────────────────
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const initLat = latInput.value || -6.2088;
    const initLng = lngInput.value || 106.8456;

    const map = L.map('map').setView([initLat, initLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        if (marker) { marker.setLatLng(e.latlng); }
        else {
            marker = L.marker(e.latlng, { draggable: true }).addTo(map);
            marker.on('dragend', function(ev) {
                const pos = ev.target.getLatLng();
                latInput.value = pos.lat.toFixed(8);
                lngInput.value = pos.lng.toFixed(8);
            });
        }
    });
    if (latInput.value && lngInput.value) {
        marker = L.marker([latInput.value, lngInput.value], { draggable: true }).addTo(map);
    }

    // ─── AI Auto-Detect ────────────────────────
    const autoDetect = document.getElementById('autoDetect');
    const categorySelect = document.getElementById('categorySelect');
    const categoryRequired = document.getElementById('categoryRequired');
    const aiCategoryNote = document.getElementById('aiCategoryNote');
    const aiPreview = document.getElementById('aiPreview');
    const aiLoading = document.getElementById('aiLoading');
    const titleInput = document.getElementById('reportTitle');
    const descInput = document.getElementById('reportDescription');
    let debounceTimer = null;

    function toggleAutoDetect() {
        const isOn = autoDetect.checked;
        if (isOn) {
            categorySelect.removeAttribute('required');
            categoryRequired.style.display = 'none';
            aiCategoryNote.style.display = 'block';
            runAIClassification();
        } else {
            categorySelect.setAttribute('required', 'required');
            categoryRequired.style.display = 'inline';
            aiCategoryNote.style.display = 'none';
            aiPreview.style.display = 'none';
        }
    }

    autoDetect.addEventListener('change', toggleAutoDetect);

    // Debounced AI classification on text input
    function onTextChange() {
        if (!autoDetect.checked) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runAIClassification, 800);
    }

    titleInput.addEventListener('input', onTextChange);
    descInput.addEventListener('input', onTextChange);

    function runAIClassification() {
        const text = (titleInput.value + ' ' + descInput.value).trim();
        if (text.length < 5) {
            aiPreview.style.display = 'none';
            return;
        }

        aiLoading.style.display = 'block';
        aiPreview.style.display = 'none';

        fetch('{{ route("api.ai.classify") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ text: text })
        })
        .then(r => r.json())
        .then(data => {
            aiLoading.style.display = 'none';

            if (data.category_name) {
                aiPreview.style.display = 'block';
                document.getElementById('aiCategoryBadge').textContent = data.category_name;
                document.getElementById('aiConfidence').textContent = 'Confidence: ' + data.confidence.toFixed(1) + '%';

                // Show matched keywords
                if (data.matched_keywords && data.matched_keywords.length) {
                    document.getElementById('aiKeywords').innerHTML =
                        'Kata kunci: ' + data.matched_keywords.map(k => '<span style="background:rgba(99,102,241,0.15);padding:2px 8px;border-radius:6px;margin:2px;">' + k + '</span>').join(' ');
                }

                // Show all scores as small progress bars
                const allScores = data.all_scores || {};
                let scoresHtml = '';
                for (const [cat, info] of Object.entries(allScores)) {
                    const barColor = cat === data.category_name ? 'var(--primary)' : 'rgba(255,255,255,0.15)';
                    scoresHtml += `
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:12px;">
                            <span style="width:100px;color:#94a3b8;">${cat}</span>
                            <div style="flex:1;background:rgba(255,255,255,0.1);border-radius:4px;height:6px;overflow:hidden;">
                                <div style="width:${Math.min(100, info.confidence)}%;background:${barColor};height:100%;border-radius:4px;transition:width 0.5s ease;"></div>
                            </div>
                            <span style="width:40px;text-align:right;color:#64748b;">${info.confidence.toFixed(0)}%</span>
                        </div>
                    `;
                }
                document.getElementById('aiAllScores').innerHTML = scoresHtml;

                // Auto-select category in dropdown
                if (data.category_id) {
                    categorySelect.value = data.category_id;
                }
            } else {
                aiPreview.style.display = 'block';
                document.getElementById('aiCategoryBadge').textContent = 'Tidak terdeteksi';
                document.getElementById('aiConfidence').textContent = 'Coba deskripsikan lebih detail';
                document.getElementById('aiKeywords').innerHTML = '';
                document.getElementById('aiAllScores').innerHTML = '';
            }
        })
        .catch(err => {
            aiLoading.style.display = 'none';
            console.error('AI classification error:', err);
        });
    }

    // Init state
    toggleAutoDetect();
});
</script>
@endpush
@endsection
