{{-- Optilux mendefinisikan warna di selector "*", jadi override harus ikut ke * agar footer/section ikut. --}}
<style>
    :root,
    * {
        {!! $websiteSettings->themeCssVariables() !!}
    }
</style>
