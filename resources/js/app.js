import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const dismissButtons = document.querySelectorAll('[data-dismiss-parent]');
    const uploadPickers = document.querySelectorAll('[data-upload-picker]');
    const activeModal = document.querySelector('[data-modal]');
    const photoManagers = document.querySelectorAll('[data-photo-manager]');
    const exportManager = document.querySelector('[data-export-manager]');

    const syncBodyScrollLock = () => {
        const isDesktop = window.innerWidth >= 1024;
        const sidebarOpen = sidebar?.getAttribute('data-open') === 'true' && !isDesktop;
        const exportModalOpen = document.querySelector('[data-export-modal]:not([hidden])');
        const mobileSelectOpen = document.querySelector('[data-mobile-select-sheet]:not([hidden])');
        const pageModalOpen = document.querySelector('[data-modal]');

        document.body.classList.toggle(
            'overflow-hidden',
            Boolean(sidebarOpen || exportModalOpen || mobileSelectOpen || pageModalOpen)
        );
    };

    const setSidebarState = (isOpen) => {
        if (!sidebar) {
            return;
        }

        const isDesktop = window.innerWidth >= 1024;

        sidebar.setAttribute('data-open', isOpen ? 'true' : 'false');

        if (overlay) {
            overlay.hidden = !isOpen || isDesktop;
        }

        syncBodyScrollLock();
    };

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const isOpen = sidebar?.getAttribute('data-open') === 'true';
            setSidebarState(!isOpen);
        });
    });

    overlay?.addEventListener('click', () => setSidebarState(false));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            setSidebarState(false);
        }
    });

    dismissButtons.forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-dismissible]')?.remove();
        });
    });

    uploadPickers.forEach((picker) => {
        const input = picker.querySelector('[data-upload-input]');
        const status = picker.querySelector('[data-upload-status]');
        const preview = picker.querySelector('[data-upload-preview]');
        const form = picker.closest('form');
        const submitButtons = form
            ? Array.from(form.querySelectorAll('button[type="submit"]:not([form])'))
            : [];

        if (!input || !status || !preview) {
            return;
        }

        let objectUrls = [];
        let uploadSequence = 0;
        let isPreparingFiles = false;
        let isResubmitting = false;
        let pendingUploadTask = Promise.resolve();
        const optimizeThresholdBytes = 8 * 1024 * 1024;
        const hardLimitBytes = 15 * 1024 * 1024;
        const maxImageEdge = 2200;

        const resetPreview = () => {
            objectUrls.forEach((url) => URL.revokeObjectURL(url));
            objectUrls = [];
            preview.innerHTML = '';
            preview.hidden = true;
        };

        const browserCanRewriteFiles = typeof window.DataTransfer !== 'undefined' && typeof window.File !== 'undefined';

        submitButtons.forEach((button) => {
            if (!button.dataset.defaultLabel) {
                button.dataset.defaultLabel = button.textContent.trim();
            }
        });

        const syncSubmitButtons = () => {
            submitButtons.forEach((button) => {
                button.disabled = isPreparingFiles;
                button.textContent = isPreparingFiles
                    ? 'Menyiapkan Foto...'
                    : (button.dataset.defaultLabel || button.textContent.trim());
            });
        };

        const loadImageFile = (file) => new Promise((resolve, reject) => {
            const objectUrl = URL.createObjectURL(file);
            const image = new Image();

            image.onload = () => {
                URL.revokeObjectURL(objectUrl);
                resolve(image);
            };

            image.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error('image_load_failed'));
            };

            image.src = objectUrl;
        });

        const optimizeImageFile = async (file) => {
            const canOptimize = browserCanRewriteFiles
                && file.type.startsWith('image/')
                && file.type !== 'image/gif';
            const shouldOptimize = canOptimize
                && (file.size > optimizeThresholdBytes || /image\/(heic|heif|avif)/i.test(file.type));

            if (!shouldOptimize) {
                return { file, optimized: false, dropped: false };
            }

            try {
                const image = await loadImageFile(file);
                const scale = Math.min(1, maxImageEdge / Math.max(image.naturalWidth, image.naturalHeight));
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
                canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));

                const context = canvas.getContext('2d');

                if (!context) {
                    throw new Error('canvas_context_unavailable');
                }

                context.drawImage(image, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.82);
                });

                if (!blob) {
                    throw new Error('blob_generation_failed');
                }

                const baseName = file.name.replace(/\.[^.]+$/, '') || 'asset-photo';

                return {
                    file: new File([blob], `${baseName}.jpg`, {
                        type: 'image/jpeg',
                        lastModified: file.lastModified,
                    }),
                    optimized: true,
                    dropped: false,
                };
            } catch (error) {
                if (file.size > hardLimitBytes) {
                    return { file: null, optimized: false, dropped: true };
                }

                return { file, optimized: false, dropped: false };
            }
        };

        const assignFiles = (files) => {
            if (!browserCanRewriteFiles) {
                return files;
            }

            const transfer = new DataTransfer();
            files.forEach((file) => transfer.items.add(file));
            input.files = transfer.files;

            return Array.from(input.files ?? []);
        };

        const updateUploadState = async () => {
            const sequence = ++uploadSequence;
            const selectedFiles = Array.from(input.files ?? []);

            resetPreview();

            if (selectedFiles.length === 0) {
                isPreparingFiles = false;
                syncSubmitButtons();
                status.textContent = 'Belum ada foto dipilih.';
                return;
            }

            isPreparingFiles = true;
            syncSubmitButtons();
            status.textContent = 'Menyiapkan foto...';

            const processedFiles = [];
            let optimizedCount = 0;
            let droppedCount = 0;

            for (const file of selectedFiles) {
                const result = await optimizeImageFile(file);

                if (sequence !== uploadSequence) {
                    return;
                }

                if (result.dropped) {
                    droppedCount += 1;
                    continue;
                }

                if (result.file) {
                    processedFiles.push(result.file);
                }

                if (result.optimized) {
                    optimizedCount += 1;
                }
            }

            if (sequence !== uploadSequence) {
                return;
            }

            const files = assignFiles(processedFiles);

            if (files.length === 0) {
                isPreparingFiles = false;
                syncSubmitButtons();
                status.textContent = droppedCount > 0
                    ? 'Foto terlalu besar untuk diupload. Pilih ulang foto yang lebih kecil.'
                    : 'Belum ada foto dipilih.';
                return;
            }

            const statusParts = [
                files.length === 1 ? '1 foto siap diupload.' : `${files.length} foto siap diupload.`,
            ];

            if (optimizedCount > 0) {
                statusParts.push(
                    optimizedCount === 1
                        ? '1 foto diperkecil otomatis.'
                        : `${optimizedCount} foto diperkecil otomatis.`
                );
            }

            if (droppedCount > 0) {
                statusParts.push(
                    droppedCount === 1
                        ? '1 foto terlalu besar dan tidak ikut dipilih.'
                        : `${droppedCount} foto terlalu besar dan tidak ikut dipilih.`
                );
            }

            status.textContent = statusParts.join(' ');

            preview.hidden = false;

            files.slice(0, 6).forEach((file) => {
                const url = URL.createObjectURL(file);
                objectUrls.push(url);

                const tile = document.createElement('div');
                tile.className = 'upload-picker__preview-tile';

                const image = document.createElement('img');
                image.src = url;
                image.alt = file.name;
                image.className = 'upload-picker__preview-image';

                tile.appendChild(image);
                preview.appendChild(tile);
            });

            if (files.length > 6) {
                const extra = document.createElement('div');
                extra.className = 'upload-picker__preview-more';
                extra.textContent = `+${files.length - 6}`;
                preview.appendChild(extra);
            }

            isPreparingFiles = false;
            syncSubmitButtons();
        };

        const queueUploadPreparation = () => {
            pendingUploadTask = updateUploadState().catch(() => {
                isPreparingFiles = false;
                syncSubmitButtons();
                status.textContent = 'Foto gagal diproses di browser. Coba pilih ulang fotonya.';
            });

            return pendingUploadTask;
        };

        form?.addEventListener('submit', (event) => {
            if (isResubmitting || !isPreparingFiles) {
                return;
            }

            event.preventDefault();

            const submitter = event.submitter;

            pendingUploadTask.finally(() => {
                if (!form.isConnected) {
                    return;
                }

                isResubmitting = true;

                if (typeof form.requestSubmit === 'function' && submitter instanceof HTMLElement) {
                    form.requestSubmit(submitter);
                } else {
                    form.submit();
                }

                window.setTimeout(() => {
                    isResubmitting = false;
                }, 0);
            });
        });

        input.addEventListener('change', () => {
            void queueUploadPreparation();
        });
    });

    const initMobileSelects = () => {
        const selects = Array.from(document.querySelectorAll('select.form-select:not([multiple])'));

        if (selects.length === 0) {
            return;
        }

        const mobileBreakpoint = window.matchMedia('(max-width: 767px)');
        const sheet = document.createElement('div');
        sheet.className = 'mobile-select-sheet';
        sheet.dataset.mobileSelectSheet = 'true';
        sheet.hidden = true;
        sheet.innerHTML = `
            <div class="mobile-select-sheet__backdrop" data-mobile-select-close></div>
            <div class="mobile-select-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="mobile-select-sheet-title">
                <div class="mobile-select-sheet__handle" aria-hidden="true"></div>
                <div class="mobile-select-sheet__header">
                    <div id="mobile-select-sheet-title" class="mobile-select-sheet__title">Pilih Opsi</div>
                    <button type="button" class="mobile-select-sheet__close" data-mobile-select-close>Tutup</button>
                </div>
                <div class="mobile-select-sheet__options" data-mobile-select-options></div>
            </div>
        `;

        document.body.appendChild(sheet);

        const sheetTitle = sheet.querySelector('#mobile-select-sheet-title');
        const sheetOptions = sheet.querySelector('[data-mobile-select-options]');
        const sheetCloseButtons = sheet.querySelectorAll('[data-mobile-select-close]');
        let activeControl = null;

        const controls = selects.map((select, index) => {
            if (!select.id) {
                select.id = `form-select-${index + 1}`;
            }

            const field = select.closest('.form-field') ?? select.parentElement;
            const label = field?.querySelector(`label[for="${select.id}"]`);
            const labelText = label?.textContent?.trim() || 'Pilih Opsi';
            const shell = document.createElement('div');
            shell.className = 'form-select-shell';

            select.parentNode.insertBefore(shell, select);
            shell.appendChild(select);

            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'form-select-trigger';
            trigger.setAttribute('aria-haspopup', 'dialog');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.innerHTML = `
                <span class="form-select-trigger__text"></span>
                <span class="form-select-trigger__icon" aria-hidden="true"></span>
            `;

            shell.appendChild(trigger);

            const triggerText = trigger.querySelector('.form-select-trigger__text');

            const updateTrigger = () => {
                const selectedOption = select.options[select.selectedIndex] ?? select.options[0] ?? null;
                const placeholder = !select.value && Array.from(select.options).some((option) => option.value === '');

                triggerText.textContent = selectedOption?.textContent?.trim() ?? '';
                trigger.dataset.placeholder = placeholder ? 'true' : 'false';
                trigger.disabled = select.disabled;
                trigger.setAttribute('aria-label', labelText);
            };

            trigger.addEventListener('click', () => {
                if (!mobileBreakpoint.matches || select.disabled) {
                    return;
                }

                activeControl = { labelText, select, trigger, updateTrigger };
                sheetTitle.textContent = labelText;
                sheetOptions.innerHTML = '';

                Array.from(select.options).forEach((option, optionIndex) => {
                    const optionButton = document.createElement('button');
                    optionButton.type = 'button';
                    optionButton.className = 'mobile-select-sheet__option';
                    optionButton.dataset.optionIndex = String(optionIndex);
                    optionButton.disabled = option.disabled;

                    if (option.selected) {
                        optionButton.classList.add('is-selected');
                    }

                    optionButton.innerHTML = `
                        <span class="mobile-select-sheet__option-label">${option.textContent.trim()}</span>
                        ${option.selected ? '<span class="mobile-select-sheet__option-state">Dipilih</span>' : ''}
                    `;

                    sheetOptions.appendChild(optionButton);
                });

                trigger.setAttribute('aria-expanded', 'true');
                sheet.hidden = false;
                syncBodyScrollLock();
            });

            select.addEventListener('change', updateTrigger);
            select.closest('form')?.addEventListener('reset', () => {
                window.setTimeout(updateTrigger, 0);
            });

            updateTrigger();

            return { select, shell, trigger, updateTrigger };
        });

        const closeSheet = () => {
            if (activeControl?.trigger) {
                activeControl.trigger.setAttribute('aria-expanded', 'false');
                activeControl.trigger.focus({ preventScroll: true });
            }

            activeControl = null;
            sheet.hidden = true;
            syncBodyScrollLock();
        };

        sheetCloseButtons.forEach((button) => {
            button.addEventListener('click', closeSheet);
        });

        sheetOptions.addEventListener('click', (event) => {
            const optionButton = event.target.closest('[data-option-index]');

            if (!optionButton || !activeControl) {
                return;
            }

            activeControl.select.selectedIndex = Number(optionButton.dataset.optionIndex);
            activeControl.select.dispatchEvent(new Event('change', { bubbles: true }));
            closeSheet();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !sheet.hidden) {
                closeSheet();
            }
        });

        const syncMode = () => {
            const isMobile = mobileBreakpoint.matches;

            controls.forEach(({ shell, trigger, updateTrigger }) => {
                shell.classList.toggle('form-select-shell--mobile', isMobile);
                trigger.hidden = !isMobile;
                trigger.setAttribute('aria-expanded', 'false');
                updateTrigger();
            });

            if (!isMobile && !sheet.hidden) {
                closeSheet();
            }
        };

        if (typeof mobileBreakpoint.addEventListener === 'function') {
            mobileBreakpoint.addEventListener('change', syncMode);
        } else if (typeof mobileBreakpoint.addListener === 'function') {
            mobileBreakpoint.addListener(syncMode);
        }

        syncMode();
    };

    const initMobileDateInputs = () => {
        const dateInputs = Array.from(document.querySelectorAll("input[type='date'].form-input"));

        if (dateInputs.length === 0) {
            return;
        }

        const mobileBreakpoint = window.matchMedia('(max-width: 767px)');
        const formatter = new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });

        const controls = dateInputs.map((input, index) => {
            if (!input.id) {
                input.id = `form-date-${index + 1}`;
            }

            const field = input.closest('.form-field') ?? input.parentElement;
            const label = field?.querySelector(`label[for="${input.id}"]`);
            const placeholder = input.dataset.datePlaceholder || label?.textContent?.trim() || 'Pilih tanggal';
            const shell = document.createElement('div');
            shell.className = 'form-date-shell';

            input.parentNode.insertBefore(shell, input);
            shell.appendChild(input);

            const display = document.createElement('div');
            display.className = 'form-date-display';
            display.innerHTML = `
                <span class="form-date-display__value"></span>
                <span class="form-date-display__icon" aria-hidden="true"></span>
            `;

            shell.appendChild(display);

            const displayValue = display.querySelector('.form-date-display__value');

            const updateDisplay = () => {
                if (input.disabled) {
                    shell.classList.add('is-disabled');
                    display.dataset.placeholder = 'true';
                    displayValue.textContent = input.dataset.dateDisabledLabel || 'Tidak perlu diisi';
                    return;
                }

                shell.classList.remove('is-disabled');

                if (!input.value) {
                    display.dataset.placeholder = 'true';
                    displayValue.textContent = placeholder;
                    return;
                }

                const [year, month, day] = input.value.split('-').map(Number);
                const valueDate = new Date(year, (month ?? 1) - 1, day ?? 1);

                display.dataset.placeholder = 'false';
                displayValue.textContent = Number.isNaN(valueDate.getTime())
                    ? input.value
                    : formatter.format(valueDate);
            };

            input.addEventListener('change', updateDisplay);
            input.addEventListener('input', updateDisplay);
            input.closest('form')?.addEventListener('reset', () => {
                window.setTimeout(updateDisplay, 0);
            });

            updateDisplay();

            return { shell, display, input, updateDisplay };
        });

        const syncMode = () => {
            const isMobile = mobileBreakpoint.matches;

            controls.forEach(({ shell, display, updateDisplay }) => {
                shell.classList.toggle('form-date-shell--mobile', isMobile);
                display.hidden = !isMobile;
                updateDisplay();
            });
        };

        if (typeof mobileBreakpoint.addEventListener === 'function') {
            mobileBreakpoint.addEventListener('change', syncMode);
        } else if (typeof mobileBreakpoint.addListener === 'function') {
            mobileBreakpoint.addListener(syncMode);
        }

        syncMode();
    };

    const initWarrantyFields = () => {
        const warrantyToggles = Array.from(document.querySelectorAll('[data-warranty-toggle]'));

        warrantyToggles.forEach((toggle) => {
            const checkbox = toggle.querySelector('[data-warranty-checkbox]');
            const form = toggle.closest('form');
            const title = toggle.querySelector('[data-warranty-title]');
            const warrantyField = form?.querySelector('[data-warranty-date-field]');
            const warrantyInput = form?.querySelector('[data-warranty-expiry]');

            if (!checkbox || !warrantyField || !warrantyInput || !title) {
                return;
            }

            const updateWarrantyState = () => {
                const hasWarranty = checkbox.checked;

                if (!hasWarranty && warrantyInput.value) {
                    warrantyInput.dataset.savedValue = warrantyInput.value;
                    warrantyInput.value = '';
                } else if (hasWarranty && !warrantyInput.value && warrantyInput.dataset.savedValue) {
                    warrantyInput.value = warrantyInput.dataset.savedValue;
                }

                warrantyInput.disabled = !hasWarranty;
                warrantyField.hidden = !hasWarranty;
                title.textContent = hasWarranty ? 'Asset memiliki garansi' : 'Asset tidak memiliki garansi';

                warrantyInput.dispatchEvent(new Event('change', { bubbles: true }));
            };

            checkbox.addEventListener('change', updateWarrantyState);
            form?.addEventListener('reset', () => {
                window.setTimeout(updateWarrantyState, 0);
            });

            updateWarrantyState();
        });
    };

    initMobileSelects();
    initMobileDateInputs();
    initWarrantyFields();

    if (activeModal) {
        const closeButtons = activeModal.querySelectorAll('[data-modal-close]');

        const closeModal = () => {
            activeModal.remove();
            syncBodyScrollLock();
        };

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        activeModal.addEventListener('click', (event) => {
            if (event.target === activeModal) {
                closeModal();
            }
        });

        syncBodyScrollLock();
    }

    if (exportManager) {
        const storageKey = 'inventaris:selected-asset-ids';
        const checkboxes = Array.from(document.querySelectorAll('[data-export-checkbox]'));
        const count = exportManager.querySelector('[data-export-count]');
        const selectPage = exportManager.querySelector('[data-export-select-page]');
        const clear = exportManager.querySelector('[data-export-clear]');
        const open = exportManager.querySelector('[data-export-open]');
        const modal = document.querySelector('[data-export-modal]');
        const closeButtons = modal?.querySelectorAll('[data-export-close]') ?? [];
        const submitButtons = modal?.querySelectorAll('[data-export-submit]') ?? [];
        const modalCount = modal?.querySelector('[data-export-modal-count]');
        const form = document.querySelector('#items-export-form');
        const formatInput = form?.querySelector('[data-export-format]');
        const hiddenInputs = form?.querySelector('[data-export-hidden-inputs]');

        const readSelectedIds = () => {
            try {
                const stored = JSON.parse(window.localStorage.getItem(storageKey) ?? '[]');

                if (!Array.isArray(stored)) {
                    return [];
                }

                return [...new Set(
                    stored
                        .map((value) => String(value).trim())
                        .filter((value) => /^\d+$/.test(value))
                )];
            } catch (error) {
                return [];
            }
        };

        const writeSelectedIds = (ids) => {
            try {
                window.localStorage.setItem(storageKey, JSON.stringify(ids));
            } catch (error) {
                // Ignore storage failures and keep the page usable.
            }
        };

        const syncCheckboxes = () => {
            const selected = new Set(readSelectedIds());

            checkboxes.forEach((checkbox) => {
                checkbox.checked = selected.has(checkbox.value);
            });
        };

        const updateExportState = () => {
            const selectedIds = readSelectedIds();
            const selectedCount = selectedIds.length;
            const label = selectedCount === 1
                ? '1 asset dipilih'
                : `${selectedCount} asset dipilih`;
            const modalLabel = selectedCount === 1
                ? '1 asset akan diexport.'
                : `${selectedCount} asset akan diexport.`;

            if (count) {
                count.textContent = label;
            }

            if (modalCount) {
                modalCount.textContent = modalLabel;
            }

            if (open) {
                open.disabled = selectedCount === 0;
            }
        };

        const setSelectedIds = (ids) => {
            writeSelectedIds([...new Set(ids)]);
            syncCheckboxes();
            updateExportState();
        };

        const openModal = () => {
            if (!modal || readSelectedIds().length === 0) {
                return;
            }

            modal.hidden = false;
            syncBodyScrollLock();
        };

        const closeModal = () => {
            if (!modal) {
                return;
            }

            modal.hidden = true;
            syncBodyScrollLock();
        };

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const selected = new Set(readSelectedIds());

                if (checkbox.checked) {
                    selected.add(checkbox.value);
                } else {
                    selected.delete(checkbox.value);
                }

                setSelectedIds(Array.from(selected));
            });
        });

        selectPage?.addEventListener('click', () => {
            const selected = new Set(readSelectedIds());

            checkboxes.forEach((checkbox) => {
                selected.add(checkbox.value);
            });

            setSelectedIds(Array.from(selected));
        });

        clear?.addEventListener('click', () => {
            setSelectedIds([]);
            closeModal();
        });

        open?.addEventListener('click', openModal);

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        submitButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const selectedIds = readSelectedIds();

                if (!form || !formatInput || !hiddenInputs || selectedIds.length === 0) {
                    return;
                }

                formatInput.value = button.getAttribute('data-export-submit') ?? '';
                hiddenInputs.innerHTML = '';

                selectedIds.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'item_ids[]';
                    input.value = id;
                    hiddenInputs.appendChild(input);
                });

                form.submit();
            });
        });

        syncCheckboxes();
        updateExportState();
    }

    photoManagers.forEach((manager) => {
        const checkboxes = manager.parentElement?.querySelectorAll('[data-photo-checkbox]') ?? [];
        const count = manager.querySelector('[data-photo-selected-count]');
        const selectAll = manager.querySelector('[data-photo-select-all]');
        const clear = manager.querySelector('[data-photo-clear]');
        const deleteButton = manager.querySelector('[data-photo-delete-button]');
        const deleteForm = document.querySelector('#item-photo-delete-form');

        if (!count || !deleteButton || checkboxes.length === 0) {
            return;
        }

        const updateState = () => {
            const selected = Array.from(checkboxes).filter((checkbox) => checkbox.checked).length;
            count.textContent = selected === 0 ? '0 dipilih' : `${selected} dipilih`;
            deleteButton.disabled = selected === 0;
        };

        selectAll?.addEventListener('click', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });

            updateState();
        });

        clear?.addEventListener('click', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });

            updateState();
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateState);
        });

        deleteForm?.addEventListener('submit', (event) => {
            const selected = Array.from(checkboxes).filter((checkbox) => checkbox.checked).length;

            if (selected === 0) {
                event.preventDefault();
                return;
            }

            if (!window.confirm(`Hapus ${selected} foto yang dipilih?`)) {
                event.preventDefault();
            }
        });

        updateState();
    });
});
