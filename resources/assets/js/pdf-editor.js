/**
 * Interactive PDF Workbook Editor JS
 */
(function () {
    const config = window.PDF_EDITOR_CONFIG || {};

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = config.workerSrc || 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    const pdfUrl = config.pdfUrl || '';
    const saveUrl = config.saveUrl || '';
    const csrfToken = config.csrfToken || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');
    const userFullName = (config.userFullName || '').trim();
    const userEmail = (config.userEmail || '').trim();
    const logoUrl = config.logoUrl || '';
    const filename = config.filename || 'document.pdf';

    let pdfDoc = null;
    let pdfBytes = null;
    let currentPageNum = 1;
    let scale = 1.0;
    let isTextToolActive = false;

    // Stores interactive PDF form values: { [fieldName]: string }
    let formFieldValues = {};
    let dirtyFieldNames = new Set();

    // Stores custom freeform note boxes: { [pageNum]: [ { pdfX, pdfY, width, height, text, fontSize } ] }
    let customAnnotations = {};

    const canvas = document.getElementById('pdfCanvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    const formFieldsLayer = document.getElementById('formFieldsLayer');
    const customAnnotationLayer = document.getElementById('customAnnotationLayer');
    const canvasWrapper = document.getElementById('canvasWrapper');
    const pageCountEl = document.getElementById('pageCount');
    const pageNumberInput = document.getElementById('pageNumberInput');
    const zoomLevelEl = document.getElementById('zoomLevel');
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    const toastIcon = document.getElementById('toastIcon');

    function showToast(message, isError = false) {
        if (!toast || !toastMsg || !toastIcon) return;
        toastMsg.innerText = message;
        toastIcon.className = isError ? 'fa-solid fa-circle-xmark text-red-400' : 'fa-solid fa-circle-check text-green-400';
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }

    // Apply Page 1 Personalized Cover Page and Page 2..N Running Footers
    async function applyCoverPageAndFooters(bytes) {
        try {
            const { PDFDocument, rgb, StandardFonts } = PDFLib;
            const pdfDocLib = await PDFDocument.load(bytes, { ignoreEncryption: true });

            const rawKw = pdfDocLib.getKeywords();
            const kwStr = Array.isArray(rawKw) ? rawKw.join(' ') : (rawKw || '');
            const titleStr = pdfDocLib.getTitle() || '';
            const authorStr = pdfDocLib.getAuthor() || '';
            const metaStr = (kwStr + ' ' + titleStr + ' ' + authorStr).toUpperCase();

            // If the document already has our personalized cover page, remove the old one
            // so we always generate a fresh, accurate cover page with the current user's name & email
            const isAlreadyPersonalized = kwStr.toUpperCase().includes('PROCEPT_PERSONALIZED_COVER') ||
                                          kwStr.toUpperCase().includes('PROCEPT_COVER');
            if (isAlreadyPersonalized && pdfDocLib.getPageCount() > 1) {
                pdfDocLib.removePage(0);
            }

            pdfDocLib.setKeywords(['PROCEPT_COVER', 'PROCEPT_PERSONALIZED_COVER']);
            const helveticaBold = await pdfDocLib.embedFont(StandardFonts.HelveticaBold);
            const helvetica = await pdfDocLib.embedFont(StandardFonts.Helvetica);

            // 1. Insert Cover Page at Page 1 (Index 0) - Standard A4: 595.28 x 841.89
            const coverPage = pdfDocLib.insertPage(0, [595.28, 841.89]);
            const { width, height } = coverPage.getSize();

            // Logo or Title
            let logoDrawn = false;
            if (logoUrl) {
                try {
                    const logoResp = await fetch(logoUrl);
                    if (logoResp.ok) {
                        const logoArrayBuf = await logoResp.arrayBuffer();
                        const logoImg = await pdfDocLib.embedPng(logoArrayBuf);
                        const maxLogoWidth = 180;
                        const dims = logoImg.scale(maxLogoWidth / logoImg.width);
                        coverPage.drawImage(logoImg, {
                            x: (width - dims.width) / 2,
                            y: height - 135,
                            width: dims.width,
                            height: dims.height,
                        });
                        logoDrawn = true;
                    }
                } catch (le) {
                    console.log('Logo load note:', le);
                }
            }

            if (!logoDrawn) {
                const title = 'PROCEPT';
                const tw = helveticaBold.widthOfTextAtSize(title, 32);
                coverPage.drawText(title, {
                    x: (width - tw) / 2,
                    y: height - 120,
                    size: 32,
                    font: helveticaBold,
                    color: rgb(0 / 255, 56 / 255, 118 / 255),
                });
            }

            // Blue Accent Line
            coverPage.drawRectangle({
                x: (width - 120) / 2,
                y: height - 155,
                width: 120,
                height: 3.5,
                color: rgb(0 / 255, 102 / 255, 204 / 255),
            });

            // COURSE MATERIAL
            const subtitle = 'COURSE MATERIAL';
            const sw = helveticaBold.widthOfTextAtSize(subtitle, 16);
            coverPage.drawText(subtitle, {
                x: (width - sw) / 2,
                y: height - 185,
                size: 16,
                font: helveticaBold,
                color: rgb(13 / 255, 35 / 255, 68 / 255),
            });

            // Light Blue Notice Box (Rounded Card)
            const cardW = 430;
            const cardH = 200;
            const cardX = (width - cardW) / 2;
            const cardY = height - 425;

            coverPage.drawRectangle({
                x: cardX,
                y: cardY,
                width: cardW,
                height: cardH,
                color: rgb(234 / 255, 244 / 255, 252 / 255),
                borderColor: rgb(203 / 255, 227 / 255, 248 / 255),
                borderWidth: 1.5,
            });

            // Notice text inside card (Matching Image 1)
            const t1 = 'This PDF file is issued exclusively to';
            coverPage.drawText(t1, {
                x: (width - helvetica.widthOfTextAtSize(t1, 14)) / 2,
                y: cardY + 145,
                size: 14,
                font: helvetica,
                color: rgb(0.2, 0.25, 0.3),
            });

            const nameT = userFullName;
            if (nameT) {
                coverPage.drawText(nameT, {
                    x: (width - helveticaBold.widthOfTextAtSize(nameT, 20)) / 2,
                    y: cardY + 110,
                    size: 20,
                    font: helveticaBold,
                    color: rgb(0 / 255, 56 / 255, 118 / 255),
                });
            }

            const emailT = userEmail ? `(${userEmail})` : '';
            if (emailT) {
                coverPage.drawText(emailT, {
                    x: (width - helveticaBold.widthOfTextAtSize(emailT, 15)) / 2,
                    y: cardY + 85,
                    size: 15,
                    font: helveticaBold,
                    color: rgb(0 / 255, 56 / 255, 118 / 255),
                });
            }

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

            // White padlock icon
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

            // Divider Line
            coverPage.drawLine({
                start: { x: (width - 320) / 2, y: height - 595 },
                end: { x: (width + 320) / 2, y: height - 595 },
                thickness: 1,
                color: rgb(0.8, 0.85, 0.9),
            });

            // Text: PROCEPT / CONFIDENTIAL MATERIAL
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

            // 2. Add Personalized Disclaimer to all pages from Page 2 to end (Matching marked position)
            const allPages = pdfDocLib.getPages();
            const userIdentifier = userEmail ? `${nameT} (${userEmail})` : nameT;
            const disclaimerLine1 = `This PDF file is issued exclusively to ${userIdentifier}.`;
            const disclaimerLine2 = 'Duplication and further distribution are prohibited.';
            const textFontSize = 6.8;

            for (let i = 1; i < allPages.length; i++) {
                const page = allPages[i];
                const pWidth = page.getWidth();

                // Measure text widths to align right neatly above the copyright line
                const w1 = helveticaBold.widthOfTextAtSize(disclaimerLine1, textFontSize);
                const w2 = helvetica.widthOfTextAtSize(disclaimerLine2, textFontSize);

                const rightMargin = 45;
                const x1 = Math.max(40, pWidth - rightMargin - w1);
                const x2 = Math.max(40, pWidth - rightMargin - w2);

                // Clean existing footer area with a white rectangle so any previous or stale watermarks are erased
                const cleanX = Math.min(x1, x2) - 4;
                const cleanW = Math.max(w1, w2) + 20;
                page.drawRectangle({
                    x: cleanX,
                    y: 34,
                    width: cleanW,
                    height: 24,
                    color: rgb(1, 1, 1),
                });

                // Positioned right above the copyright / page footer line (y=38 and y=48)
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

            const stampedBytes = await pdfDocLib.save();
            return stampedBytes;
        } catch (err) {
            console.error('Error applying cover page and footers:', err);
            return bytes;
        }
    }

    // Initialize and load PDF document
    async function loadPdfDocument() {
        try {
            const response = await fetch(pdfUrl);
            if (!response.ok) throw new Error("Could not load PDF document.");
            let rawBytes = await response.arrayBuffer();

            // Apply cover page and footers before rendering
            pdfBytes = await applyCoverPageAndFooters(rawBytes);

            // Initial extraction of existing values from form fields using pdf-lib
            try {
                const { PDFDocument } = PDFLib;
                const preDoc = await PDFDocument.load(pdfBytes, { ignoreEncryption: true });
                const preForm = preDoc.getForm();
                const preFields = preForm.getFields();
                preFields.forEach(f => {
                    const name = f.getName();
                    if (f.constructor.name === 'PDFTextField') {
                        const val = f.getText();
                        if (val) formFieldValues[name] = val;
                    }
                });
            } catch (pe) {
                console.log('Pre-load field extraction note:', pe);
            }

            const loadingTask = pdfjsLib.getDocument({ data: pdfBytes.slice(0) });
            pdfDoc = await loadingTask.promise;
            if (pageCountEl) pageCountEl.textContent = pdfDoc.numPages;
            if (pageNumberInput) pageNumberInput.max = pdfDoc.numPages;

            renderPage(currentPageNum);
            renderThumbnails();
        } catch (err) {
            console.error(err);
            showToast("Failed to load PDF: " + err.message, true);
        }
    }

    // Render Page on Canvas & Overlay Interactive Form Fields
    async function renderPage(num) {
        if (!pdfDoc || !canvas || !ctx) return;

        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: scale });

        canvas.height = viewport.height;
        canvas.width = viewport.width;
        if (canvasWrapper) {
            canvasWrapper.style.width = `${viewport.width}px`;
            canvasWrapper.style.height = `${viewport.height}px`;
        }

        const renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };

        await page.render(renderContext).promise;

        if (pageNumberInput) pageNumberInput.value = num;
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        if (prevBtn) prevBtn.disabled = (num <= 1);
        if (nextBtn) nextBtn.disabled = (num >= pdfDoc.numPages);

        // 1. Render Interactive PDF Form Fields (The exact Learning Journal & Notes boxes)
        await renderInteractiveFormFields(page, viewport);

        // 2. Render Extra Freeform Notes
        renderCustomAnnotations(num, viewport);

        highlightCurrentThumbnail(num);
    }

    // Render interactive form fields over page
    async function renderInteractiveFormFields(page, viewport) {
        if (!formFieldsLayer) return;
        formFieldsLayer.innerHTML = '';

        const annotations = await page.getAnnotations();
        if (!annotations || annotations.length === 0) return;

        annotations.forEach(annot => {
            // Check if this annotation is a form widget (Text, Choice, etc.)
            if (annot.subtype === 'Widget' || annot.fieldType) {
                const rect = annot.rect; // [x1, y1, x2, y2] in PDF coordinates
                const normalizedRect = viewport.convertToViewportRectangle(rect);
                // normalizedRect is [x1, y1, x2, y2] in viewport pixels
                const minX = Math.min(normalizedRect[0], normalizedRect[2]);
                const minY = Math.min(normalizedRect[1], normalizedRect[3]);
                const width = Math.abs(normalizedRect[2] - normalizedRect[0]);
                const height = Math.abs(normalizedRect[3] - normalizedRect[1]);

                const fieldName = annot.fieldName || annot.id;
                const currentValue = formFieldValues[fieldName] !== undefined ? formFieldValues[fieldName] : (annot.fieldValue || '');

                // Container wrapper
                const fieldWrapper = document.createElement('div');
                fieldWrapper.style.position = 'absolute';
                fieldWrapper.style.left = `${minX}px`;
                fieldWrapper.style.top = `${minY}px`;
                fieldWrapper.style.width = `${width}px`;
                fieldWrapper.style.height = `${height}px`;
                fieldWrapper.style.pointerEvents = 'auto';

                let inputEl;
                // If multi-line (height is large like journal/notes boxes), render textarea
                if (annot.multiLine || height > 40) {
                    inputEl = document.createElement('textarea');
                } else {
                    inputEl = document.createElement('input');
                    inputEl.type = 'text';
                }

                inputEl.className = 'pdf-interactive-field w-full h-full';
                inputEl.value = currentValue;
                inputEl.placeholder = annot.alternativeText || 'Type your notes or answers here...';

                // Update values in realtime
                inputEl.addEventListener('input', (e) => {
                    formFieldValues[fieldName] = e.target.value;
                    dirtyFieldNames.add(fieldName);
                });

                // Optional label/title badge if available
                if (annot.alternativeText && height > 60) {
                    const badge = document.createElement('div');
                    badge.className = 'field-title-badge';
                    badge.textContent = annot.alternativeText;
                    fieldWrapper.appendChild(badge);
                }

                fieldWrapper.appendChild(inputEl);
                formFieldsLayer.appendChild(fieldWrapper);
            }
        });
    }

    // Render Custom Freeform Note Annotations
    function renderCustomAnnotations(pageNum, viewport) {
        if (!customAnnotationLayer) return;
        customAnnotationLayer.innerHTML = '';
        const pageBoxes = customAnnotations[pageNum] || [];

        pageBoxes.forEach((box, index) => {
            createCustomAnnotationElement(box, index, pageNum, viewport);
        });
    }

    function createCustomAnnotationElement(box, index, pageNum, viewport) {
        const [vx, vy] = viewport.convertToViewportPoint(box.pdfX, box.pdfY);

        const el = document.createElement('div');
        el.className = 'custom-annotation-box';
        el.style.left = `${vx}px`;
        el.style.top = `${vy}px`;
        el.style.width = `${box.width * scale}px`;
        el.style.minHeight = `${box.height * scale}px`;

        const textarea = document.createElement('div');
        textarea.contentEditable = "true";
        textarea.className = "w-full h-full outline-none";
        textarea.innerText = box.text || '';
        textarea.style.fontSize = `${(box.fontSize || 13) * scale}px`;

        const delBtn = document.createElement('span');
        delBtn.className = 'delete-box-btn';
        delBtn.innerHTML = '&times;';
        delBtn.title = 'Remove';
        delBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            customAnnotations[pageNum].splice(index, 1);
            renderCustomAnnotations(pageNum, viewport);
        });

        textarea.addEventListener('input', () => {
            box.text = textarea.innerText;
        });

        makeDraggable(el, box, viewport);

        el.appendChild(delBtn);
        el.appendChild(textarea);
        customAnnotationLayer.appendChild(el);
    }

    function makeDraggable(element, boxData, viewport) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        element.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            if (e.target.isContentEditable || e.target.classList.contains('delete-box-btn')) return;
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            const newTop = (element.offsetTop - pos2);
            const newLeft = (element.offsetLeft - pos1);
            element.style.top = newTop + "px";
            element.style.left = newLeft + "px";

            const pdfPoint = viewport.convertToPdfPoint(newLeft, newTop);
            boxData.pdfX = pdfPoint[0];
            boxData.pdfY = pdfPoint[1];
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    // Toggle "Add Extra Note" Tool
    const addTextToolBtn = document.getElementById('addTextToolBtn');
    if (addTextToolBtn && canvasWrapper) {
        addTextToolBtn.addEventListener('click', () => {
            isTextToolActive = !isTextToolActive;
            if (isTextToolActive) {
                addTextToolBtn.classList.replace('bg-amber-50', 'bg-amber-500');
                addTextToolBtn.classList.replace('text-amber-700', 'text-white');
                canvasWrapper.classList.add('cursor-crosshair');
            } else {
                addTextToolBtn.classList.replace('bg-amber-500', 'bg-amber-50');
                addTextToolBtn.classList.replace('text-white', 'text-amber-700');
                canvasWrapper.classList.remove('cursor-crosshair');
            }
        });

        canvasWrapper.addEventListener('click', async (e) => {
            if (!isTextToolActive) return;
            if (e.target.closest('.custom-annotation-box') || e.target.closest('.pdf-interactive-field')) return;

            const rect = canvasWrapper.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;

            const page = await pdfDoc.getPage(currentPageNum);
            const viewport = page.getViewport({ scale: scale });
            const pdfPoint = viewport.convertToPdfPoint(clickX, clickY);

            if (!customAnnotations[currentPageNum]) {
                customAnnotations[currentPageNum] = [];
            }

            const newBox = {
                pdfX: pdfPoint[0],
                pdfY: pdfPoint[1],
                width: 180,
                height: 45,
                text: 'Click here to write notes...',
                fontSize: 12
            };

            customAnnotations[currentPageNum].push(newBox);
            renderCustomAnnotations(currentPageNum, viewport);

            // Turn off tool after placement
            isTextToolActive = false;
            addTextToolBtn.classList.replace('bg-amber-500', 'bg-amber-50');
            addTextToolBtn.classList.replace('text-white', 'text-amber-700');
            canvasWrapper.classList.remove('cursor-crosshair');
        });
    }

    // Navigation
    const prevPageBtn = document.getElementById('prevPageBtn');
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPageNum > 1) {
                currentPageNum--;
                renderPage(currentPageNum);
            }
        });
    }

    const nextPageBtn = document.getElementById('nextPageBtn');
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            if (currentPageNum < pdfDoc.numPages) {
                currentPageNum++;
                renderPage(currentPageNum);
            }
        });
    }

    if (pageNumberInput) {
        pageNumberInput.addEventListener('change', (e) => {
            let val = parseInt(e.target.value);
            if (val >= 1 && val <= pdfDoc.numPages) {
                currentPageNum = val;
                renderPage(currentPageNum);
            } else {
                e.target.value = currentPageNum;
            }
        });
    }

    // Zoom Controls
    const zoomInBtn = document.getElementById('zoomInBtn');
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', () => {
            if (scale < 2.5) {
                scale += 0.25;
                if (zoomLevelEl) zoomLevelEl.textContent = `${Math.round(scale * 100)}%`;
                renderPage(currentPageNum);
            }
        });
    }

    const zoomOutBtn = document.getElementById('zoomOutBtn');
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', () => {
            if (scale > 0.5) {
                scale -= 0.25;
                if (zoomLevelEl) zoomLevelEl.textContent = `${Math.round(scale * 100)}%`;
                renderPage(currentPageNum);
            }
        });
    }

    // Thumbnails in Sidebar
    async function renderThumbnails() {
        const container = document.getElementById('thumbnailsContainer');
        if (!container) return;
        container.innerHTML = '';

        for (let i = 1; i <= Math.min(pdfDoc.numPages, 40); i++) {
            const item = document.createElement('div');
            item.className = `cursor-pointer flex flex-col items-center p-1 rounded border border-transparent hover:border-blue-400 thumbnail-item transition ${i === currentPageNum ? 'bg-blue-50 border-blue-500' : ''}`;
            item.dataset.page = i;

            const page = await pdfDoc.getPage(i);
            const thumbViewport = page.getViewport({ scale: 0.18 });
            const thumbCanvas = document.createElement('canvas');
            thumbCanvas.width = thumbViewport.width;
            thumbCanvas.height = thumbViewport.height;
            thumbCanvas.className = "rounded shadow-sm bg-white";

            await page.render({ canvasContext: thumbCanvas.getContext('2d'), viewport: thumbViewport }).promise;

            const label = document.createElement('span');
            label.className = "text-xs font-medium text-slate-500 mt-1";
            label.innerText = `P. ${i}`;

            item.appendChild(thumbCanvas);
            item.appendChild(label);

            item.addEventListener('click', () => {
                currentPageNum = i;
                renderPage(currentPageNum);
            });

            container.appendChild(item);
        }
    }

    function highlightCurrentThumbnail(pageNum) {
        document.querySelectorAll('.thumbnail-item').forEach(el => {
            if (parseInt(el.dataset.page) === pageNum) {
                el.classList.add('bg-blue-50', 'border-blue-500');
            } else {
                el.classList.remove('bg-blue-50', 'border-blue-500');
            }
        });
    }

    // SAVE LOGIC: Updates native PDF Form fields AND draws custom annotations
    const saveBtn = document.getElementById('saveBtn');
    const saveBtnText = document.getElementById('saveBtnText');

    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            try {
                saveBtn.disabled = true;
                if (saveBtnText) saveBtnText.innerText = 'Saving...';

                const { PDFDocument, rgb, StandardFonts } = PDFLib;
                const pdfDocLib = await PDFDocument.load(pdfBytes, { ignoreEncryption: true });
                const helveticaFont = await pdfDocLib.embedFont(StandardFonts.Helvetica);

                // 1. Fill modified native PDF Form Fields with normal, readable font size
                try {
                    const form = pdfDocLib.getForm();
                    const fieldsToUpdate = dirtyFieldNames.size > 0 
                        ? Array.from(dirtyFieldNames) 
                        : Object.keys(formFieldValues);

                    for (const name of fieldsToUpdate) {
                        const value = formFieldValues[name];
                        try {
                            const field = form.getField(name);
                            if (field && typeof field.setText === 'function') {
                                field.setFontSize(11);
                                field.updateAppearances(helveticaFont);
                                field.setText(String(value || ''));
                            }
                        } catch (fieldErr) {
                            try {
                                const field = form.getField(name);
                                if (field && typeof field.setText === 'function') {
                                    field.setText(String(value || ''));
                                }
                            } catch (e) {}
                        }
                    }
                } catch (formErr) {
                    console.log('No form object or form fields processed:', formErr);
                }

                // 2. Burn Custom text annotations into pages
                const pages = pdfDocLib.getPages();
                for (const pageNumStr in customAnnotations) {
                    const pIndex = parseInt(pageNumStr) - 1;
                    if (pIndex >= 0 && pIndex < pages.length) {
                        const targetPage = pages[pIndex];
                        const boxes = customAnnotations[pageNumStr];

                        boxes.forEach(box => {
                            if (box.text && box.text.trim().length > 0 && box.text !== 'Click here to write notes...') {
                                targetPage.drawText(box.text, {
                                    x: box.pdfX,
                                    y: box.pdfY - (box.fontSize || 12),
                                    size: box.fontSize || 12,
                                    font: helveticaFont,
                                    color: rgb(0.1, 0.1, 0.1),
                                });
                            }
                        });
                    }
                }

                // 3. Export PDF
                const modifiedPdfBytes = await pdfDocLib.save();

                // 4. Send to backend
                const blob = new Blob([modifiedPdfBytes], { type: 'application/pdf' });
                const formData = new FormData();
                formData.append('pdf', blob, 'document.pdf');

                const res = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data;
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    data = await res.json();
                } else {
                    const text = await res.text();
                    data = { message: text.substring(0, 150) };
                }

                if (res.ok && data.success) {
                    showToast(data.message || 'PDF copy saved successfully!');
                    pdfBytes = modifiedPdfBytes.buffer;
                    dirtyFieldNames.clear();
                    const docStatusBadge = document.getElementById('docStatusBadge');
                    if (docStatusBadge) {
                        docStatusBadge.innerText = '● Saved Copy Loaded';
                    }
                } else {
                    throw new Error(data.message || 'Failed to save.');
                }
            } catch (err) {
                console.error(err);
                showToast('Save failed: ' + err.message, true);
            } finally {
                saveBtn.disabled = false;
                if (saveBtnText) saveBtnText.innerText = 'Save Copy';
            }
        });
    }

    // Download button click handler (downloads the stamped PDF directly)
    const downloadBtn = document.getElementById('downloadBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', (e) => {
            if (pdfBytes) {
                e.preventDefault();
                const blob = new Blob([pdfBytes], { type: 'application/pdf' });
                const blobUrl = URL.createObjectURL(blob);
                const tempLink = document.createElement('a');
                tempLink.href = blobUrl;
                tempLink.download = filename;
                document.body.appendChild(tempLink);
                tempLink.click();
                document.body.removeChild(tempLink);
                URL.revokeObjectURL(blobUrl);
            }
        });
    }

    // Initialize editor when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadPdfDocument);
    } else {
        loadPdfDocument();
    }
})();
