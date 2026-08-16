@if (config('services.turnstile.site_key'))
    <label for="cf-turnstile-response">Verifikasi Bukan Robot</label>
    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
    @error('cf-turnstile-response')
    <div class="field-error">{{ $message }}</div>
    @enderror
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
@else
    <label>Verifikasi Bukan Robot</label>
    <div class="field-error">Captcha belum dikonfigurasi. Hubungi administrator.</div>
@endif
