<div class="glass-card p-4 md:p-6">
    <h2 class="text-xl font-bold text-slate-900 mb-4">Lien he voi chung toi</h2>
    <div class="grid gap-3 sm:grid-cols-2 mb-4">
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Email</h3>
            <p class="mt-1 text-sm text-blue-600">contact@goldprice.vn</p>
        </div>
        <div class="rounded-sm border border-slate-200 p-4">
            <h3 class="font-bold text-sm">Hotline</h3>
            <p class="mt-1 text-sm text-blue-600">1900 xxxx xx</p>
        </div>
    </div>
    <form class="rounded-sm border border-[#bcbcbc] bg-slate-50 p-4 grid gap-3" onsubmit="event.preventDefault();alert('Cam on ban da gop y!')">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Ho va ten</label>
            <input type="text" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Nguyen Van A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="email@example.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Chu de</label>
            <select class="w-full rounded-sm border border-blue-200 bg-white px-3 py-2.5 text-sm">
                <option>Gop y noi dung</option>
                <option>Hop tac quang cao</option>
                <option>Yeu cau API</option>
                <option>Bao loi</option>
                <option>Khac</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Noi dung</label>
            <textarea rows="5" required class="w-full rounded-sm border border-blue-200 px-3 py-2.5 text-sm" placeholder="Viet noi dung tai day..."></textarea>
        </div>
        <button type="submit" class="btn-primary py-3">Gui tin nhan</button>
    </form>
</div>