


<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/anchor.min.js') }}"></script>
<script src="{{ asset('js/is.min.js') }}"></script>
<script src="{{ asset('js/all.min.js') }}"></script>
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
<script src="{{ asset('js/list.min.js') }}"></script>
<script src="{{ asset('js/feather.min.js') }}"></script>
<script src="{{ asset('js/dayjs.min.js') }}"></script>
<script src="{{ asset('js/phoenix.js') }}"></script>

@stack('js')
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
<x-livewire-notification::toast />
</body>
</html>

