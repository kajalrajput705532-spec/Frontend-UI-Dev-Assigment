@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style type="text/tailwindcss">
    @layer utilities {
        .ghost-field { @apply opacity-30 bg-blue-50 border-2 border-dashed border-blue-400; }
        .drag-over { @apply bg-blue-50 border-blue-400 ring-2 ring-blue-200 !important; }
        .canvas-bg {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .card-shadow {
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
    }
</style>
<div class="content-wrapper bg-slate-50 min-h-screen p-8" style="margin-left: 260px; padding-top: 80px;">
    <div class="max-w-[1400px] mx-auto">
        
        <div class="bg-white rounded-xl card-shadow p-6 mb-6 border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="w-full md:w-1/2">
                <input type="text" id="formTitle" maxlength="200"
                       class="w-full text-3xl font-bold text-slate-800 border-none focus:ring-0 p-0 bg-transparent placeholder-slate-300"
                       placeholder="Contact Us Form">
                <div class="flex items-center mt-2 text-sm text-slate-500">
                    <!-- <span id="titleCount">0</span>/200 chars -->
                    <!-- <span class="mx-3 text-slate-300">|</span> -->
                    <!-- <span>Submission URL: <span id="submissionUrl" class="text-blue-500">/forms/untitled-form</span></span> -->
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <button id="previewBtn" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-colors text-sm">Preview</button>
                <div class="flex bg-slate-100 rounded-lg p-1">
                    <button id="undoBtn" class="p-1.5 text-slate-600 rounded hover:bg-white transition-all disabled:opacity-30" title="Undo (Ctrl+Z)" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    </button>
                    <button id="redoBtn" class="p-1.5 text-slate-600 rounded hover:bg-white transition-all disabled:opacity-30" title="Redo (Ctrl+Y)" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-2/3 flex flex-col" style="height: 75vh;">
                <div class="bg-white rounded-t-xl border border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-700">Form Canvas</h2>
                    <span class="text-xs text-slate-400">Drag fields here to build your form</span>
                </div>
                <div id="dropCanvas" class="flex-1 canvas-bg rounded-b-xl border border-t-0 border-slate-200 p-6 overflow-y-auto relative">
                    <div id="emptyState" class="absolute inset-0 m-6 flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-300 rounded-xl">
                        <svg class="w-10 h-10 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                        <p class="font-medium text-slate-500">Drag elements from the right panel</p>
                        <p class="text-sm text-slate-400 mt-1">to start building your form</p>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl border border-slate-200 card-shadow overflow-hidden sticky top-6" style="height: 75vh; display: flex; flex-direction: column;">
                    <div class="flex p-1.5 bg-slate-50 border-b border-slate-200">
                        <button id="tabAddFields" class="w-1/2 py-2 text-sm font-bold text-blue-700 bg-white rounded-lg shadow-sm transition-all">Add Fields</button>
                        <button id="tabFieldOptions" class="w-1/2 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 rounded-lg transition-all">Field Options</button>
                    </div>

                    <!-- Panel: Add Fields -->
                    <div id="panelAddFields" class="p-5 flex-1 overflow-y-auto">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Basic Elements</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="text">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Text Input</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="textarea">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Text Area</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="number">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <span class="font-black text-slate-500 group-hover:text-blue-600 text-lg leading-none">123</span>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Number</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="email">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Email</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="phone">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Phone</span>
                            </div>
                        </div>

                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-6 mb-4">Choice Elements</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="dropdown">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Dropdown</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="radio">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Radio</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="checkbox">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Checkbox</span>
                            </div>
                        </div>

                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-6 mb-4">Date & File</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="datepicker">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Date Picker</span>
                            </div>
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="fileupload">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">File Upload</span>
                            </div>
                        </div>

                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-6 mb-4">Structural Elements</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="palette-item bg-white border border-slate-200 rounded-xl p-4 text-center cursor-grab hover:border-blue-500 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 group" draggable="true" data-type="hidden">
                                <div class="bg-slate-50 p-2.5 rounded-lg group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Hidden</span>
                            </div>
                        </div>
                    </div>

                    <!-- Panel: Field Options -->
                    <div id="panelFieldOptions" class="hidden p-6 flex-1 overflow-y-auto bg-slate-50">
                        <div id="noFieldSelected" class="text-center text-slate-400 mt-16 flex flex-col items-center">
                            <div class="bg-white p-4 rounded-full shadow-sm mb-4 border border-slate-100">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                            </div>
                            <p class="font-medium text-slate-500">Select a field on the canvas<br>to edit its options.</p>
                        </div>
                        
                        <div id="fieldSettings" class="hidden space-y-5">
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                                <h3 class="font-extrabold text-slate-800 text-lg">Field Settings</h3>
                                <span id="settingsFieldType" class="text-xs font-bold px-3 py-1 bg-blue-100 text-blue-800 rounded-md uppercase tracking-wider">Text</span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Label</label>
                                    <input type="text" id="optLabel" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                </div>

                                <div class="opt-group" data-applies-to="text,textarea,number,email,phone">
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Placeholder</label>
                                    <input type="text" id="optPlaceholder" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                </div>

                                <div class="opt-group" data-applies-to="text,number,email,hidden">
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Default Value</label>
                                    <input type="text" id="optDefault" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                </div>

                                <div class="flex gap-4 opt-group" data-applies-to="text,textarea">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Min Length</label>
                                        <input type="number" id="optMin" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Max Length</label>
                                        <input type="number" id="optMax" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Custom CSS Class</label>
                                    <input type="text" id="optClass" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono transition-all bg-white" placeholder="e.g. mt-2 text-red-500">
                                </div>

                                <div class="flex items-center bg-white p-4 rounded-lg border border-slate-200 shadow-sm mt-2">
                                    <input type="checkbox" id="optRequired" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer transition-all">
                                    <label for="optRequired" class="ml-3 block text-sm text-slate-800 font-bold cursor-pointer">Make this field required</label>
                                </div>

                                <div class="opt-group pt-5 mt-2 border-t border-slate-200" data-applies-to="dropdown,radio,checkbox">
                                    <label class="block text-sm font-bold text-slate-700 mb-3">Options List</label>
                                    <div id="optionsListContainer" class="space-y-2.5 mb-4">
                                        <!-- Options will be generated here -->
                                    </div>
                                    <button type="button" id="btnAddOption" class="w-full py-2.5 border-2 border-dashed border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors text-sm font-bold flex justify-center items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add New Option
                                    </button>
                                </div>
                            </div>

                            <div class="pt-6 mt-4 border-t border-red-100">
                                <button type="button" id="btnDeleteSelected" class="w-full py-3 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded-lg font-bold transition-all flex justify-center items-center shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Delete This Element
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="mt-8 flex justify-between items-center bg-white p-5 rounded-2xl premium-shadow border border-slate-100 relative overflow-hidden">
            <button id="btnCancel" class="px-6 py-3 border-2 border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 hover:border-slate-300 font-bold transition-all flex items-center">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Clear Canvas
            </button>
            <button id="btnNext" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg hover:shadow-blue-500/30 hover:scale-105 font-bold transition-all flex items-center">
                Generate JSON Schema
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>

    </div>
</div>

<!-- Modal for JSON output -->
<div id="jsonModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden" id="jsonModalContent">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <h3 class="text-lg font-bold text-slate-800">Generated JSON Schema</h3>
            </div>
            <button id="btnCloseModal" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5">
            <pre id="jsonOutput" class="bg-gray-950 text-green-400 p-5 rounded-xl overflow-x-auto text-sm font-mono max-h-[420px] overflow-y-auto border border-gray-800 whitespace-pre-wrap"></pre>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-xs text-slate-400 font-medium">Copy and paste this into your backend handler</span>
            <button id="btnCopyJson" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold transition-all flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                Copy to Clipboard
            </button>
        </div>
    </div>
</div>

<!-- TEMPLATES FOR BLADE COMPONENTS -->
<div id="fieldTemplates" class="hidden">
    <template id="tpl-text"><x-field-text /></template>
    <template id="tpl-textarea"><x-field-textarea /></template>
    <template id="tpl-number"><x-field-number /></template>
    <template id="tpl-email"><x-field-email /></template>
    <template id="tpl-phone"><x-field-phone /></template>
    <template id="tpl-dropdown"><x-field-dropdown /></template>
    <template id="tpl-radio"><x-field-radio /></template>
    <template id="tpl-checkbox"><x-field-checkbox /></template>
    <template id="tpl-datepicker"><x-field-datepicker /></template>
    <template id="tpl-fileupload"><x-field-fileupload /></template>
    <template id="tpl-hidden"><x-field-hidden /></template>
</div>

<!-- Add Toast Notification Container -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

<script src="{{ asset('js/form-builder.js') }}"></script>
@endsection