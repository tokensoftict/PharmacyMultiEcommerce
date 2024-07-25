<footer class="footer position-absolute">
    <div class="row g-0 justify-content-between align-items-center h-100">
        <div class="col-12 col-sm-auto text-center">
            <p class="mb-0 mt-2 mt-sm-0 text-body">{{ app(\App\Classes\Settings::class)->uiSettings('title') ?? config('app.name') }}<span class="d-none d-sm-inline-block"></span><span class="d-none d-sm-inline-block mx-1">|</span><br class="d-sm-none">2024 ©<a class="mx-1" href="https://tokensoftict.com.ng/">Tokensoft ICT</a></p>
        </div>
        <div class="col-12 col-sm-auto text-center">
            <p class="mb-0 text-body-tertiary text-opacity-85">&copy; Alright Reserved</p>
        </div>
    </div>
</footer>
