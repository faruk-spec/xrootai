<!-- Billing Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        showUpgradeModal: false,
        couponCode: '',
        copyReferral() {
            navigator.clipboard.writeText('https://xrootai.com/referral/join?ref={{ $user->id ?? 8921 }}');
            $dispatch('notify', { message: 'Referral link copied to clipboard! Share with colleagues.', type: 'success' });
        },
        applyCoupon() {
            if (!this.couponCode) return;
            $dispatch('notify', { message: `Promo code '${this.couponCode.toUpperCase()}' applied! +$25.00 credits added.`, type: 'success' });
            this.couponCode = '';
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Subscription Billing, Quota & Credits</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Manage your Pro Enterprise plan, track monthly token burn, download VAT invoices, and redeem referral credits.</p>
        </div>
        <x-settings.badge variant="accent" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>'>
            Pro Enterprise Tier
        </x-settings.badge>
    </div>

    <!-- Active Subscription Tier Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 p-6 sm:p-8 rounded-[28px] border flex flex-col justify-between space-y-6 relative overflow-hidden"
             style="background: linear-gradient(135deg, var(--clay-card-bg) 0%, rgba(74, 136, 255, 0.08) 100%); border-color: var(--accent); box-shadow: var(--clay-outer-shadow);">
            <!-- Background glow -->
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-[var(--accent)] opacity-15 blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="px-3 py-1 rounded-xl text-xs font-extrabold uppercase tracking-wider bg-blue-500 text-white shadow-md">
                            Active Subscription
                        </span>
                        <span class="text-xs font-mono opacity-80" style="color: var(--text-secondary);">Renews August 10, 2026</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight mt-3" style="color: var(--text-primary);">Pro Enterprise SaaS Workspace</h3>
                    <p class="text-xs sm:text-sm mt-1 leading-relaxed max-w-lg" style="color: var(--text-secondary);">
                        Includes unlimited BYOK gateway requests, 500,000 monthly cloud generation tokens, 2M context window, autonomous memory store, and priority 24/7 dedicated support.
                    </p>
                </div>

                <div class="text-left sm:text-right shrink-0">
                    <span class="text-3xl sm:text-4xl font-black tracking-tight" style="color: var(--accent);">$49</span>
                    <span class="text-xs sm:text-sm font-semibold opacity-75" style="color: var(--text-secondary);">/ month per seat</span>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="pt-5 border-t flex flex-wrap items-center justify-between gap-3" style="border-color: var(--clay-card-border);">
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-500">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span>Automatic billing active</span>
                </div>

                <div class="flex items-center gap-2.5">
                    <button type="button" @click="showUpgradeModal = true" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-lg transition-transform hover:scale-105" style="background: var(--accent); color: white;">
                        Upgrade Tier / Manage Seats
                    </button>
                    <button type="button" @click="$dispatch('notify', { message: 'Redirecting to Stripe Billing Portal...', type: 'accent' })" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all border" style="background: var(--clay-input-bg); color: var(--text-primary); border-color: var(--clay-card-border);">
                        Stripe Portal
                    </button>
                </div>
            </div>
        </div>

        <!-- Available Credits Card -->
        <div class="lg:col-span-1 p-6 sm:p-8 rounded-[28px] border flex flex-col justify-between space-y-6"
             style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" style="color: var(--text-secondary);">Prepaid Account Balance</span>
                <h3 class="text-4xl font-black tracking-tight mt-2 text-emerald-500 dark:text-emerald-400">$45.00</h3>
                <p class="text-xs mt-1.5 leading-normal" style="color: var(--text-secondary);">
                    Credits automatically apply toward overage tokens or premium custom fine-tuning jobs.
                </p>
            </div>

            <!-- Redeem Coupon Input -->
            <div class="space-y-2 pt-4 border-t" style="border-color: var(--clay-card-border);">
                <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Redeem Promo Code</label>
                <div class="flex gap-2">
                    <input type="text"
                           x-model="couponCode"
                           placeholder="PROMO2026"
                           class="w-full rounded-xl py-2 px-3 text-xs uppercase font-mono font-bold transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
                    <button type="button" @click="applyCoupon()" class="px-4 py-2 rounded-xl text-xs font-bold transition-transform hover:scale-105 shrink-0" style="background: var(--accent); color: white;">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Consumption & Quota Tracker -->
    <x-settings.card title="Monthly Token Consumption Breakdown" description="Real-time telemetry tracking your monthly prompt generation and reasoning token burn." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>'>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h4 class="text-xl font-black tracking-tight font-mono" style="color: var(--text-primary);">284,190 / 500,000 Tokens</h4>
                    <span class="text-xs" style="color: var(--text-secondary);">Billing cycle resets on the 1st of every calendar month.</span>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-mono font-bold bg-blue-500/15 text-blue-400 border border-blue-500/20 shrink-0">
                    56.8% Quota Used
                </span>
            </div>

            <x-settings.progress-bar :percentage="56.8" color="accent" height="h-3.5" />

            <!-- Breakdown cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div class="p-4 rounded-2xl border space-y-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-2 text-xs font-semibold" style="color: var(--text-secondary);">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                        <span>Prompt Input Tokens</span>
                    </div>
                    <span class="text-base font-mono font-bold block" style="color: var(--text-primary);">142,095 tokens (50%)</span>
                </div>

                <div class="p-4 rounded-2xl border space-y-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-2 text-xs font-semibold" style="color: var(--text-secondary);">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span>Completion Output Tokens</span>
                    </div>
                    <span class="text-base font-mono font-bold block" style="color: var(--text-primary);">113,676 tokens (40%)</span>
                </div>

                <div class="p-4 rounded-2xl border space-y-1" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-2 text-xs font-semibold" style="color: var(--text-secondary);">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                        <span>CoT Reasoning Scratchpad</span>
                    </div>
                    <span class="text-base font-mono font-bold block" style="color: var(--text-primary);">28,419 tokens (10%)</span>
                </div>
            </div>
        </div>
    </x-settings.card>

    <!-- Payment Methods & Billing Address -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-settings.card title="Payment Method & Cards" description="Manage primary credit card and automated renewal methods." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>'>
            <div class="space-y-4">
                <div class="p-4 rounded-2xl border flex items-center justify-between gap-4" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-8 rounded-lg bg-blue-900 text-white flex items-center justify-center font-black text-xs shrink-0 shadow">
                            VISA
                        </div>
                        <div>
                            <h6 class="text-sm font-bold font-mono tracking-tight" style="color: var(--text-primary);">•••• •••• •••• 4242</h6>
                            <span class="text-xs opacity-70 block" style="color: var(--text-secondary);">Expires 12/2028 • Default Payment Method</span>
                        </div>
                    </div>
                    <x-settings.badge variant="success" size="sm">Primary</x-settings.badge>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="button" @click="$dispatch('notify', { message: 'Opening Stripe secure card form...', type: 'accent' })" class="px-4 py-2 rounded-xl text-xs font-semibold transition-transform hover:scale-105 border flex items-center gap-1.5" style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add New Payment Method
                    </button>
                </div>
            </div>
        </x-settings.card>

        <!-- Referral Program Card -->
        <x-settings.card title="Refer a Friend & Earn $20" description="Give $20 in free tokens to colleagues, get $20 added to your prepaid balance on their first upgrade." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>'>
            <div class="space-y-4">
                <div class="relative flex items-center">
                    <input type="text"
                           readonly
                           value="https://xrootai.com/referral/join?ref={{ $user->id ?? 8921 }}"
                           class="w-full rounded-2xl py-3 pl-4 pr-24 text-xs font-mono font-semibold select-all"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
                    <button type="button" @click="copyReferral()" class="absolute right-2 px-3 py-1.5 rounded-xl text-xs font-bold shadow transition-transform hover:scale-105" style="background: var(--accent); color: white;">
                        Copy Link
                    </button>
                </div>

                <div class="flex items-center justify-between text-xs" style="color: var(--text-secondary);">
                    <span>Total Referrals Joined: <strong style="color: var(--text-primary);">4 users</strong></span>
                    <span>Total Credits Earned: <strong class="text-emerald-500">$80.00</strong></span>
                </div>
            </div>
        </x-settings.card>
    </div>

    <!-- Billing Invoices Card -->
    <x-settings.card title="Recent Invoices & Payment History" description="Download VAT compliant PDF receipts for accounting and expense reports." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b uppercase font-mono tracking-wider opacity-70" style="border-color: var(--clay-card-border); color: var(--text-secondary);">
                        <th class="py-3 px-2 font-semibold">Invoice ID</th>
                        <th class="py-3 px-2 font-semibold">Billing Period</th>
                        <th class="py-3 px-2 font-semibold">Amount</th>
                        <th class="py-3 px-2 font-semibold">Status</th>
                        <th class="py-3 px-2 font-semibold text-right">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--clay-card-border);">
                    @foreach([
                        ['id' => 'INV-2026-0701', 'date' => 'July 1, 2026', 'amount' => '$49.00', 'status' => 'Paid'],
                        ['id' => 'INV-2026-0601', 'date' => 'June 1, 2026', 'amount' => '$49.00', 'status' => 'Paid'],
                        ['id' => 'INV-2026-0501', 'date' => 'May 1, 2026', 'amount' => '$49.00', 'status' => 'Paid'],
                        ['id' => 'INV-2026-0401', 'date' => 'April 1, 2026', 'amount' => '$49.00', 'status' => 'Paid']
                    ] as $inv)
                        <tr class="transition-colors hover:bg-white/5 font-mono">
                            <td class="py-3.5 px-2 font-bold" style="color: var(--text-primary);">{{ $inv['id'] }}</td>
                            <td class="py-3.5 px-2" style="color: var(--text-secondary);">{{ $inv['date'] }}</td>
                            <td class="py-3.5 px-2 font-bold" style="color: var(--text-primary);">{{ $inv['amount'] }}</td>
                            <td class="py-3.5 px-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500/15 text-emerald-500 border border-emerald-500/20">
                                    {{ $inv['status'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-2 text-right">
                                <button type="button" @click="$dispatch('notify', { message: 'Downloading PDF receipt {{ $inv['id'] }}...', type: 'success' })" class="text-[var(--accent)] font-semibold hover:underline flex items-center gap-1 ml-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    PDF
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-settings.card>

    <!-- Modal: Plan Upgrade Switcher -->
    <div x-show="showUpgradeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/60" style="display: none;">
        <div class="max-w-2xl w-full rounded-[32px] p-6 sm:p-8 border shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto" style="background: var(--clay-card-bg); border-color: var(--clay-card-border);">
            <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--clay-card-border);">
                <h3 class="text-xl font-bold tracking-tight" style="color: var(--text-primary);">Select Enterprise Subscription Tier</h3>
                <button type="button" @click="showUpgradeModal = false" class="opacity-60 hover:opacity-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl border space-y-4 transition-all" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div>
                        <span class="text-xs font-bold uppercase text-blue-400 block">Pro Enterprise (Current)</span>
                        <h4 class="text-2xl font-black mt-1" style="color: var(--text-primary);">$49 / mo</h4>
                    </div>
                    <ul class="text-xs space-y-2 opacity-80" style="color: var(--text-secondary);">
                        <li>✓ 500,000 monthly cloud tokens</li>
                        <li>✓ Unlimited BYOK Gateway access</li>
                        <li>✓ 2M context window support</li>
                        <li>✓ Autonomous memory store</li>
                    </ul>
                    <button type="button" disabled class="w-full py-2.5 rounded-xl text-xs font-bold bg-white/10 opacity-70 cursor-not-allowed">Active Plan</button>
                </div>

                <div class="p-5 rounded-2xl border-2 space-y-4 transition-all relative overflow-hidden" style="background: var(--clay-card-bg); border-color: var(--accent); box-shadow: 0 8px 24px rgba(74, 136, 255, 0.2);">
                    <div class="absolute right-3 top-3"><span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-purple-500 text-white">RECOMMENDED</span></div>
                    <div>
                        <span class="text-xs font-bold uppercase text-purple-400 block">Scale Unlimited</span>
                        <h4 class="text-2xl font-black mt-1" style="color: var(--text-primary);">$149 / mo</h4>
                    </div>
                    <ul class="text-xs space-y-2 opacity-90" style="color: var(--text-secondary);">
                        <li>✓ 2,500,000 monthly cloud tokens</li>
                        <li>✓ Dedicated cluster fine-tuning</li>
                        <li>✓ Custom Team MCP Server integration</li>
                        <li>✓ 99.99% SLA & 1-hour support response</li>
                    </ul>
                    <button type="button" @click="$dispatch('notify', { message: 'Upgrading to Scale Unlimited...', type: 'success' }); showUpgradeModal = false;" class="w-full py-2.5 rounded-xl text-xs font-bold shadow transition-transform hover:scale-105" style="background: var(--accent); color: white;">
                        Upgrade to Scale Unlimited
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
