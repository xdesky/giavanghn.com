<div class="glass-card p-4 md:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-[#001061]">Bảng giá {{ $brandName }} hôm nay</h2>
        <span class="chip">Cập nhật: 10:30</span>
    </div>
    <div class="table-wrap rounded-sm border border-[#bcbcbc]">
        <table class="w-full text-sm">
            <thead class="bg-[#f5f5f5]">
                <tr><th class="p-3 text-left font-semibold">Loại vàng</th><th class="p-3 text-right font-semibold">Mua vào</th><th class="p-3 text-right font-semibold">Bán ra</th><th class="p-3 text-right font-semibold">Thay đổi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr><td class="p-3 font-medium">Vàng miếng 1 lượng</td><td class="p-3 text-right">91,500,000</td><td class="p-3 text-right">92,500,000</td><td class="p-3 text-right font-semibold text-emerald-600">+500,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 5 chỉ</td><td class="p-3 text-right">91,300,000</td><td class="p-3 text-right">92,300,000</td><td class="p-3 text-right font-semibold text-emerald-600">+300,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 2 chỉ</td><td class="p-3 text-right">91,200,000</td><td class="p-3 text-right">92,200,000</td><td class="p-3 text-right font-semibold text-emerald-600">+200,000</td></tr>
                <tr><td class="p-3 font-medium">Vàng miếng 1 chỉ</td><td class="p-3 text-right">91,100,000</td><td class="p-3 text-right">92,100,000</td><td class="p-3 text-right font-semibold text-emerald-600">+100,000</td></tr>
                <tr><td class="p-3 font-medium">Nhẫn trơn 9999</td><td class="p-3 text-right">82,500,000</td><td class="p-3 text-right">83,600,000</td><td class="p-3 text-right font-semibold text-emerald-600">+400,000</td></tr>
                <tr><td class="p-3 font-medium">Vang 24K</td><td class="p-3 text-right">82,300,000</td><td class="p-3 text-right">83,400,000</td><td class="p-3 text-right font-semibold text-emerald-600">+350,000</td></tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 chart-placeholder rounded-sm border border-[#bcbcbc] bg-slate-50 p-4">
        <p class="text-xs text-slate-500 mb-2">Biến động giá {{ $brandName }} trong ngày</p>
        <svg viewBox="0 0 600 100" class="w-full h-24"><polyline fill="none" stroke="#f59e0b" stroke-width="2" points="0,60 60,55 120,50 180,45 240,48 300,42 360,38 420,35 480,33 540,30 600,28"/></svg>
    </div>
    <div class="mt-4 rounded-sm border border-amber-100 bg-amber-50 p-4">
        <h3 class="font-bold text-amber-900">Giới thiệu {{ $brandName }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-amber-800">{{ $brandName }} là một trong những thương hiệu vàng uy tín hàng đầu tại Việt Nam, cung cấp đa dạng sản phẩm vàng miếng, vàng nhẫn và vàng trang sức chất lượng cao. Giá được cập nhật liên tục trong ngày giao dịch.</p>
    </div>
</div>