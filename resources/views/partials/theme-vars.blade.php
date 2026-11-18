@php
    $theme = $websiteSettings->theme();
    $vars = $websiteSettings->themeCssVariables();
@endphp
{{--
  Optilux set warna di selector "*", jadi override harus ke :root dan *.
  Rule !important di bawah memastikan hero/footer ikut tema meski CSS lama ter-cache di hosting.
--}}
<style id="opd-theme-vars">
    :root,
    html,
    body,
    #wrapper,
    * {
        {!! $vars !!}
    }

    .kecamatan-hero,
    .breadcrumb-area.rsud-page-hero {
        background-image: none !important;
        background:
            radial-gradient(circle at 12% 20%, rgba(255, 255, 255, 0.14) 0%, transparent 42%),
            radial-gradient(circle at 88% 75%, rgba(0, 0, 0, 0.18) 0%, transparent 48%),
            linear-gradient(135deg, {{ $theme['dark'] }} 0%, {{ $theme['primary'] }} 42%, {{ $theme['light'] }} 100%) !important;
    }

    body.page-beranda,
    body.page-beranda footer,
    footer.section-dark,
    footer {
        background-color: {{ $theme['primary'] }} !important;
        background: {{ $theme['primary'] }} !important;
    }

    a.btn-main,
    .btn-main,
    #back-to-top {
        background: {{ $theme['primary'] }} !important;
    }

    a.btn-main:hover,
    .btn-main:hover,
    #back-to-top:hover {
        background: {{ $theme['dark'] }} !important;
    }

    .id-color,
    .kecamatan-hero-stats__title,
    .kecamatan-hero-stats__number {
        color: {{ $theme['primary'] }} !important;
    }
</style>
