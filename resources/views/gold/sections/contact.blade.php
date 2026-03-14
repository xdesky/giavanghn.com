<div class="glass-card p-4 md:p-6">
    <h2 class="text-lg font-bold text-[#001061] mb-4">Liên hệ với chúng tôi</h2>
    <div class="grid gap-5 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Email</h3>
            <p class="mt-1 text-sm text-blue-600">contact@goldprice.vn</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Hotline</h3>
            <p class="mt-1 text-sm text-blue-600">1900 xxxx xx</p>
        </div>
    </div>
    <form class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4 grid gap-3" onsubmit="event.preventDefault();alert('Cảm ơn bạn đã góp ý!')">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Họ và tên</label>
            <input type="text" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Nguyễn Văn A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="email@example.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Chủ đề</label>
            <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                <option>Góp ý nội dung</option>
                <option>Hợp tác quảng cáo</option>
                <option>Yêu cầu API</option>
                <option>Báo lỗi</option>
                <option>Khác</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nội dung</label>
            <textarea rows="5" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Viết nội dung tại đây..."></textarea>
        </div>
        <button type="submit" class="btn-primary py-3">Gửi tin nhắn</button>
    </form>
</div>