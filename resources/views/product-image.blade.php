<div>
    <spa class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Imagens</spa>
    @if(is_array($getState()))
        <ul class="flex">
            @foreach($getState() as $image)
                <li class="flex snap-start flex-col items-center justify-center p-2" role="option">
                    <img class="mt-2 rounded" src="{{ Storage::url($image) }}" alt="placeholder image">
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-justify text-danger-600">Nenhuma imagem encontrada.</p>
    @endif
</div>


