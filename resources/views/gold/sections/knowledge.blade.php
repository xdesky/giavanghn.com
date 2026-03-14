<div class="glass-card p-4 md:p-6 article-body">
    <h2 class="text-lg font-bold text-[#001061] mb-4">{{ $articleTitle }}</h2>

    @if (!empty($toc))
    <nav class="mb-4 rounded-sm border border-[#bcbcbc] bg-blue-50 p-4">
        <p class="font-semibold text-sm text-blue-900 mb-2">Nội dung chính</p>
        <ol class="list-decimal list-inside space-y-1 text-sm text-blue-700">
            @foreach ($toc as $item)
            <li>{{ $item }}</li>
            @endforeach
        </ol>
    </nav>
    @endif

    @foreach ($sections as $sec)
    <div class="mb-4">
        <h3 class="text-lg font-bold text-[#001061] mb-2">{{ $sec['heading'] }}</h3>
        <div class="text-sm leading-relaxed text-slate-700">{!! $sec['body'] !!}</div>
    </div>
    @endforeach

    <div class="mt-6 rounded-sm border border-amber-200 bg-amber-50 p-4">
        <p class="font-bold text-amber-900 text-sm">Lưu ý</p>
        <p class="mt-1 text-sm text-amber-800">Thông tin trên mang tính chất tham khảo và giáo dục. Hãy tham khảo ý kiến chuyên gia trước khi đưa ra quyết định đầu tư.</p>
    </div>
</div>