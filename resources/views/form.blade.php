@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card-body">

            <div class="mb-3" style="width: 60%;">
                <input type="text" id="formTitle" maxlength="200"
                       class="form-control form-control-lg"
                       placeholder="Untitled Form">

                <small class="text-muted">
                    <span id="titleCount">0</span>/200 characters
                </small>

                <div class="text-muted mt-1">
                    Submission URL:  <a href="#">{{ $title }}</a>
                </div>
            </div>

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Form Editor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
            </ul>

            <div class="row">
                <div class="col-md-8">
                    <div id="dropCanvas" class="border border-dashed rounded p-4 bg-light" style="min-height: 400px;">
                        <div id="emptyState" class="text-center text-muted mt-5">
                            Drag elements from the right panel to build your form →
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <ul class="nav nav-pills mb-3">
                                <li class="nav-item">
                                    <button id="addFieldsTab" class="nav-link active" type="button">Add Fields</button>
                                </li>
                                <li class="nav-item">
                                    <button id="fieldOptionsTab" class="nav-link" type="button">Field Options</button>
                                </li>
                            </ul>

                            <div id="addFieldsPanel" class="row g-2">
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="text">Text Input</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="textarea">Text Area</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="number">Number Input</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="email">Email Input</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="phone">Phone Input</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="dropdown">Dropdown</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="radio">Radio Buttons</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="checkbox">Checkboxes</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="date">Date Picker</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="file">File Upload</div></div>
                                <div class="col-6"><div class="field-tile" draggable="true" data-type="title">Title</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="description">Description</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="newline">New Line</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="pagebreak">Page Break</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="hidden">Hidden Field</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="state">State</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="city">City</div></div>

                                <div class="col-6"><div class="field-tile" draggable="true" data-type="statecity">State & City</div></div>
                            </div>

                            <div id="fieldOptionsPanel" class="d-none">
                                <h6>Field Options</h6>

                                <label class="form-label">Label</label>
                                <input type="text" id="optionLabel" class="form-control mb-3">

                                <label class="form-label">Placeholder</label>
                                <input type="text" id="optionPlaceholder" class="form-control mb-3">

                                <label class="form-label">CSS Class</label>
                                <input type="text" id="optionClass" class="form-control mb-3">

                                <div class="form-check mb-3">
                                    <input type="checkbox" id="optionRequired" class="form-check-input">
                                    <label class="form-check-label" for="optionRequired">Required</label>
                                </div>

                                <button class="btn btn-danger w-100" onclick="deleteField(selectedFieldId)">
                                    Remove Element
                                </button>
                            </div>
							<div id="textConfigWrapper">
								<label class="form-label">Min Characters</label>
								<input type="number" id="optionMin" class="form-control mb-3">

								<label class="form-label">Max Characters</label>
								<input type="number" id="optionMax" class="form-control mb-3">

								<label class="form-label">Default Value</label>
								<input type="text" id="optionDefault" class="form-control mb-3">
							</div>
							<div id="optionsWrapper" class="d-none mb-3">
								<label class="form-label">Options</label>

								<div id="optionsList"></div>

								<button type="button"
										class="btn btn-sm btn-outline-primary mt-2"
										onclick="addOption()">
									+ Add Option
								</button>
							</div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
                <button class="btn btn-primary" id="nextBtn">Next</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="jsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Form JSON Schema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <pre id="jsonOutput"
                     style="max-height:500px;overflow:auto;background:#f8f9fa;padding:15px;border-radius:5px;">
                </pre>
            </div>

        </div>
    </div>
</div>
</div>
<style>
    .field-tile {
        border: 1px solid #ddd;
        background: #fff;
        padding: 12px;
        border-radius: 8px;
        cursor: grab;
        text-align: center;
        font-size: 14px;
    }

    .field-tile:hover {
        background: #f1f5ff;
        border-color: #0d6efd;
    }

    .border-dashed {
        border-style: dashed !important;
    }
	.content-wrapper {
        margin-left: 260px !important;
        padding: 20px;
        padding-top: 60px !important; /* Fix top navbar overlap */
        min-height: 100vh;
        box-sizing: border-box;
    }

    @media (max-width: 1024px) {
        .content-wrapper {
            margin-left: 0 !important;
            padding: 15px;
            padding-top: 80px !important;
        }
    }

    .form-builder-card {
        max-width: 100%;
        overflow: hidden;
    }

    #dropCanvas {
        min-height: 420px;
        background: #f8f9fc !important;
    }

    .field-card {
        background: #fff;
        border: 1px solid #0d6efd;
        border-radius: 10px;
    }

    #dropCanvas.drag-over {
        border: 2px dashed #0d6efd !important;
        background: #eef5ff !important;
    }
</style>

<script>
    let draggedFieldType = null;
    let formFields = [];
    let selectedFieldId = null;

    const fieldLabels = {
        text: 'Text Input',
        textarea: 'Text Area',
        number: 'Number Input',
        email: 'Email Input',
        phone: 'Phone Input',
        dropdown: 'Dropdown',
        radio: 'Radio Buttons',
        checkbox: 'Checkboxes',
        date: 'Date Picker',
        file: 'File Upload',
        title: 'Title',
        description: 'Description',
        newline: 'New Line',
        pagebreak: 'Page Break',
        hidden: 'Hidden Field',
        state: 'State',
        city: 'City',
        statecity: 'State & City',
    };

    document.getElementById('formTitle').addEventListener('input', function () {
        document.getElementById('titleCount').innerText = this.value.length;
    });

    document.querySelectorAll('.field-tile').forEach(tile => {
        tile.addEventListener('dragstart', function () {
            draggedFieldType = this.dataset.type;
        });
    });

    const dropCanvas = document.getElementById('dropCanvas');

    dropCanvas.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropCanvas.classList.add('drag-over');
    });

    dropCanvas.addEventListener('dragleave', function () {
        dropCanvas.classList.remove('drag-over');
    });

    dropCanvas.addEventListener('drop', function (e) {
        e.preventDefault();

        if (!draggedFieldType) return;

        const newField = {
            id: Date.now(),
            type: draggedFieldType,
            label: fieldLabels[draggedFieldType],
            placeholder: '',
            cssClass: '',
            required: false,
            options: ['Option 1', 'Option 2'],
            min: '',
            max: '',
            defaultValue: '',
        };

        formFields.push(newField);
        renderFields();
    });

    function renderOptionsList(field) {
        const optionsList = document.getElementById('optionsList');
        optionsList.innerHTML = '';

        field.options.forEach((option, index) => {
            optionsList.innerHTML += `
                <div class="input-group mb-2">
                    <input type="text"
                        class="form-control"
                        value="${option}"
                        oninput="updateOption(${index}, this.value)">

                    <button type="button"
                            class="btn btn-outline-danger"
                            onclick="removeOption(${index})">
                        ×
                    </button>
                </div>`;
        });
    }

    function updateOption(index, value) {
        const field = formFields.find(item => item.id === selectedFieldId);
        if (!field) return;

        field.options[index] = value;
        renderFields();
    }

    function addOption() {
        const field = formFields.find(item => item.id === selectedFieldId);
        if (!field) return;

        field.options.push('New Option');
        renderOptionsList(field);
        renderFields();
    }

    function removeOption(index) {
        const field = formFields.find(item => item.id === selectedFieldId);
        if (!field) return;

        field.options.splice(index, 1);
        renderOptionsList(field);
        renderFields();
    }

    function renderFields() {
        dropCanvas.innerHTML = '';

        if (formFields.length === 0) {
            dropCanvas.innerHTML = `
                <div id="emptyState" class="text-center text-muted mt-5">
                    Drag elements from the right panel to build your form →
                </div>`;
            return;
        }

        formFields.forEach(field => {
            const card = document.createElement('div');
            card.className = 'field-card p-3 mb-3';

            card.innerHTML = `
                <div class="d-flex justify-content-between mb-2">
                    <strong>
                        ${field.label}
                        ${field.required ? '<span class="text-danger">*</span>' : ''}
                    </strong>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="moveUp(${field.id})">⬆️</button>

                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="moveDown(${field.id})">⬇️</button>

                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editField(${field.id})">✏️</button>

                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="duplicateField(${field.id})">📄</button>

                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteField(${field.id})">🗑️</button>
                    </div>
                </div>
                ${getFieldPreview(field)}`;

            dropCanvas.appendChild(card);
        });
    }

    function getFieldPreview(field) {
        if (field.type === 'textarea') {
            return `<textarea class="form-control ${field.cssClass}" placeholder="${field.placeholder}" disabled></textarea>`;
        }

    if (field.type === 'dropdown') {
        return `
            <select class="form-control ${field.cssClass}" disabled>
                ${field.options.map(option => `<option>${option}</option>`).join('')}
            </select>
        `;
    }

    if (field.type === 'radio') {
        return `
            <div class="${field.cssClass}">
                ${field.options.map(option => `
                    <label class="me-3">
                        <input type="radio" disabled> ${option}
                    </label>
                `).join('')}
            </div>
        `;
    }

    if (field.type === 'checkbox') {
        return `
            <div class="${field.cssClass}">
                ${field.options.map(option => `
                    <label class="me-3">
                        <input type="checkbox" disabled> ${option}
                    </label>
                `).join('')}
            </div>
        `;
    }

    if (field.type === 'date') {
        return `<input type="date" class="form-control ${field.cssClass}" disabled>`;
    }

    if (field.type === 'file') {
        return `<input type="file" class="form-control ${field.cssClass}" disabled>`;
    }
    if (field.type === 'title') {
        return `<h3>${field.label}</h3>`;
    }

    if (field.type === 'description') {
        return `<p>${field.defaultValue || 'Description Text'}</p>`;
    }

    if (field.type === 'newline') {
        return `<br>`;
    }

    if (field.type === 'pagebreak') {
        return `<hr style="border-top:2px dashed #999;">`;
    }

    if (field.type === 'hidden') {
        return `
            <input type="text"
                class="form-control"
                value="${field.defaultValue || ''}"
                disabled>
        `;
    }

    if (field.type === 'state') {
        return `
            <select class="form-control" disabled>
                <option>Gujarat</option>
                <option>Maharashtra</option>
            </select>
        `;
    }

    if (field.type === 'city') {
        return `
            <select class="form-control" disabled>
                <option>Ahmedabad</option>
                <option>Rajkot</option>
            </select>
        `;
    }

    if (field.type === 'statecity') {
        return `
            <div class="row">
                <div class="col-md-6">
                    <select class="form-control" disabled>
                        <option>State</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control" disabled>
                        <option>City</option>
                    </select>
                </div>
            </div>
        `;
    }

        return `<input type="${field.type}" class="form-control ${field.cssClass}" placeholder="${field.placeholder}" disabled>`;
    }

    function editField(id) {
        selectedFieldId = id;

        const field = formFields.find(item => item.id === id);
        const isOptionField = ['dropdown', 'radio', 'checkbox'].includes(field.type);
        if (!field) return;

        document.getElementById('addFieldsPanel').classList.add('d-none');
        document.getElementById('fieldOptionsPanel').classList.remove('d-none');

        document.getElementById('addFieldsTab').classList.remove('active');
        document.getElementById('fieldOptionsTab').classList.add('active');

        document.getElementById('optionLabel').value = field.label;
        document.getElementById('optionPlaceholder').value = field.placeholder || '';
        document.getElementById('optionClass').value = field.cssClass || '';
        document.getElementById('optionRequired').checked = field.required || false;
        document.getElementById('optionsWrapper').classList.toggle('d-none', !isOptionField);

        if (isOptionField) {
            renderOptionsList(field);
        }
        document.getElementById('optionMin').value = field.min || '';
        document.getElementById('optionMax').value = field.max || '';
        document.getElementById('optionDefault').value = field.defaultValue || '';
    }

    function duplicateField(id) {
        const index = formFields.findIndex(field => field.id === id);
        if (index === -1) return;

        const copiedField = {
            ...formFields[index],
            id: Date.now()
        };

        formFields.splice(index + 1, 0, copiedField);
        renderFields();
    }

    function deleteField(id) {
        formFields = formFields.filter(field => field.id !== id);

        if (selectedFieldId === id) {
            selectedFieldId = null;
            showAddFieldsPanel();
        }

        renderFields();
    }

    function updateSelectedField() {
        const field = formFields.find(item => item.id === selectedFieldId);
        if (!field) return;

        field.label = document.getElementById('optionLabel').value;
        field.placeholder = document.getElementById('optionPlaceholder').value;
        field.cssClass = document.getElementById('optionClass').value;
        field.required = document.getElementById('optionRequired').checked;
        field.min = document.getElementById('optionMin').value;
        field.max = document.getElementById('optionMax').value;
        field.defaultValue = document.getElementById('optionDefault').value;

        renderFields();
    }

    document.getElementById('optionLabel').addEventListener('input', updateSelectedField);
    document.getElementById('optionPlaceholder').addEventListener('input', updateSelectedField);
    document.getElementById('optionClass').addEventListener('input', updateSelectedField);
    document.getElementById('optionRequired').addEventListener('change', updateSelectedField);

    document.getElementById('addFieldsTab').addEventListener('click', showAddFieldsPanel);
    document.getElementById('optionMin').addEventListener('input', updateSelectedField);
    document.getElementById('optionMax').addEventListener('input', updateSelectedField);
    document.getElementById('optionDefault').addEventListener('input', updateSelectedField);

    function showAddFieldsPanel() {
        document.getElementById('addFieldsPanel').classList.remove('d-none');
        document.getElementById('fieldOptionsPanel').classList.add('d-none');

        document.getElementById('addFieldsTab').classList.add('active');
        document.getElementById('fieldOptionsTab').classList.remove('active');
    }

    document.getElementById('nextBtn').addEventListener('click', function () {

        const formSchema = {
            title: document.getElementById('formTitle').value,
            fields: formFields
        };

        document.getElementById('jsonOutput').textContent =
            JSON.stringify(formSchema, null, 2);

        const modal = new bootstrap.Modal(
            document.getElementById('jsonModal')
        );

        modal.show();
    });

    document.getElementById('cancelBtn').addEventListener('click', function () {
        if (confirm('Are you sure you want to clear the form?')) {
            formFields = [];
            selectedFieldId = null;

            document.getElementById('formTitle').value = '';
            document.getElementById('titleCount').innerText = '0';

            showAddFieldsPanel();
            renderFields();
        }
    });

    function moveUp(id) {
        const index = formFields.findIndex(f => f.id === id);

        if (index <= 0) return;

        [formFields[index], formFields[index - 1]] =
        [formFields[index - 1], formFields[index]];

        renderFields();
    }

    function moveDown(id) {
        const index = formFields.findIndex(f => f.id === id);

        if (index === -1 || index === formFields.length - 1) return;

        [formFields[index], formFields[index + 1]] =
        [formFields[index + 1], formFields[index]];

        renderFields();
    }
</script>
@endsection