<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dominium - Color Scheme Showcase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Color Scheme 1: Professional Navy - High Contrast */
        .scheme-navy {
            --primary: #0F172A;
            --secondary: #475569;
            --accent: #F97316;
            --bg: #F1F5F9;
            --surface: #FFFFFF;
            --border: #CBD5E1;
            --success: #059669;
            --error: #DC2626;
            --text-on-primary: #FFFFFF;
            --text-on-secondary: #FFFFFF;
            --text-on-accent: #FFFFFF;
            --text-on-bg: #1E293B;
            --text-on-surface: #334155;
            --text-on-success: #FFFFFF;
            --text-on-error: #FFFFFF;
        }
        .scheme-navy .preview-primary {
            background-color: var(--primary);
            color: var(--text-on-primary);
        }
        .scheme-navy .preview-secondary {
            background-color: var(--secondary);
            color: var(--text-on-secondary);
        }
        .scheme-navy .preview-accent {
            background-color: var(--accent);
            color: var(--text-on-accent);
        }
        .scheme-navy .preview-success {
            background-color: var(--success);
            color: var(--text-on-success);
        }
        .scheme-navy .preview-error {
            background-color: var(--error);
            color: var(--text-on-error);
        }
        .scheme-navy .preview-card {
            background-color: var(--surface);
            color: var(--text-on-surface);
            border: 2px solid var(--border);
        }
        .scheme-navy.scheme-preview {
            background-color: var(--bg);
            color: var(--text-on-bg);
        }
        .scheme-navy .text-primary {
            color: var(--primary) !important;
        }
        .scheme-navy .text-secondary {
            color: var(--secondary) !important;
        }
        .scheme-navy .text-accent {
            color: var(--accent) !important;
        }

        /* Color Scheme 2: Tropical Modern */
        .scheme-tropical {
            --primary: #0369A1;
            --secondary: #78716C;
            --accent: #EA580C;
            --bg: #FEF7ED;
            --surface: #FFFFFF;
            --border: #FED7AA;
            --success: #15803D;
            --error: #DC2626;
        }

        /* Color Scheme 3: Minimal Black & White */
        .scheme-minimal {
            --primary: #000000;
            --secondary: #404040;
            --accent: #3B82F6;
            --bg: #FFFFFF;
            --surface: #F5F5F5;
            --border: #E5E5E5;
            --success: #16A34A;
            --error: #DC2626;
        }

        /* Color Scheme 4: Earthy Organic */
        .scheme-earthy {
            --primary: #166534;
            --secondary: #57534E;
            --accent: #C2410C;
            --bg: #F5F5F4;
            --surface: #FAFAF9;
            --border: #D6D3D1;
            --success: #84CC16;
            --error: #B91C1C;
        }

        /* Color Scheme 5: Dark Mode Modern */
        .scheme-dark {
            --primary: #F8FAFC;
            --secondary: #94A3B8;
            --accent: #3B82F6;
            --bg: #0F172A;
            --surface: #1E293B;
            --border: #334155;
            --success: #22C55E;
            --error: #F87171;
        }

        /* Color Scheme 6: Purple Haze */
        .scheme-purple {
            --primary: #6B21A8;
            --secondary: #7C3AED;
            --accent: #EC4899;
            --bg: #FAF5FF;
            --surface: #FFFFFF;
            --border: #E9D5FF;
            --success: #22C55E;
            --error: #EF4444;
        }

        /* Color Scheme 7: Ocean Breeze */
        .scheme-ocean {
            --primary: #0C4A6E;
            --secondary: #38BDF8;
            --accent: #06B6D4;
            --bg: #F0F9FF;
            --surface: #FFFFFF;
            --border: #BAE6FD;
            --success: #14B8A6;
            --error: #F43F5E;
        }

        /* Color Scheme 8: Sunset Boulevard */
        .scheme-sunset {
            --primary: #BE123C;
            --secondary: #FB7185;
            --accent: #F59E0B;
            --bg: #FFF1F2;
            --surface: #FFFFFF;
            --border: #FECDD3;
            --success: #65A30D;
            --error: #E11D48;
        }

        /* Color Scheme 9: Forest Retreat */
        .scheme-forest {
            --primary: #14532D;
            --secondary: #65A30D;
            --accent: #D97706;
            --bg: #F7FEE7;
            --surface: #FFFFFF;
            --border: #D9F99D;
            --success: #16A34A;
            --error: #B91C1C;
        }

        /* Color Scheme 10: Midnight City */
        .scheme-midnight {
            --primary: #E2E8F0;
            --secondary: #94A3B8;
            --accent: #A855F7;
            --bg: #020617;
            --surface: #0F172A;
            --border: #1E293B;
            --success: #10B981;
            --error: #F43F5E;
        }

        /* Color Scheme 11: Coral Reef */
        .scheme-coral {
            --primary: #0D9488;
            --secondary: #2DD4BF;
            --accent: #F97316;
            --bg: #F0FDFA;
            --surface: #FFFFFF;
            --border: #99F6E4;
            --success: #84CC16;
            --error: #EF4444;
        }

        /* Color Scheme 12: Rose Gold Luxury */
        .scheme-rose {
            --primary: #881337;
            --secondary: #FDA4AF;
            --accent: #FDE047;
            --bg: #FFF1F2;
            --surface: #FFFFFF;
            --border: #FECDD3;
            --success: #65A30D;
            --error: #E11D48;
        }

        /* Apply colors */
        .scheme-preview {
            background-color: var(--bg);
            color: var(--primary);
        }
        .preview-card {
            background-color: var(--surface);
            border: 1px solid var(--border);
        }
        .preview-primary {
            background-color: var(--primary);
            color: white;
        }
        .preview-accent {
            background-color: var(--accent);
            color: white;
        }
        .preview-secondary {
            background-color: var(--secondary);
            color: white;
        }
        .preview-success {
            background-color: var(--success);
            color: white;
        }
        .preview-error {
            background-color: var(--error);
            color: white;
        }
        .text-primary { color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .text-accent { color: var(--accent); }
        .border-theme { border-color: var(--border); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-2 text-slate-900">Dominium Color Schemes</h1>
        <p class="text-center text-slate-600 mb-4">12 unique color palettes to choose from</p>
        <p class="text-center text-slate-500 text-sm mb-12 max-w-2xl mx-auto">Professional Navy (recommended), Tropical Modern, Minimal B&W, Earthy Organic, Dark Mode, Purple Haze, Ocean Breeze, Sunset Boulevard, Forest Retreat, Midnight City, Coral Reef, and Rose Gold Luxury</p>

        <!-- Scheme 1: Professional Navy - HIGH CONTRAST VERSION -->
        <section class="mb-12 rounded-xl overflow-hidden shadow-lg" style="background-color: #F1F5F9;">
            <div class="p-6 border-b-2" style="border-color: #CBD5E1; background-color: #F8FAFC;">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold" style="color: #0F172A;">1. Professional Navy</h2>
                        <p class="text-sm mt-1" style="color: #475569;">Clean, trustworthy, Airbnb-inspired</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: #D1FAE5; color: #065F46;">Recommended</span>
                </div>
            </div>
            <div class="p-6">
                <!-- Color Swatches - ALL LABELS NOW VISIBLE -->
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #0F172A;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Primary</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#0F172A</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #475569;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Secondary</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#475569</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #F97316;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Accent</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#F97316</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm border" style="background-color: #F1F5F9; border-color: #CBD5E1;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Background</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#F1F5F9</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm border" style="background-color: #FFFFFF; border-color: #CBD5E1;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Surface</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #CBD5E1;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Border</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#CBD5E1</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #059669;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Success</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#059669</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg shadow-sm" style="background-color: #DC2626;"></div>
                        <p class="text-xs mt-1 font-medium" style="color: #0F172A;">Error</p>
                        <p class="text-xs font-mono" style="color: #64748B;">#DC2626</p>
                    </div>
                </div>

                <!-- UI Components Preview - ALL TEXT VISIBLE -->
                <div class="rounded-lg p-6 space-y-4" style="background-color: #FFFFFF; border: 2px solid #CBD5E1;">
                    <h3 class="text-lg font-semibold" style="color: #0F172A;">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="px-6 py-2 rounded-lg font-medium transition hover:opacity-90" style="background-color: #0F172A; color: #FFFFFF;">Primary Button</button>
                        <button class="px-6 py-2 rounded-lg font-medium transition hover:opacity-90" style="background-color: #F97316; color: #FFFFFF;">Accent / CTA</button>
                        <button class="px-6 py-2 rounded-lg font-medium transition hover:opacity-90" style="background-color: #475569; color: #FFFFFF;">Secondary</button>
                        <button class="px-6 py-2 rounded-lg font-medium transition hover:opacity-90" style="background-color: #059669; color: #FFFFFF;">Available</button>
                        <button class="px-6 py-2 rounded-lg font-medium transition hover:opacity-90" style="background-color: #DC2626; color: #FFFFFF;">Delete</button>
                    </div>
                    <div class="p-4 rounded-lg" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <h4 class="font-semibold" style="color: #0F172A;">Listing Card Title</h4>
                        <p class="text-sm mt-1" style="color: #475569;">Sample description text in secondary color</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="font-bold text-lg" style="color: #F97316;">₱15,000</span>
                            <span class="text-sm" style="color: #475569;">/month</span>
                            <svg class="w-5 h-5 ml-auto" style="color: #DC2626;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 2: Tropical Modern -->
        <section class="mb-12 scheme-preview scheme-tropical rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">2. Tropical Modern</h2>
                <p class="text-secondary text-sm mt-1">Philippines-inspired, warm, inviting</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#0369A1] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#0369A1</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#78716C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#78716C</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#EA580C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#EA580C</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FEF7ED] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#FEF7ED</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FED7AA] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#FED7AA</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#15803D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#15803D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#DC2626] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#DC2626</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-success px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Available</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Beachfront Villa</h4>
                        <p class="text-secondary text-sm mt-1">Cebu City, Philippines</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱25,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 3: Minimal Black & White -->
        <section class="mb-12 scheme-preview scheme-minimal rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">3. Minimal Black & White</h2>
                <p class="text-secondary text-sm mt-1">Ultra-clean, luxury aesthetic</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#000000] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#000000</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#404040] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#404040</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#3B82F6] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#3B82F6</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F5F5F5] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#F5F5F5</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#E5E5E5] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#E5E5E5</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#16A34A] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#16A34A</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#DC2626] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#DC2626</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="border border-black text-black px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition">Secondary</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Link Style</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg border-2 border-black">
                        <h4 class="font-semibold text-primary uppercase tracking-wide">Luxury Apartment</h4>
                        <p class="text-secondary text-sm mt-1">Makati, Metro Manila</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱45,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 4: Earthy Organic -->
        <section class="mb-12 scheme-preview scheme-earthy rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">4. Earthy Organic</h2>
                <p class="text-secondary text-sm mt-1">Warm, approachable, eco-friendly vibe</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#166534] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#166534</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#57534E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#57534E</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#C2410C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#C2410C</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F5F5F4] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#F5F5F4</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FAFAF9] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FAFAF9</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#D6D3D1] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#D6D3D1</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#84CC16] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#84CC16</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#B91C1C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#B91C1C</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-success px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Eco Available</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Garden Villa</h4>
                        <p class="text-secondary text-sm mt-1">Tagaytay, Cavite</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱20,000</span>
                            <span class="text-secondary text-sm">/month</span>
                            <span class="ml-auto px-2 py-1 bg-[#84CC16] text-white text-xs rounded-full">Eco-friendly</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 5: Dark Mode Modern -->
        <section class="mb-12 scheme-preview scheme-dark rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">5. Dark Mode Modern</h2>
                <p class="text-secondary text-sm mt-1">Sleek, reduces eye strain, trendy</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F8FAFC] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#F8FAFC</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#94A3B8] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#94A3B8</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#3B82F6] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#3B82F6</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#0F172A] shadow-sm border border-gray-700"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#0F172A</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#1E293B] shadow-sm border border-gray-700"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#1E293B</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#334155] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#334155</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#22C55E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#22C55E</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F87171] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#F87171</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary text-black px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Modern Condo</h4>
                        <p class="text-secondary text-sm mt-1">BGC, Taguig</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱35,000</span>
                            <span class="text-secondary text-sm">/month</span>
                            <span class="ml-auto w-2 h-2 bg-[#22C55E] rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 6: Purple Haze -->
        <section class="mb-12 scheme-preview scheme-purple rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">6. Purple Haze</h2>
                <p class="text-secondary text-sm mt-1">Creative, modern, slightly playful</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#6B21A8] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#6B21A8</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#7C3AED] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#7C3AED</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#EC4899] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#EC4899</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FAF5FF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#FAF5FF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#E9D5FF] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#E9D5FF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#22C55E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#22C55E</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#EF4444] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#EF4444</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Creative Studio Loft</h4>
                        <p class="text-secondary text-sm mt-1">Quezon City, Metro Manila</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱18,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 7: Ocean Breeze -->
        <section class="mb-12 scheme-preview scheme-ocean rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">7. Ocean Breeze</h2>
                <p class="text-secondary text-sm mt-1">Fresh, calming, coastal feel</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#0C4A6E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#0C4A6E</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#38BDF8] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#38BDF8</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#06B6D4] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#06B6D4</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F0F9FF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#F0F9FF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#BAE6FD] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#BAE6FD</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#14B8A6] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#14B8A6</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F43F5E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#F43F5E</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Seaside Condo</h4>
                        <p class="text-secondary text-sm mt-1">Cebu City, Philippines</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱28,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 8: Sunset Boulevard -->
        <section class="mb-12 scheme-preview scheme-sunset rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">8. Sunset Boulevard</h2>
                <p class="text-secondary text-sm mt-1">Warm, energetic, Instagram-worthy</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#BE123C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#BE123C</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FB7185] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#FB7185</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F59E0B] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#F59E0B</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFF1F2] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#FFF1F2</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FECDD3] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#FECDD3</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#65A30D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#65A30D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#E11D48] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#E11D48</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Sunset View Apartment</h4>
                        <p class="text-secondary text-sm mt-1">Manila Bay Area</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱32,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 9: Forest Retreat -->
        <section class="mb-12 scheme-preview scheme-forest rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">9. Forest Retreat</h2>
                <p class="text-secondary text-sm mt-1">Nature-focused, calming, sustainable vibe</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#14532D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#14532D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#65A30D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#65A30D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#D97706] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#D97706</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F7FEE7] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#F7FEE7</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#D9F99D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#D9F99D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#16A34A] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#16A34A</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#B91C1C] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#B91C1C</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-success px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Eco Badge</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Eco-Friendly Home</h4>
                        <p class="text-secondary text-sm mt-1">Baguio City, Benguet</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱22,000</span>
                            <span class="text-secondary text-sm">/month</span>
                            <span class="ml-auto px-2 py-1 bg-[#16A34A] text-white text-xs rounded-full">Sustainable</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 10: Midnight City -->
        <section class="mb-12 scheme-preview scheme-midnight rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">10. Midnight City</h2>
                <p class="text-secondary text-sm mt-1">Sophisticated dark, neon accents</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#E2E8F0] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#E2E8F0</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#94A3B8] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#94A3B8</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#A855F7] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#A855F7</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#020617] shadow-sm border border-gray-700"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#020617</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#0F172A] shadow-sm border border-gray-700"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#0F172A</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#1E293B] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#1E293B</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#10B981] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#10B981</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F43F5E] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#F43F5E</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary text-black px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-success px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Neon Success</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">High-Rise Penthouse</h4>
                        <p class="text-secondary text-sm mt-1">Makati, Metro Manila</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱85,000</span>
                            <span class="text-secondary text-sm">/month</span>
                            <span class="ml-auto w-2 h-2 bg-[#10B981] rounded-full animate-pulse"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 11: Coral Reef -->
        <section class="mb-12 scheme-preview scheme-coral rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">11. Coral Reef</h2>
                <p class="text-secondary text-sm mt-1">Friendly, approachable, tropical</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#0D9488] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#0D9488</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#2DD4BF] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#2DD4BF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F97316] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#F97316</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#F0FDFA] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#F0FDFA</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#99F6E4] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#99F6E4</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#84CC16] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#84CC16</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#EF4444] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#EF4444</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Accent / CTA</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Beach Cottage</h4>
                        <p class="text-secondary text-sm mt-1">Boracay, Aklan</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg">₱25,000</span>
                            <span class="text-secondary text-sm">/month</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scheme 12: Rose Gold Luxury -->
        <section class="mb-12 scheme-preview scheme-rose rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-[var(--border)]">
                <h2 class="text-2xl font-bold text-primary">12. Rose Gold Luxury</h2>
                <p class="text-secondary text-sm mt-1">Elegant, premium, feminine touch</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 md:grid-cols-8 gap-3 mb-6">
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#881337] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Primary</p>
                        <p class="text-xs font-mono text-secondary">#881337</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FDA4AF] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Secondary</p>
                        <p class="text-xs font-mono text-secondary">#FDA4AF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FDE047] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Accent</p>
                        <p class="text-xs font-mono text-secondary">#FDE047</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFF1F2] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Background</p>
                        <p class="text-xs font-mono text-secondary">#FFF1F2</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FFFFFF] shadow-sm border border-gray-200"></div>
                        <p class="text-xs mt-1 text-secondary">Surface</p>
                        <p class="text-xs font-mono text-secondary">#FFFFFF</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#FECDD3] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Border</p>
                        <p class="text-xs font-mono text-secondary">#FECDD3</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#65A30D] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Success</p>
                        <p class="text-xs font-mono text-secondary">#65A30D</p>
                    </div>
                    <div class="text-center">
                        <div class="w-full h-16 rounded-lg bg-[#E11D48] shadow-sm"></div>
                        <p class="text-xs mt-1 text-secondary">Error</p>
                        <p class="text-xs font-mono text-secondary">#E11D48</p>
                    </div>
                </div>
                <div class="preview-card rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-primary">Sample Components</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="preview-primary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Primary Button</button>
                        <button class="preview-accent text-black px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Gold Accent</button>
                        <button class="preview-secondary px-6 py-2 rounded-lg font-medium hover:opacity-90 transition">Secondary</button>
                    </div>
                    <div class="preview-card p-4 rounded-lg">
                        <h4 class="font-semibold text-primary">Luxury Suite</h4>
                        <p class="text-secondary text-sm mt-1">Rockwell, Makati</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-accent font-bold text-lg text-[#D97706]">₱55,000</span>
                            <span class="text-secondary text-sm">/month</span>
                            <span class="ml-auto text-[#FDE047]">★★★★★</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Implementation Guide -->
        <section class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-slate-900 mb-4">Quick Implementation</h2>
            <p class="text-slate-600 mb-4">Add this CSS to your <code>header.php</code> or create a new <code>colors.css</code> file:</p>
            <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;style&gt;
  :root {
    --color-primary: #0F172A;    /* Choose your scheme */
    --color-secondary: #475569;
    --color-accent: #F97316;
    --color-bg: #FAFAFA;
    --color-surface: #FFFFFF;
    --color-border: #E2E8F0;
    --color-success: #10B981;
    --color-error: #EF4444;
  }

  /* Tailwind-style usage */
  .btn-primary { 
    @apply bg-[var(--color-primary)] text-white; 
  }
  .btn-accent { 
    @apply bg-[var(--color-accent)] text-white; 
  }
  .card { 
    @apply bg-[var(--color-surface)] border border-[var(--color-border)]; 
  }
&lt;/style&gt;</code></pre>
        </section>

        <footer class="text-center text-slate-500 mt-12 text-sm">
            <p>Dominium Color Scheme Showcase • Pick a palette that matches your brand</p>
        </footer>
    </div>
</body>
</html>
