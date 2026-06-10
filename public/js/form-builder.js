document.addEventListener('DOMContentLoaded', () => {

    let formSchema = [];
    let historyStack = [];
    let historyIndex = -1;
    let selectedFieldId = null;
    let draggedFieldType = null;
    let dragSourceElement = null;
    let isPreviewMode = false;

    const dropCanvas = document.getElementById('dropCanvas');
    const emptyState = document.getElementById('emptyState');
    const panelAddFields = document.getElementById('panelAddFields');
    const panelFieldOptions = document.getElementById('panelFieldOptions');
    const tabAddFields = document.getElementById('tabAddFields');
    const tabFieldOptions = document.getElementById('tabFieldOptions');
    const noFieldSelected = document.getElementById('noFieldSelected');
    const fieldSettings = document.getElementById('fieldSettings');

    // Restore saved form from localStorage
    const savedSchema = localStorage.getItem('formBuilderSchema');
    if (savedSchema) {
        try {
            formSchema = JSON.parse(savedSchema);
            if (formSchema.length > 0) {
                saveHistory();
                renderCanvas();
            }
        } catch (e) {
            console.error('Could not load saved form.');
        }
    }

    if (historyStack.length === 0) saveHistory();

    tabAddFields.addEventListener('click', showAddFields);
    tabFieldOptions.addEventListener('click', () => {
        tabAddFields.className = "w-1/2 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700 rounded-lg transition-all";
        tabFieldOptions.className = "w-1/2 py-2.5 text-sm font-bold text-blue-700 bg-white rounded-lg shadow-sm transition-all";
        panelAddFields.classList.add('hidden');
        panelFieldOptions.classList.remove('hidden');
    });

    function showAddFields() {
        tabAddFields.className = "w-1/2 py-2.5 text-sm font-bold text-blue-700 bg-white rounded-lg shadow-sm transition-all";
        tabFieldOptions.className = "w-1/2 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700 rounded-lg transition-all";
        panelAddFields.classList.remove('hidden');
        panelFieldOptions.classList.add('hidden');
    }

    document.querySelectorAll('.palette-item').forEach(item => {
        item.addEventListener('dragstart', (e) => {
            draggedFieldType = item.dataset.type;
            e.dataTransfer.setData('text/plain', 'palette');
            e.dataTransfer.effectAllowed = 'copy';
        });
        item.addEventListener('dragend', () => {
            draggedFieldType = null;
        });
    });

    dropCanvas.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = draggedFieldType ? 'copy' : 'move';
        dropCanvas.classList.add('drag-over');
    });

    dropCanvas.addEventListener('dragleave', () => {
        dropCanvas.classList.remove('drag-over');
    });

    dropCanvas.addEventListener('drop', (e) => {
        e.preventDefault();
        dropCanvas.classList.remove('drag-over');

        const source = e.dataTransfer.getData('text/plain');

        if (source === 'palette' && draggedFieldType) {
            const newField = createNewField(draggedFieldType);
            formSchema.push(newField);
            saveHistory();
            renderCanvas();
            selectField(newField.id);
            showToast('Field added');
        } else if (source === 'canvas' && dragSourceElement) {
            const targetElement = e.target.closest('.form-element');
            if (targetElement && targetElement !== dragSourceElement) {
                const sourceId = parseInt(dragSourceElement.dataset.fieldId);
                const targetId = parseInt(targetElement.dataset.fieldId);
                const sourceIndex = formSchema.findIndex(f => f.id === sourceId);
                const targetIndex = formSchema.findIndex(f => f.id === targetId);
                const [moved] = formSchema.splice(sourceIndex, 1);
                formSchema.splice(targetIndex, 0, moved);
                saveHistory();
                renderCanvas();
                if (selectedFieldId === sourceId) selectField(sourceId);
            }
        }

        draggedFieldType = null;
        dragSourceElement = null;
    });

    function createNewField(type) {
        const labels = {
            text: 'Text Input',
            textarea: 'Text Area',
            number: 'Number',
            email: 'Email',
            phone: 'Phone Number',
            dropdown: 'Dropdown',
            radio: 'Radio Buttons',
            checkbox: 'Checkboxes',
            datepicker: 'Date Picker',
            fileupload: 'File Upload',
            hidden: 'Hidden Field'
        };

        const field = {
            id: Date.now(),
            type: type,
            label: labels[type] || 'New Field',
            placeholder: '',
            cssClass: '',
            required: false,
            defaultValue: '',
            min: '',
            max: ''
        };

        if (['dropdown', 'radio', 'checkbox'].includes(type)) {
            field.options = ['Option 1', 'Option 2', 'Option 3'];
        }

        return field;
    }

    function renderCanvas() {
        if (formSchema.length === 0) {
            dropCanvas.innerHTML = '';
            dropCanvas.appendChild(emptyState);
            emptyState.classList.remove('hidden');
            localStorage.removeItem('formBuilderSchema');
            return;
        }

        emptyState.classList.add('hidden');
        dropCanvas.innerHTML = '';

        formSchema.forEach((field) => {
            const tpl = document.getElementById(`tpl-${field.type}`);
            if (!tpl) return;

            const clone = tpl.content.cloneNode(true);
            const wrapper = clone.querySelector('.form-element');
            wrapper.dataset.fieldId = field.id;
            wrapper.draggable = true;

            if (field.id === selectedFieldId) {
                wrapper.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50/30', 'rounded-xl', 'p-2', '-mx-2');
            } else {
                wrapper.classList.add('hover:bg-gray-50', 'rounded-xl', 'p-2', '-mx-2', 'transition-colors');
            }

            const labelEl = wrapper.querySelector('.field-label');
            if (labelEl) {
                labelEl.innerHTML = `${field.label} ${field.required ? '<span class="text-red-500">*</span>' : ''}`;
            }

            const inputEl = wrapper.querySelector('.field-input');
            if (inputEl) {
                if (field.placeholder) inputEl.placeholder = field.placeholder;
                if (field.defaultValue) inputEl.value = field.defaultValue;
                if (field.cssClass) inputEl.className += ` ${field.cssClass}`;
            }

            if (['radio', 'checkbox'].includes(field.type)) {
                const container = wrapper.querySelector('.field-options-container');
                if (container && field.options) {
                    container.innerHTML = field.options.map(opt => `
                        <label class="flex items-center gap-2">
                            <input type="${field.type}" class="w-4 h-4 text-blue-600" disabled>
                            <span class="text-sm text-gray-700">${opt}</span>
                        </label>
                    `).join('');
                }
            }

            if (field.type === 'dropdown') {
                const selectEl = wrapper.querySelector('select');
                if (selectEl && field.options) {
                    selectEl.innerHTML = field.options.map(opt => `<option>${opt}</option>`).join('');
                }
            }

            const editBtn = wrapper.querySelector('.edit-field');
            if (editBtn) editBtn.onclick = (e) => { e.stopPropagation(); selectField(field.id); };

            const delBtn = wrapper.querySelector('.delete-field');
            if (delBtn) delBtn.onclick = (e) => { e.stopPropagation(); deleteField(field.id); };

            const dupBtn = wrapper.querySelector('.duplicate-field');
            if (dupBtn) dupBtn.onclick = (e) => { e.stopPropagation(); duplicateField(field.id); };

            wrapper.onclick = () => {
                if (!isPreviewMode) selectField(field.id);
            };

            wrapper.addEventListener('dragstart', (e) => {
                dragSourceElement = wrapper;
                e.dataTransfer.setData('text/plain', 'canvas');
                e.dataTransfer.effectAllowed = 'move';
                wrapper.classList.add('opacity-50');
            });

            wrapper.addEventListener('dragend', () => {
                wrapper.classList.remove('opacity-50');
                dragSourceElement = null;
                renderCanvas();
            });

            dropCanvas.appendChild(clone);
        });

        localStorage.setItem('formBuilderSchema', JSON.stringify(formSchema));
    }

    function selectField(id) {
        selectedFieldId = id;
        const field = formSchema.find(f => f.id === id);
        if (!field) return;

        renderCanvas();
        tabFieldOptions.click();
        noFieldSelected.classList.add('hidden');
        fieldSettings.classList.remove('hidden');

        document.getElementById('settingsFieldType').innerText = field.type;
        document.getElementById('optLabel').value = field.label || '';
        document.getElementById('optPlaceholder').value = field.placeholder || '';
        document.getElementById('optClass').value = field.cssClass || '';
        document.getElementById('optRequired').checked = field.required || false;
        document.getElementById('optDefault').value = field.defaultValue || '';
        document.getElementById('optMin').value = field.min || '';
        document.getElementById('optMax').value = field.max || '';

        document.querySelectorAll('.opt-group').forEach(group => {
            const types = group.dataset.appliesTo.split(',');
            group.classList.toggle('hidden', !types.includes(field.type));
        });

        if (['dropdown', 'radio', 'checkbox'].includes(field.type)) {
            renderOptionsEditor(field);
        }
    }

    function renderOptionsEditor(field) {
        const container = document.getElementById('optionsListContainer');
        container.innerHTML = field.options.map((opt, idx) => `
            <div class="flex items-center gap-2">
                <input type="text" value="${opt}" class="flex-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:border-blue-500 focus:outline-none" onchange="updateOption(${field.id}, ${idx}, this.value)">
                <button type="button" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" onclick="removeOption(${field.id}, ${idx})">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        `).join('');
    }

    window.updateOption = function(fieldId, idx, val) {
        const field = formSchema.find(f => f.id === fieldId);
        if (field) {
            field.options[idx] = val;
            saveHistory();
            renderCanvas();
        }
    };

    window.removeOption = function(fieldId, idx) {
        const field = formSchema.find(f => f.id === fieldId);
        if (field) {
            field.options.splice(idx, 1);
            saveHistory();
            renderOptionsEditor(field);
            renderCanvas();
        }
    };

    document.getElementById('btnAddOption').onclick = () => {
        if (!selectedFieldId) return;
        const field = formSchema.find(f => f.id === selectedFieldId);
        if (field) {
            field.options.push(`Option ${field.options.length + 1}`);
            saveHistory();
            renderOptionsEditor(field);
            renderCanvas();
        }
    };

    const updateFieldProp = (prop, val) => {
        if (!selectedFieldId) return;
        const field = formSchema.find(f => f.id === selectedFieldId);
        if (field) {
            field[prop] = val;
            renderCanvas();
        }
    };

    document.getElementById('optLabel').addEventListener('input', (e) => updateFieldProp('label', e.target.value));
    document.getElementById('optLabel').addEventListener('change', saveHistory);

    document.getElementById('optPlaceholder').addEventListener('input', (e) => updateFieldProp('placeholder', e.target.value));
    document.getElementById('optPlaceholder').addEventListener('change', saveHistory);

    document.getElementById('optClass').addEventListener('input', (e) => updateFieldProp('cssClass', e.target.value));
    document.getElementById('optClass').addEventListener('change', saveHistory);

    document.getElementById('optDefault').addEventListener('input', (e) => updateFieldProp('defaultValue', e.target.value));
    document.getElementById('optDefault').addEventListener('change', saveHistory);

    document.getElementById('optMin').addEventListener('input', (e) => updateFieldProp('min', e.target.value));
    document.getElementById('optMin').addEventListener('change', saveHistory);

    document.getElementById('optMax').addEventListener('input', (e) => updateFieldProp('max', e.target.value));
    document.getElementById('optMax').addEventListener('change', saveHistory);

    document.getElementById('optRequired').addEventListener('change', (e) => {
        updateFieldProp('required', e.target.checked);
        saveHistory();
    });

    function deleteField(id) {
        if (confirm('Remove this field?')) {
            formSchema = formSchema.filter(f => f.id !== id);
            if (selectedFieldId === id) {
                selectedFieldId = null;
                noFieldSelected.classList.remove('hidden');
                fieldSettings.classList.add('hidden');
                showAddFields();
            }
            saveHistory();
            renderCanvas();
            showToast('Field removed');
        }
    }

    document.getElementById('btnDeleteSelected').onclick = () => {
        if (selectedFieldId) deleteField(selectedFieldId);
    };

    function duplicateField(id) {
        const idx = formSchema.findIndex(f => f.id === id);
        if (idx !== -1) {
            const copy = JSON.parse(JSON.stringify(formSchema[idx]));
            copy.id = Date.now();
            formSchema.splice(idx + 1, 0, copy);
            saveHistory();
            renderCanvas();
            showToast('Field duplicated');
        }
    }

    function saveHistory() {
        if (historyIndex < historyStack.length - 1) {
            historyStack = historyStack.slice(0, historyIndex + 1);
        }
        historyStack.push(JSON.stringify(formSchema));
        historyIndex = historyStack.length - 1;
        updateUndoRedoBtns();
    }

    function undo() {
        if (historyIndex > 0) {
            historyIndex--;
            formSchema = JSON.parse(historyStack[historyIndex]);
            selectedFieldId = null;
            renderCanvas();
            updateUndoRedoBtns();
        }
    }

    function redo() {
        if (historyIndex < historyStack.length - 1) {
            historyIndex++;
            formSchema = JSON.parse(historyStack[historyIndex]);
            selectedFieldId = null;
            renderCanvas();
            updateUndoRedoBtns();
        }
    }

    function updateUndoRedoBtns() {
        document.getElementById('undoBtn').disabled = historyIndex <= 0;
        document.getElementById('redoBtn').disabled = historyIndex >= historyStack.length - 1;
    }

    document.getElementById('undoBtn').onclick = undo;
    document.getElementById('redoBtn').onclick = redo;

    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 'z') { e.preventDefault(); undo(); }
        if (e.ctrlKey && e.key === 'y') { e.preventDefault(); redo(); }
    });

    document.getElementById('btnCancel').onclick = () => {
        if (formSchema.length > 0 && confirm('Clear the form canvas?')) {
            formSchema = [];
            selectedFieldId = null;
            saveHistory();
            renderCanvas();
            showAddFields();
        }
    };

    document.getElementById('btnNext').onclick = () => {
        const title = document.getElementById('formTitle').value || 'Untitled Form';
        const output = { title, fields: formSchema };
        document.getElementById('jsonOutput').textContent = JSON.stringify(output, null, 2);
        document.getElementById('jsonModal').classList.remove('hidden');
        document.getElementById('jsonModal').classList.add('flex');
    };

    document.getElementById('btnCloseModal').onclick = () => {
        document.getElementById('jsonModal').classList.add('hidden');
        document.getElementById('jsonModal').classList.remove('flex');
    };

    document.getElementById('btnCopyJson').onclick = () => {
        navigator.clipboard.writeText(document.getElementById('jsonOutput').textContent).then(() => {
            showToast('Copied to clipboard');
        });
    };

    document.getElementById('formTitle').addEventListener('input', (e) => {
        const val = e.target.value;
        document.getElementById('titleCount').textContent = val.length;
        const slug = val.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'untitled-form';
        document.getElementById('submissionUrl').textContent = `/forms/${slug}`;
    });

    function showToast(msg) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'bg-gray-800 text-white px-4 py-2.5 rounded-lg shadow-lg text-sm flex items-center gap-2 transition-opacity duration-300';
        toast.innerHTML = `<svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ${msg}`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    document.getElementById('previewBtn').addEventListener('click', () => {
        isPreviewMode = !isPreviewMode;
        const btn = document.getElementById('previewBtn');
        const sidebar = document.querySelector('.lg\\:w-1\\/3');
        const canvasWrap = document.querySelector('.lg\\:w-2\\/3');

        if (isPreviewMode) {
            btn.textContent = 'Back to Editor';
            sidebar.classList.add('hidden');
            canvasWrap.classList.replace('lg:w-2/3', 'w-full');
            document.querySelectorAll('.field-input').forEach(el => el.removeAttribute('disabled'));
            document.querySelectorAll('.field-actions').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.form-element').forEach(el => { el.draggable = false; });
        } else {
            btn.textContent = 'Preview';
            sidebar.classList.remove('hidden');
            canvasWrap.classList.replace('w-full', 'lg:w-2/3');
            document.querySelectorAll('.field-input').forEach(el => el.setAttribute('disabled', 'true'));
            document.querySelectorAll('.field-actions').forEach(el => el.classList.remove('hidden'));
            renderCanvas();
        }
    });
});
