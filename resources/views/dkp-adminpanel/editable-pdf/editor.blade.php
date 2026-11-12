<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Interactive PDF Workbook Editor</title>

    <!-- Tailwind CSS (CDN for standalone editor UI) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- PDF.js 3.11.174 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <!-- pdf-lib (Local asset + CDN fallback) -->
    <script src="{{ asset('vendor/pdf-lib/pdf-lib.min.js') }}"></script>
    <script>
        if (typeof PDFLib === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"><\/script>');
        }
    </script>

    <!-- PDF Editor Styles -->
    <link rel="stylesheet" href="{{ asset('css/pdf-editor.css') }}">
</head>
<body class="bg-slate-100 flex flex-col h-screen overflow-hidden text-slate-800">

    <!-- Header Toolbar -->
    <header class="bg-white border-b border-slate-200 px-4 py-2 flex items-center justify-between shadow-sm z-40">
        <div class="flex items-center space-x-3">
            <a href="{{ route('materials.dashboard') }}" class="p-2 rounded hover:bg-slate-100 text-slate-600 border border-slate-200" title="Back to Documents">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-red-600"></i>
                    <span class="truncate max-w-sm" title="{{ $filename }}">{{ $filename }}</span>
                </h1>
                <span id="docStatusBadge" class="text-[11px] text-blue-600 font-medium">
                    {{ $hasSavedVersion ? '● Saved Copy Loaded' : '● Original Master Document' }}
                </span>
            </div>
        </div>

        <!-- Controls: Page, Zoom, Tools -->
        <div class="flex items-center space-x-2">
            <button id="prevPageBtn" class="p-2 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-40" title="Previous Page">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="flex items-center text-sm font-medium text-slate-600">
                <input id="pageNumberInput" type="number" min="1" value="1" class="w-14 text-center border rounded py-1 px-1 text-sm mr-1 font-semibold">
                <span>/ <span id="pageCount">1</span></span>
            </div>
            <button id="nextPageBtn" class="p-2 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-40" title="Next Page">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="h-5 border-r border-slate-300 mx-2"></div>

            <button id="zoomOutBtn" class="p-2 rounded hover:bg-slate-100 text-slate-600" title="Zoom Out">
                <i class="fa-solid fa-magnifying-glass-minus"></i>
            </button>
            <span id="zoomLevel" class="text-xs font-semibold text-slate-500 w-12 text-center">100%</span>
            <button id="zoomInBtn" class="p-2 rounded hover:bg-slate-100 text-slate-600" title="Zoom In">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
            </button>

            <div class="h-5 border-r border-slate-300 mx-2"></div>

            <button id="addTextToolBtn" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100 transition shadow-sm" title="Add a custom freeform note box anywhere">
                <i class="fa-solid fa-i-cursor"></i>
                <span>Add Extra Note</span>
            </button>
        </div>

        <!-- Actions: Save & Download -->
        <div class="flex items-center space-x-3">
            <button id="saveBtn" class="flex items-center gap-2 px-4 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm transition shadow-sm">
                <i class="fa-solid fa-floppy-disk"></i>
                <span id="saveBtnText">Save Copy</span>
            </button>
            <a href="{{ $downloadUrl }}" id="downloadBtn" class="flex items-center gap-2 px-3 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm transition border border-slate-300">
                <i class="fa-solid fa-download"></i>
                <span>Download</span>
            </a>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 flex overflow-hidden">
        <!-- Sidebar Thumbnails -->
        <aside id="thumbnailSidebar" class="w-48 bg-slate-50 border-r border-slate-200 overflow-y-auto p-3 flex flex-col items-center gap-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-full text-left pl-1">Pages</div>
            <div id="thumbnailsContainer" class="flex flex-col gap-3 w-full"></div>
        </aside>

        <!-- Canvas Area -->
        <section id="viewportContainer" class="flex-1 overflow-auto bg-slate-200 flex justify-center p-6 relative">
            <div id="canvasWrapper" class="pdf-canvas-container relative self-start">
                <canvas id="pdfCanvas" class="block"></canvas>
                <!-- Native interactive form fields overlay -->
                <div id="formFieldsLayer" class="form-fields-layer"></div>
                <!-- Extra freeform annotations -->
                <div id="customAnnotationLayer" class="absolute inset-0 pointer-events-none"></div>
            </div>
        </section>
    </main>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-5 right-5 bg-slate-900 text-white px-4 py-2.5 rounded-lg shadow-xl text-sm flex items-center gap-2 transform translate-y-20 opacity-0 transition duration-300 z-50">
        <i id="toastIcon" class="fa-solid fa-circle-check text-green-400"></i>
        <span id="toastMsg">Changes saved</span>
    </div>

    <!-- PDF Editor Configuration and Script -->
    <script>
        window.PDF_EDITOR_CONFIG = {
            pdfUrl: @json($loadUrl),
            saveUrl: @json($saveUrl),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            userFullName: @json($userFullName ?? ''),
            userEmail: @json($userEmail ?? ''),
            logoUrl: @json($logoUrl ?? ''),
            filename: @json($filename ?? 'document.pdf')
        };
    </script>
    <script src="{{ asset('js/pdf-editor.js') }}"></script>

    <script>
        window.__FLASH__ = {
            success: @json(session('success')),
            error: @json(session('error')),
            warning: @json(session('warning')),
            info: @json(session('info')),
            errors: @json($errors->all())
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="{{ asset('js/dkp-sweetalert.js') }}" defer></script>
</body>
</html>
