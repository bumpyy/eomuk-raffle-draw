@props(['name', 'prizeImage'])
<div class="max-w-95 w-full rounded-2xl bg-white p-2.5 text-center shadow-[0_30px_30px_-25px_rgba(65,51,183,0.25)]">
    <div class="text-cedea-blue bg-cedea-dark relative rounded-xl p-5 pt-10">
        <div class="-left-2/6 absolute -top-[110%] w-full">
            <img src="{{ $prizeImage }}" alt="">
        </div>

        <h1 class="mt-3 text-2xl font-extrabold text-white">
            {{ $name }}
        </h1>

    </div>
</div>
{{ $slot }}
