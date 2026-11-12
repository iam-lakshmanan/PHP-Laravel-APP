<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available PDF Documents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-red-600"></i>
                    PDF Workbooks & Documents
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Select a document to edit online, resume your previously saved work, or download a copy.
                </p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fa-solid fa-folder-open mr-1.5"></i>
                    Master Documents Folder
                </span>
            </div>
        </div>

        <!-- Documents Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800">Available PDF Workbooks</h2>
                <span class="text-xs text-slate-500">{{ count($documents) }} document(s) found</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/60 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-6">Document Name</th>
                            <th class="py-3 px-6">File Size</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Last Edited</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($documents as $doc)
                            <tr class="hover:bg-slate-50/80 transition duration-150">
                                <td class="py-4 px-6 font-medium text-slate-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 text-lg">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $doc['filename'] }}</p>
                                        <p class="text-xs text-slate-400">Original Master Copy</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $doc['size'] }}
                                </td>
                                <td class="py-4 px-6">
                                    @if ($doc['has_edited'])
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Edited Copy Saved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Unmodified Original
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs">
                                    @if ($doc['has_edited'])
                                        <span class="font-medium text-slate-700">{{ $doc['edited_date'] }}</span>
                                    @else
                                        <span class="text-slate-400">Not modified yet</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ $doc['editor_url'] }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>{{ $doc['has_edited'] ? 'Resume Edit' : 'Open in Editor' }}</span>
                                    </a>
                                    <a href="{{ $doc['download_url'] }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition">
                                        <i class="fa-solid fa-download text-xs"></i>
                                        <span>Download</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    No PDF documents found in <code>storage/app/private/documents/originals/</code>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
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
