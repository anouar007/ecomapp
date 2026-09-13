@foreach(\App\Models\CustomCode::where('is_active', true)->where('position', $position)->orderByDesc('priority')->get() as $code)
    @if($code->type === 'css')
        <style>{!! $code->content !!}</style>
    @elseif($code->type === 'js')
        <script>{!! $code->content !!}</script>
    @else
        {!! $code->content !!}
    @endif
@endforeach

