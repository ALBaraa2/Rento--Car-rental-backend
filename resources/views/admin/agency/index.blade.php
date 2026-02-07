<div>
    @foreach ($agencies as $agency)
        {{ $agency->user->name }}
    @endforeach
</div>
