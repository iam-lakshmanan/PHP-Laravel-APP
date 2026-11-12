
@include('dkp-adminpanel.dashboard.layout')
<body class="flex flex-col justify-between dashboard-bg min-h-screen text-[#1e293b]">

    {{-- Main Container --}}
    <div class="w-full flex-1 flex flex-col px-4 sm:px-8 lg:px-12 py-6 sm:py-8 max-w-[1400px] mx-auto">

        {{-- Top Floating Header Card --}}
        <header
            class="w-full bg-white rounded-2xl sm:rounded-[24px] px-6 sm:px-10 py-4 flex items-center justify-between shadow-[0_2px_12px_rgba(0,0,0,0.03)] border border-[#e8eff5] mb-8">
            {{-- Procept Logo --}}
            @php
                $host = request()->getHost();

                if (str_contains($host, 'barnesconti.com')) {
                    $logo = 'img/barnesconti-logo.png';
                    $alt = 'Barnes & Conti';
                } else {
                    $logo = 'img/procept-logo.png';
                    $alt = 'Procept Associates Ltd.';
                }
            @endphp

            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset($logo) }}" alt="{{ $alt }}" class="h-10 sm:h-12 w-auto object-contain">
            </a>

            {{-- Logout Button --}}
            <a href="{{ route('materials.logout') }}"
                class="inline-flex items-center gap-2.5 px-5 sm:px-6 py-2 rounded-full border border-[#034b82] text-[#034b82] text-sm font-semibold hover:bg-[#034b82] hover:text-white transition duration-200">
                <span>Logout</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </header>

        {{-- Section Title & Search Bar --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-bold text-[#034b82] tracking-tight">
                    Documents
                </h1>
                <p class="text-sm sm:text-[15px] text-[#475569] mt-1 font-normal">
                    Your materials have been watermarked with your name and email address and are now available for download.
                </p>
            </div>

            {{-- Search Input --}}
            <div class="relative w-full md:w-[280px]">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94a3b8]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="documentSearch" placeholder="Search"
                    class="w-full bg-white rounded-full border border-[#cbd5e1] pl-10 pr-4 py-2 text-sm text-[#334155] placeholder-[#94a3b8] focus:outline-none focus:border-[#034b82] focus:ring-1 focus:ring-[#034b82]/30 shadow-sm transition" />
            </div>
        </div>

        {{-- Main Documents Card --}}
        <div
            class="bg-white rounded-[24px] sm:rounded-[28px] border border-[#e2e8f0] p-6 sm:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.02)] mb-8 flex-1 flex flex-col justify-between">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-[#eef4f9] text-[13px] font-bold text-[#334155] tracking-wider text-left">
                            <th class="py-3.5 px-6 rounded-l-xl w-20">SL NO</th>
                            <th class="py-3.5 px-6">NAME</th>
                            <th class="py-3.5 px-6">DATE</th>
                            <th class="py-3.5 px-6">STATUS</th>
                            <th class="py-3.5 px-6 text-center rounded-r-xl w-48">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="documentTableBody" class="divide-y divide-[#f1f5f9] text-sm">

                        @php
                            $hasRealDocuments = isset($documents) && count($documents) > 0;
                            // Sample items matching exact visual design as fallback
                        @endphp

                        @if ($hasRealDocuments)
                            @foreach ($documents as $index => $doc)
                                @php
                                    $docTitle = !empty($doc->title)
                                        ? $doc->title
                                        : 'Document-' . $doc->document_id . '.pdf';
                                    if (!str_ends_with(strtolower($docTitle), '.pdf')) {
                                        $docTitle .= '.pdf';
                                    }
                                    $createdDate = !empty($doc->created)
                                        ? (is_numeric($doc->created)
                                            ? date('M d,Y', (int) $doc->created)
                                            : date('M d,Y', strtotime($doc->created)))
                                        : date('M d,Y');
                                    $isEditable = !empty($doc->editable_files);
                                    $isPrintable = !empty($doc->printable);
                                @endphp
                                <tr class="document-row hover:bg-[#f8fafc] transition duration-150">
                                    {{-- SL NO --}}
                                    <td class="py-4 px-6 text-[#334155] font-medium text-sm">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- NAME with Red PDF Icon --}}
                                    <td class="py-4 px-6 text-[#334155] font-medium text-sm">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-[#ef4444] flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M7 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 12.172 2H7zm0 2h5v4a1 1 0 0 0 1 1h4v11a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm1 9a1 1 0 0 1 1-1h1.5a1.5 1.5 0 0 1 0 3H9v1a1 1 0 1 1-2 0v-3zm3 0a1 1 0 0 1 1-1h1.5a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5H12v-3z" />
                                            </svg>
                                            <span class="document-title truncate max-w-sm sm:max-w-md lg:max-w-xl">
                                                {{ $docTitle }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- DATE --}}
                                    <td class="py-4 px-6 text-[#475569] text-sm whitespace-nowrap">
                                        {{ $createdDate }}
                                    </td>

                                    {{-- STATUS BADGE --}}
                                    @php
                                        $userEmailPrefix = preg_replace('/@.*$/', '', session('dkp_user.email') ?: 'user');
                                        $cleanPrefix = preg_replace('/[^A-Za-z0-9_-]/', '', $userEmailPrefix);
                                        $boxTargetName = strtolower($cleanPrefix . '__' . $docTitle);
                                        $hasEditedFile = in_array($boxTargetName, $editedFileNames ?? []);
                                    @endphp
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if ($hasEditedFile)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#ffedd5] text-[#d97706]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                                Edited
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#dcfce7] text-[#16a34a]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                                Original
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACTION BUTTONS --}}
                                    <td class="py-4 px-6 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            @if ($isEditable)
                                                <a href="{{ route('materials.editor', ['document' => $doc->document_id]) }}"
                                                    title="Edit Document"
                                                    class="inline-flex items-center justify-center transition-transform hover:scale-105 active:scale-95 focus:outline-none">
                                                    <img src="{{ asset('img/dashboard/icon-edit.png') }}"
                                                        alt="Edit"
                                                        class="w-[36px] h-[36px] sm:w-[38px] sm:h-[38px] object-contain pointer-events-none select-none">
                                                </a>
                                            @endif

                                            @if ($isPrintable)
                                                <button type="button"
                                                    onclick="downloadWatermarkedPdf('{{ route('materials.load', ['document' => $doc->document_id]) }}', '{{ $docTitle }}')"
                                                    title="Download Document"
                                                    class="inline-flex items-center justify-center transition-transform hover:scale-105 active:scale-95 focus:outline-none bg-transparent border-0 p-0 cursor-pointer">
                                                    <img src="{{ asset('img/dashboard/icon-download.png') }}"
                                                        alt="Download"
                                                        class="w-[36px] h-[36px] sm:w-[38px] sm:h-[38px] object-contain pointer-events-none select-none">
                                                </button>
                                            @endif

                                            @if ($hasEditedFile)
                                                <button type="button" title="Restore Original PDF"
                                                    onclick="confirmRestoreOriginal('{{ $doc->document_id }}', '{{ route('materials.revert', $doc->document_id) }}')"
                                                    class="inline-flex items-center justify-center transition-transform hover:scale-105 active:scale-95 focus:outline-none bg-transparent border-0 p-0 cursor-pointer">
                                                    <img src="{{ asset('img/dashboard/icon-revert.png') }}"
                                                        alt="Restore"
                                                        class="w-[36px] h-[36px] sm:w-[38px] sm:h-[38px] object-contain pointer-events-none select-none">
                                                </button>
                                            @endif

                                            @if (!$isEditable && !$isPrintable && !$hasEditedFile)
                                                <span class="text-xs text-[#94a3b8] font-medium">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5"
                                    class="py-12 px-6 text-center text-[#64748b] font-medium text-sm sm:text-base">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-[#94a3b8]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>No PDF is available</span>
                                    </div>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            {{-- Pagination Matching Design --}}
            @if (isset($documents) && method_exists($documents, 'hasPages') && $documents->hasPages())
                <div class="flex items-center justify-end gap-1.5 mt-8 pt-4">
                    {{-- Previous Page Link --}}
                    @if ($documents->onFirstPage())
                        <span
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border border-[#e2e8f0] flex items-center justify-center text-slate-300 cursor-not-allowed select-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" aria-label="Previous Page"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border border-[#e2e8f0] flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($documents->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span
                                class="px-1.5 text-slate-400 text-xs sm:text-sm font-semibold select-none">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $documents->currentPage())
                                    <span
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-md bg-[#034b82] text-white font-semibold text-xs sm:text-sm flex items-center justify-center shadow-xs select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border border-[#e2e8f0] text-slate-600 text-xs sm:text-sm flex items-center justify-center hover:bg-slate-50 transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" aria-label="Next Page"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border border-[#e2e8f0] flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border border-[#e2e8f0] flex items-center justify-center text-slate-300 cursor-not-allowed select-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    {{-- Bottom Full-Width Footer --}}
    @php
        $isBarnesConti = str_contains(request()->getHost(), 'barnesconti.com');

        $logo = $isBarnesConti ? 'img/barnesconti-logo.png' : 'img/procept-logo.png';

        $alt = $isBarnesConti ? 'Barnes & Conti' : 'Procept Associates Ltd.';

        $copyright = $isBarnesConti
            ? '©2005-2026 Barnes & Conti. All rights reserved.'
            : '©2005-2026 Procept Associates Ltd. All rights reserved.';
    @endphp

    <footer class="w-full bg-white border-t border-[#e2e8f0] py-4 px-6 sm:px-12 mt-auto">
        <div class="max-w-[1400px] mx-auto flex items-center gap-6">

            {{-- Dynamic Logo --}}
            <img src="{{ asset($logo) }}" alt="{{ $alt }}" class="h-8 sm:h-9 w-auto object-contain">

            {{-- Divider --}}
            <div class="h-6 w-px bg-slate-300"></div>

            {{-- Dynamic Copyright --}}
            <p class="text-xs sm:text-sm text-slate-600">
                {{ $copyright }}
            </p>

        </div>
    </footer>

    {{-- Live Search & Action Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('documentSearch');
            const rows = document.querySelectorAll('.document-row');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();

                    rows.forEach(function(row) {
                        const titleEl = row.querySelector('.document-title');
                        const text = titleEl ? titleEl.textContent.toLowerCase() : '';

                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });

        function confirmRestoreOriginal(documentId, revertUrl) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    html: `
                        <div class="flex flex-col items-center px-2 py-4">
                            <!-- Circular Refresh Red Icon -->
                            <div class="w-16 h-16 rounded-full bg-[#fee2e2] flex items-center justify-center mb-5 text-[#ef4444]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <!-- Title -->
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2.5 text-center">
                                Restore Original PDF?
                            </h2>
                            <!-- Subtitle / Warning -->
                            <p class="text-sm sm:text-base text-slate-600 text-center max-w-md leading-relaxed mb-6">
                                Are you sure you want to restore this PDF to its original version? All edits made to the current PDF will be reverted, and your changes may be lost.
                            </p>
                        </div>
                    `,
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: 'Restore & Continue',
                    cancelButtonText: 'Cancel',
                    reverseButtons: false,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-[24px] p-6 max-w-lg',
                        closeButton: 'text-slate-400 hover:text-slate-600 top-4 right-4 focus:outline-none',
                        actions: 'flex items-center justify-center gap-3 w-full pb-2',
                        cancelButton: 'px-6 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition min-w-[130px] text-center',
                        confirmButton: 'px-6 py-2.5 rounded-xl bg-[#e11d48] text-white font-semibold hover:bg-[#be123c] transition shadow-sm min-w-[170px] text-center'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call server to remove local edit & delete from Box
                        fetch(revertUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (window.Toast) {
                                    window.Toast.fire({
                                        icon: 'success',
                                        title: 'Restored to original version'
                                    });
                                }
                                setTimeout(() => {
                                    window.location.reload();
                                }, 800);
                            })
                            .catch(err => {
                                window.location.reload();
                            });
                    }
                });
            } else {
                if (confirm('Are you sure you want to restore this PDF to its original version?')) {
                    fetch(revertUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => window.location.reload());
                }
            }
        }

        const watermarkConfig = {
            userFullName: @json($userFullName ?? ($user['full_name'] ?? 'John Smith')),
            userEmail: @json($userEmail ?? ($user['email'] ?? 'jsmith@email.com')),
            logoUrl: @json($logoUrl ?? asset('img/procept-logo.png')),
        };

        async function downloadWatermarkedPdf(loadUrl, filename) {
            // Show loading alert
            let timerInterval;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Preparing Document',
                    html: 'Adding personalized watermark and cover page...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            try {
                const response = await fetch(loadUrl);
                if (!response.ok) throw new Error('Could not download PDF from server.');
                const rawBytes = await response.arrayBuffer();

                const {
                    PDFDocument,
                    rgb,
                    StandardFonts
                } = PDFLib;
                const pdfDocLib = await PDFDocument.load(rawBytes, {
                    ignoreEncryption: true
                });

                const rawKw = pdfDocLib.getKeywords();
                const kwStr = Array.isArray(rawKw) ? rawKw.join(' ') : (rawKw || '');
                const titleStr = pdfDocLib.getTitle() || '';
                const authorStr = pdfDocLib.getAuthor() || '';
                const metaStr = (kwStr + ' ' + titleStr + ' ' + authorStr).toUpperCase();
                const isAlreadyCovered = metaStr.includes('PROCEPT') || metaStr.includes('COVER');

                let finalBytes;

                if (isAlreadyCovered) {
                    finalBytes = rawBytes;
                } else {
                    const helvetica = await pdfDocLib.embedFont(StandardFonts.Helvetica);
                    const helveticaBold = await pdfDocLib.embedFont(StandardFonts.HelveticaBold);

                    const nameT = watermarkConfig.userFullName || 'John Smith';
                    const emailT = watermarkConfig.userEmail || 'jsmith@email.com';

                    pdfDocLib.setKeywords(['PROCEPT_COVER', 'PROCEPT_PERSONALIZED_COVER']);

                    // Create Cover Page as Page 1
                    const coverPage = pdfDocLib.insertPage(0, [612, 792]);
                    const {
                        width,
                        height
                    } = coverPage.getSize();

                    // Background
                    coverPage.drawRectangle({
                        x: 0,
                        y: 0,
                        width: width,
                        height: height,
                        color: rgb(244 / 255, 248 / 255, 252 / 255),
                    });

                    // Top Logo
                    try {
                        const logoResp = await fetch(watermarkConfig.logoUrl);
                        if (logoResp.ok) {
                            const logoBytes = await logoResp.arrayBuffer();
                            const logoImg = await pdfDocLib.embedPng(logoBytes);
                            const logoDims = logoImg.scale(0.38);
                            coverPage.drawImage(logoImg, {
                                x: (width - logoDims.width) / 2,
                                y: height - 160,
                                width: logoDims.width,
                                height: logoDims.height,
                            });
                        }
                    } catch (imgErr) {
                        console.warn('Could not load logo for cover page:', imgErr);
                    }

                    // Card Dimensions
                    const cardWidth = 470;
                    const cardHeight = 220;
                    const cardX = (width - cardWidth) / 2;
                    const cardY = height - 440;

                    coverPage.drawRectangle({
                        x: cardX,
                        y: cardY,
                        width: cardWidth,
                        height: cardHeight,
                        color: rgb(234 / 255, 244 / 255, 252 / 255),
                        borderColor: rgb(203 / 255, 227 / 255, 248 / 255),
                        borderWidth: 1.2,
                    });

                    const t0 = 'This PDF file is issued';
                    coverPage.drawText(t0, {
                        x: (width - helvetica.widthOfTextAtSize(t0, 15)) / 2,
                        y: cardY + 165,
                        size: 15,
                        font: helvetica,
                        color: rgb(0.2, 0.25, 0.3),
                    });

                    const t0sub = 'exclusively to';
                    coverPage.drawText(t0sub, {
                        x: (width - helvetica.widthOfTextAtSize(t0sub, 15)) / 2,
                        y: cardY + 144,
                        size: 15,
                        font: helvetica,
                        color: rgb(0.2, 0.25, 0.3),
                    });

                    const tUser = nameT;
                    coverPage.drawText(tUser, {
                        x: (width - helveticaBold.widthOfTextAtSize(tUser, 19)) / 2,
                        y: cardY + 105,
                        size: 19,
                        font: helveticaBold,
                        color: rgb(0 / 255, 56 / 255, 118 / 255),
                    });

                    const tEmail = `(${emailT})`;
                    coverPage.drawText(tEmail, {
                        x: (width - helveticaBold.widthOfTextAtSize(tEmail, 14)) / 2,
                        y: cardY + 82,
                        size: 14,
                        font: helveticaBold,
                        color: rgb(0 / 255, 56 / 255, 118 / 255),
                    });

                    const t2 = 'Duplication and further distribution are prohibited.';
                    coverPage.drawText(t2, {
                        x: (width - helvetica.widthOfTextAtSize(t2, 12)) / 2,
                        y: cardY + 45,
                        size: 12,
                        font: helvetica,
                        color: rgb(0.35, 0.4, 0.45),
                    });

                    // Bottom Section: Lock Circle & Confidential Material
                    const circleRadius = 24;
                    const circleCenterX = width / 2;
                    const circleCenterY = height - 540;

                    coverPage.drawCircle({
                        x: circleCenterX,
                        y: circleCenterY,
                        size: circleRadius,
                        color: rgb(0 / 255, 56 / 255, 118 / 255),
                    });

                    coverPage.drawRectangle({
                        x: circleCenterX - 7,
                        y: circleCenterY + 1,
                        width: 14,
                        height: 14,
                        borderColor: rgb(1, 1, 1),
                        borderWidth: 2.8,
                        color: rgb(0 / 255, 56 / 255, 118 / 255),
                    });
                    coverPage.drawRectangle({
                        x: circleCenterX - 11,
                        y: circleCenterY - 11,
                        width: 22,
                        height: 16,
                        color: rgb(1, 1, 1),
                    });
                    coverPage.drawCircle({
                        x: circleCenterX,
                        y: circleCenterY - 3,
                        size: 2.5,
                        color: rgb(0 / 255, 56 / 255, 118 / 255),
                    });

                    coverPage.drawLine({
                        start: {
                            x: (width - 320) / 2,
                            y: height - 595
                        },
                        end: {
                            x: (width + 320) / 2,
                            y: height - 595
                        },
                        thickness: 1,
                        color: rgb(0.8, 0.85, 0.9),
                    });

                    const confTitle = 'PROCEPT';
                    coverPage.drawText(confTitle, {
                        x: (width - helveticaBold.widthOfTextAtSize(confTitle, 11)) / 2,
                        y: height - 615,
                        size: 11,
                        font: helveticaBold,
                        color: rgb(0.35, 0.4, 0.45),
                    });

                    const confSub = 'CONFIDENTIAL MATERIAL';
                    coverPage.drawText(confSub, {
                        x: (width - helveticaBold.widthOfTextAtSize(confSub, 9)) / 2,
                        y: height - 632,
                        size: 9,
                        font: helveticaBold,
                        color: rgb(0.45, 0.5, 0.55),
                    });

                    // Disclaimer on all subsequent pages
                    const allPages = pdfDocLib.getPages();
                    const disclaimerLine1 = `This PDF file is issued exclusively to ${nameT} (${emailT}).`;
                    const disclaimerLine2 = 'Duplication and further distribution are prohibited.';
                    const textFontSize = 6.8;

                    for (let i = 1; i < allPages.length; i++) {
                        const page = allPages[i];
                        const pWidth = page.getWidth();

                        const w1 = helveticaBold.widthOfTextAtSize(disclaimerLine1, textFontSize);
                        const w2 = helvetica.widthOfTextAtSize(disclaimerLine2, textFontSize);

                        const rightMargin = 45;
                        const x1 = Math.max(40, pWidth - rightMargin - w1);
                        const x2 = Math.max(40, pWidth - rightMargin - w2);

                        page.drawText(disclaimerLine1, {
                            x: x1,
                            y: 47,
                            size: textFontSize,
                            font: helveticaBold,
                            color: rgb(0.3, 0.3, 0.3),
                        });

                        page.drawText(disclaimerLine2, {
                            x: x2,
                            y: 38,
                            size: textFontSize,
                            font: helvetica,
                            color: rgb(0.4, 0.4, 0.4),
                        });
                    }

                    finalBytes = await pdfDocLib.save();
                }

                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                // Trigger browser file download
                const blob = new Blob([finalBytes], {
                    type: 'application/pdf'
                });
                const blobUrl = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = filename || 'document.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);

            } catch (err) {
                console.error('Download error:', err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Could not generate the watermarked document. Please try again.'
                    });
                } else {
                    alert('Could not download watermarked PDF.');
                }
            }
        }
    </script>

</body>

</html>
