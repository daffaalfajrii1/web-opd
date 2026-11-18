@if ($websiteSettings->whatsappUrl())
    <a href="{{ $websiteSettings->whatsappAduanUrl() }}" class="rsud-wa-fab" target="_blank" rel="noopener" title="Aduan WhatsApp" aria-label="Kirim aduan via WhatsApp">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
        <span>Aduan</span>
    </a>
@endif
