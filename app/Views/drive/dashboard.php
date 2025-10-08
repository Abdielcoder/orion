<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Explorador de Archivos - Drive</title>
  <!-- Font Awesome Icons - Local -->
  <link rel="stylesheet" href="/biblioteca/public/assets/css/fontawesome.min.css">
  <link rel="stylesheet" href="/biblioteca/public/assets/css/theme.css">
  <style>
    * { box-sizing: border-box; }
    body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Ubuntu, Cantarell, 'Noto Sans', sans-serif; margin: 0; background: #1e1e1e; color: #ffffff; overflow: hidden; }

    /* Light Theme Overrides */
    body.light-theme { background: #f7f7f9; color: #1f2937; }
    body.light-theme .header { background: #ffffff; border-bottom-color: #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04); }
    body.light-theme .header .logo { color: #0e7490; }
    body.light-theme .header .logo:hover { background: #f0f9ff; color: #0c5f75; }
    body.light-theme .header .user { color: #6b7280; background: #f9fafb; }
    body.light-theme .header .user:hover { background: #f3f4f6; color: #374151; }
    body.light-theme .header .btn { background: transparent; color: #111827; border: 1px solid #e5e7eb; }
    body.light-theme .header .btn:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
    /* Light theme styles for sidebar storage quota */
    body.light-theme .storage-quota-sidebar { 
      background: rgba(14, 116, 144, 0.1);
      border-color: rgba(14, 116, 144, 0.3);
    }
    body.light-theme .storage-quota-sidebar:hover {
      background: rgba(14, 116, 144, 0.15);
      border-color: #0e7490;
      box-shadow: 0 2px 12px rgba(14, 116, 144, 0.2);
    }
    body.light-theme .quota-header-sidebar { color: #374151; }
    body.light-theme .quota-header-sidebar i { color: #0e7490; }
    body.light-theme .quota-bar-sidebar { background: #e5e7eb; }
    body.light-theme .quota-text-sidebar { color: #374151; }
    body.light-theme .sidebar-divider { background: #e5e7eb; }
    
    /* Light theme para modal de vista previa */
    body.light-theme .preview-modal-content { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .preview-modal-header { background: #f9fafb; border-bottom-color: #e5e7eb; }
    body.light-theme .preview-modal-title { color: #111827; }
    body.light-theme .preview-modal-close { color: #6b7280; }
    body.light-theme .preview-modal-close:hover { background: #f3f4f6; color: #111827; }
    body.light-theme .preview-modal-loading { color: #6b7280; }
    body.light-theme .preview-modal-footer { background: #f9fafb; border-top-color: #e5e7eb; }
    body.light-theme .preview-modal-body .file-info-large { color: #6b7280; }
    body.light-theme .preview-modal-body .file-info-large h3 { color: #111827; }
    body.light-theme .document-header { background: #0e7490; }
    body.light-theme .pdf-container::before { background: linear-gradient(90deg, #0e7490, #0891b2); }

    body.light-theme .main { background: #f7f7f9; }
    body.light-theme .sidebar { background: #ffffff; border-right-color: #e5e7eb; }
    body.light-theme .sidebar-header { color: #6b7280; border-bottom-color: #e5e7eb; }
    body.light-theme .tree-item:hover { background: #f3f4f6; }
    body.light-theme .tree-item.active { background: #dbeafe; }

    body.light-theme .content { background: #fafafa; }
    body.light-theme .toolbar { background: #ffffff; border-bottom-color: #e5e7eb; }
    body.light-theme .toolbar .btn { background: transparent; color: #6b7280; }
    body.light-theme .toolbar .btn:hover { background: #f3f4f6; color: #374151; }
    body.light-theme .toolbar .separator { background: #e5e7eb; }
    body.light-theme .view-toggle button { background: transparent; color: #6b7280; }
    body.light-theme .view-toggle button:hover { background: #f3f4f6; color: #374151; }
    body.light-theme .view-toggle button.active { background: #e5e7eb; color: #111827; }

    body.light-theme .breadcrumb { background: #ffffff; border-bottom-color: #e5e7eb; color: #374151; }
    body.light-theme .breadcrumb a { color: #0ea5e9; }
    body.light-theme .breadcrumb .sep { color: #9ca3af; }

    body.light-theme .explorer { background: #fafafa; }

    /* Grid */
    body.light-theme .grid-item:hover { background: #f3f4f6; }
    body.light-theme .grid-item .name { color: #111827; }
    body.light-theme .grid-item .label { color: #fff; }

    /* List */
    body.light-theme .list-header { background: #ffffff; border-bottom-color: #e5e7eb; color: #374151; }
    body.light-theme .list-item { border-bottom-color: #e5e7eb; }
    body.light-theme .list-item:hover { background: #f3f4f6; }

    /* Columns */
    body.light-theme .column { background: #ffffff; border-right-color: #e5e7eb; }
    body.light-theme .column-header { background: #f9fafb; border-bottom-color: #e5e7eb; color: #374151; }
    body.light-theme .column-item { border-bottom-color: rgba(229, 231, 235, 0.7); }
    body.light-theme .column-item:hover { background: #f3f4f6; }

    /* Context menu */
    body.light-theme .context-menu { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .context-menu-item:hover { background: #e5f2ff; }
    body.light-theme .context-menu-separator { background: #e5e7eb; }

    /* Modals */
    body.light-theme .modal { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .modal input { background: #ffffff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .modal label { color: #374151; }
    body.light-theme .modal-buttons .secondary { background: #4a4a4a; color: #ffffff; }
    body.light-theme .modal-buttons .primary { background: #4a4a4a; color: #ffffff; }
    body.light-theme .move-info { background: #f3f4f6; }

    /* Upload */
    body.light-theme .upload-area { border-color: #e5e7eb; color: #6b7280; }
    body.light-theme .upload-area.drag-over { background: #e6f6ff; color: #0284c7; border-color: #0284c7; }
    body.light-theme .upload-progress { background: #e5e7eb; }
    body.light-theme .upload-progress-bar { background: #0ea5e9; }

    /* Selection box */
    body.light-theme .selection-box { border-color: #0284c7; background: rgba(14, 165, 233, 0.25); box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.65) inset; }

    /* Trash bin */
    body.light-theme .trash-bin { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .trash-bin:hover { background: #f9fafb; }
    
    /* Shared files info */
    body.light-theme .shared-info { color: #6b7280; }
    body.light-theme .shared-info .owner { color: #374151; }
    body.light-theme .shared-info .permission { color: #059669; }
    body.light-theme .shared-info .shared-date { color: #9ca3af; }
    
    /* Shared files list view specific styles */
    .list-item.shared-file { 
      grid-template-columns: 1fr 150px 120px 100px 80px; 
    }
    .list-item .owner,
    .list-item .permission,
    .list-item .shared-date {
      font-size: 12px;
      display: flex;
      align-items: center;
      color: #888;
    }
    .list-item .permission {
      color: #4ade80;
    }
    body.light-theme .list-item .owner,
    body.light-theme .list-item .shared-date {
      color: #6b7280;
    }
    body.light-theme .list-item .permission {
      color: #059669;
    }
    
    /* Shared indicator */
    .shared-indicator {
      position: absolute;
      top: 4px;
      right: 4px;
      background: #0ea5e9;
      color: white;
      border-radius: 50%;
      width: 16px;
      height: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      z-index: 2;
    }
    .grid-item { position: relative; }
    .list-item { position: relative; }
    body.light-theme .shared-indicator {
      background: #0ea5e9;
    }
    
    /* Shared indicator for list view */
    .list-item .shared-indicator {
      position: absolute;
      top: 50%;
      right: 8px;
      transform: translateY(-50%);
      background: transparent;
      color: #0ea5e9;
      border-radius: 0;
      width: auto;
      height: auto;
      font-size: 12px;
    }
    body.light-theme .list-item .shared-indicator {
      color: #0ea5e9;
    }
    
    /* Special styling for shared folders */
    .grid-item.shared-folder .icon i {
      color: #0ea5e9;
    }
    body.light-theme .grid-item.shared-folder .icon i {
      color: #0ea5e9;
    }
    
    /* Shared folder hover effect */
    .grid-item.shared-folder:hover .icon i {
      color: #0284c7;
    }
    body.light-theme .grid-item.shared-folder:hover .icon i {
      color: #0284c7;
    }
    
    /* List view shared folders */
    .list-item.shared-folder .icon i {
      color: #0ea5e9;
    }
    body.light-theme .list-item.shared-folder .icon i {
      color: #0ea5e9;
    }
    
    /* Preview Panel */
    .preview-panel {
      position: fixed;
      top: 48px;
      right: -400px;
      width: 400px;
      height: calc(100vh - 48px);
      background: #1e1e1e;
      border-left: 1px solid #3e3e42;
      z-index: 1000;
      transition: right 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    .preview-panel.open {
      right: 0;
    }
    .preview-panel:hover {
      background: #252526;
    }
    body.light-theme .preview-panel {
      background: #ffffff;
      border-left-color: #e5e7eb;
    }
    body.light-theme .preview-panel:hover {
      background: #f9fafb;
    }
    
    .preview-header {
      padding: 16px;
      border-bottom: 1px solid #3e3e42;
      display: grid;
      grid-template-columns: 1fr auto; /* título flexible + botón fijo */
      gap: 8px;
      align-items: center;
      position: relative;
    }
    
    .preview-expand-hint {
      position: absolute;
      top: 50%;
      right: 45px;
      transform: translateY(-50%);
      color: #007acc;
      font-size: 12px;
      opacity: 0;
      transition: opacity 0.2s ease;
      pointer-events: none;
    }
    
    .preview-panel:hover .preview-expand-hint {
      opacity: 1;
    }
    body.light-theme .preview-header {
      border-bottom-color: #e5e7eb;
    }
    
    .preview-title {
      font-weight: 600;
      font-size: 14px;
      color: #ffffff;
      min-width: 0;                /* necesario para truncado en grid/flex */
      overflow: hidden;            /* oculta exceso */
      text-overflow: ellipsis;     /* agrega … */
      white-space: nowrap;         /* una sola línea */
      max-width: 100%;
    }
    body.light-theme .preview-title {
      color: #111827;
    }
    
    .preview-close {
      background: none;
      border: none;
      color: #888;
      cursor: pointer;
      font-size: 18px;
      padding: 4px;
      border-radius: 3px;
    }
    .preview-close:hover {
      background: #3e3e42;
      color: #fff;
    }
    body.light-theme .preview-close {
      color: #6b7280;
    }
    body.light-theme .preview-close:hover {
      background: #f3f4f6;
      color: #111827;
    }
    
    .preview-content {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
    }
    
    .preview-viewer {
      width: 100%;
      max-height: 300px;
      border-radius: 6px;
      overflow: hidden;
      background: #2d2d30;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    body.light-theme .preview-viewer {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
    }
    
    .preview-viewer img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .preview-viewer iframe {
      width: 100%;
      height: 300px;
      border: none;
    }
    
    .preview-viewer .file-icon {
      font-size: 48px;
      color: #888;
      text-align: center;
      padding: 40px;
    }
    body.light-theme .preview-viewer .file-icon {
      color: #6b7280;
    }
    
    .preview-metadata {
      background: #2d2d30;
      border-radius: 6px;
      padding: 12px;
    }
    body.light-theme .preview-metadata {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
    }
    
    .metadata-item {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      border-bottom: 1px solid #3e3e42;
      font-size: 13px;
    }
    .metadata-item:last-child {
      border-bottom: none;
    }
    body.light-theme .metadata-item {
      border-bottom-color: #e5e7eb;
    }
    
    .metadata-label {
      color: #888;
      font-weight: 500;
    }
    .metadata-value {
      color: #ffffff;
      text-align: right;
      max-width: 200px;
      word-break: break-word;
    }
    body.light-theme .metadata-label {
      color: #6b7280;
    }
    body.light-theme .metadata-value {
      color: #111827;
    }
    
    /* Adjust main content when preview is open */
    .main.preview-open {
      padding-right: 400px;
    }
    
    /* Ensure preview panel is visible in column view */
    .column-view.active ~ .preview-panel,
    .preview-panel {
      z-index: 1001 !important;
      position: fixed !important;
    }
    
    body.light-theme .trash-bin i { color: #9ca3af; }
    body.light-theme .trash-bin.drag-over i { color: #ffffff; }

    /* Active selections - higher contrast for light theme */
    body.light-theme .grid-item.selected,
    body.light-theme .list-item.selected,
    body.light-theme .column-item.selected {
      background: #bfdbfe !important; /* blue-200 */
      border-color: #3b82f6;          /* blue-500 */
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.35);
      color: #0f172a;                 /* slate-900 */
    }
    body.light-theme .grid-item.selected .name { color: #0f172a !important; }
    body.light-theme .list-item.selected { border-left-color: #3b82f6; }
    body.light-theme .column-item.selected { border-left-color: #3b82f6; }
    
    /* Font Awesome Icons */
    i { display: inline-block; }
    .fas, .far, .fab { font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands"; font-weight: 900; }
    
    /* Header - Rediseño moderno */
    .header { 
      height: 56px; 
      background: #ffffff; 
      border-bottom: 1px solid #e5e7eb; 
      display: flex; 
      align-items: center; 
      padding: 0 24px; 
      gap: 16px; 
      position: relative; 
      z-index: 100000;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    
    .header .logo { 
      font-weight: 700; 
      color: #0e7490; 
      display: flex; 
      align-items: center; 
      gap: 10px;
      font-size: 16px;
      letter-spacing: 0.05em;
      transition: all 0.2s ease;
      padding: 8px 12px;
      border-radius: 8px;
    }
    
    .header .logo:hover {
      background: #f0f9ff;
      color: #0c5f75;
    }
    
    .header .logo i { 
      width: 22px; 
      height: 22px; 
      font-size: 20px;
    }
    /* Storage Quota in Sidebar */
    .storage-quota-sidebar { 
      margin: 16px 12px 24px 12px; /* Bajar 2x la separación bajo el bloque */
      padding: 10px 12px;
      background: rgba(0, 122, 204, 0.1);
      border: 1px solid rgba(0, 122, 204, 0.3);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .storage-quota-sidebar:hover {
      background: rgba(0, 122, 204, 0.15);
      border-color: #007acc;
      transform: translateY(-1px);
      box-shadow: 0 2px 12px rgba(0, 122, 204, 0.2);
    }
    .quota-header-sidebar {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: #cccccc;
      font-weight: 600;
      margin-bottom: 8px;
    }
    .quota-header-sidebar i {
      width: 14px;
      height: 14px;
      color: #007acc;
    }
    .quota-bar-sidebar { 
      height: 8px; 
      background: #3e3e42; 
      border-radius: 4px; 
      overflow: hidden; 
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
      margin-bottom: 8px;
    }
    .quota-fill-sidebar { 
      height: 100%; 
      transition: width 0.3s ease; 
      border-radius: 4px; 
      position: relative;
    }
    .quota-fill-sidebar.normal { 
      background: linear-gradient(90deg, #28a745, #20c997); 
    }
    .quota-fill-sidebar.warning { 
      background: linear-gradient(90deg, #ffc107, #fd7e14); 
    }
    .quota-fill-sidebar.danger { 
      background: linear-gradient(90deg, #dc3545, #e74c3c); 
      animation: pulse-danger 2s infinite;
    }
    .quota-text-sidebar { 
      font-size: 11px; 
      color: #cccccc; 
      text-align: center;
      font-weight: 500;
      line-height: 1.3;
    }
    .sidebar-divider {
      height: 1px;
      background: #3e3e42;
      margin: 8px 12px;
      opacity: 0.5;
    }
    
    @keyframes pulse-danger {
      0% { opacity: 1; }
      50% { opacity: 0.7; }
      100% { opacity: 1; }
    }
    
    @keyframes slideIn {
      from { 
        transform: translateX(100%); 
        opacity: 0; 
      }
      to { 
        transform: translateX(0); 
        opacity: 1; 
      }
    }
    
    @keyframes slideOut {
      from { 
        transform: translateX(0); 
        opacity: 1; 
      }
      to { 
        transform: translateX(100%); 
        opacity: 0; 
      }
    }
    
    /* Modal de Vista Previa Expandida */
    .preview-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 10000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .preview-modal-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(3px);
    }
    
    .preview-modal-content {
      position: relative;
      background: #2d2d30;
      border-radius: 12px;
      max-width: 90vw;
      max-height: 90vh;
      width: 900px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      border: 1px solid #3e3e42;
    }
    
    .preview-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid #3e3e42;
      background: #252526;
      border-radius: 12px 12px 0 0;
    }
    
    .preview-modal-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 16px;
      font-weight: 600;
      color: #ffffff;
    }
    
    .preview-modal-title i {
      color: #007acc;
      font-size: 18px;
    }
    
    .preview-modal-close {
      background: none;
      border: none;
      color: #cccccc;
      font-size: 20px;
      cursor: pointer;
      padding: 8px;
      border-radius: 4px;
      transition: all 0.2s ease;
    }
    
    .preview-modal-close:hover {
      background: #3e3e42;
      color: #ffffff;
    }
    
    .preview-modal-body {
      flex: 1;
      padding: 20px;
      overflow: auto;
      min-height: 400px;
      max-height: 70vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .preview-modal-loading {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      color: #cccccc;
      font-size: 14px;
    }
    
    .preview-modal-loading i {
      font-size: 24px;
      color: #007acc;
    }
    
    .preview-modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      padding: 16px 20px;
      border-top: 1px solid #3e3e42;
      background: #252526;
      border-radius: 0 0 12px 12px;
    }
    
    /* Contenido específico del modal */
    .preview-modal-body img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    
    .preview-modal-body iframe {
      width: 100%;
      height: 60vh;
      border: none;
      border-radius: 8px;
      background: #ffffff;
    }
    
    .preview-modal-body .document-preview {
      width: 100%;
      height: 60vh;
      background: #ffffff;
      border-radius: 8px;
      padding: 20px;
      overflow: auto;
      color: #333333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
    }
    
    .preview-modal-body .file-info-large {
      text-align: center;
      color: #cccccc;
    }
    
    .preview-modal-body .file-info-large .file-icon-large {
      font-size: 80px;
      color: #007acc;
      margin-bottom: 20px;
    }
    
    .preview-modal-body .file-info-large h3 {
      color: #ffffff;
      margin-bottom: 10px;
      font-size: 24px;
    }
    
    .preview-modal-body .file-info-large p {
      margin: 5px 0;
      opacity: 0.8;
    }
    
    /* Contenedor de PDF */
    .pdf-container {
      width: 100%;
      height: 60vh;
      position: relative;
      border-radius: 8px;
      overflow: hidden;
      background: #ffffff;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .pdf-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, #007acc, #0066cc);
      z-index: 1;
    }
    
    /* Header de documento de texto */
    .document-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      background: #007acc;
      color: white;
      border-radius: 8px 8px 0 0;
      margin-bottom: 0;
      font-weight: 500;
    }
    
    .document-header i {
      color: rgba(255, 255, 255, 0.9);
    }
    
    .document-header small {
      margin-left: auto;
      opacity: 0.8;
      font-size: 11px;
    }
    
    /* Íconos de archivos con colores */
    .fa-file-word { color: #2b579a !important; }
    .fa-file-excel { color: #217346 !important; }
    .fa-file-powerpoint { color: #d24726 !important; }
    .fa-file-pdf { color: #dc3545 !important; }
    .fa-file-image { color: #6f42c1 !important; }
    .fa-file-video { color: #fd7e14 !important; }
    .fa-file-audio { color: #20c997 !important; }
    .fa-file-archive { color: #ffc107 !important; }
    .fa-file-code { color: #6c757d !important; }
    .fa-file-alt { color: #6c757d !important; }
    .fa-file-csv { color: #28a745 !important; }
    .fa-font { color: #6c757d !important; }
    .fa-calendar { color: #007bff !important; }
    .header .user { 
      font-size: 14px; 
      color: #6b7280; 
      display: flex; 
      align-items: center; 
      gap: 8px;
      margin-left: auto;
      padding: 8px 16px;
      background: #f9fafb;
      border-radius: 10px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .header .user:hover {
      background: #f3f4f6;
      color: #374151;
    }
    
    .header .user i { 
      width: 18px; 
      height: 18px;
      color: #0e7490;
    }
    
    .header .btn { 
      background: transparent; 
      color: #111827; 
      border: 1px solid #e5e7eb; 
      border-radius: 10px; 
      padding: 8px 16px; 
      font-size: 14px; 
      cursor: pointer; 
      display: flex; 
      align-items: center; 
      gap: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .header .btn:hover { 
      background: #f3f4f6; 
      color: #111827;
      border-color: #d1d5db;
      transform: translateY(-1px);
    }
    
    .header .btn i { 
      width: 16px; 
      height: 16px; 
    }
    
    /* Admin Menu - Rediseño */
    .admin-menu { position: relative; display: inline-block; margin-left: 0; z-index: 100000; }
    
    .btn-admin { 
      background: linear-gradient(135deg, #1e3a8a 0%, #0b2a6f 100%) !important; /* azul oscuro */
      border: none !important;
      padding: 8px 16px !important;
      border-radius: 10px !important;
      box-shadow: 0 2px 8px rgba(30, 64, 175, 0.35) !important;
      transition: all 0.2s ease !important;
      font-weight: 600 !important;
      color: #ffffff !important;
    }
    
    .btn-admin:hover { 
      background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%) !important; /* variación azul al hover */
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.45) !important;
      transform: translateY(-2px) !important;
    }
    
    .btn-admin i {
      color: #ffffff !important; /* icono blanco */
      animation: rotate 2s linear infinite;
    }
    
    @keyframes rotate {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .btn-admin:hover i {
      animation-duration: 0.6s;
    }
    
    .admin-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: calc(100% + 12px);
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
      z-index: 99999 !important;
      min-width: 220px;
      padding: 8px;
      animation: dropdownSlide 0.2s ease-out;
    }
    
    @keyframes dropdownSlide {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .admin-dropdown.show { display: block; }
    
    .admin-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      text-decoration: none;
      color: #374151;
      font-size: 14px;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.2s ease;
      margin: 2px 0;
      border-bottom: none;
    }
    
    .admin-item:hover {
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      color: #0e7490;
      transform: translateX(4px);
    }
    
    .admin-item i { 
      width: 18px; 
      height: 18px;
      text-align: center;
      color: #0e7490;
    }
    
    .admin-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
      margin: 4px 0;
    }
    
    /* Light theme admin menu overrides */
    body.light-theme .admin-dropdown {
      background: #ffffff;
      border-color: #e5e7eb;
    }
    
    body.light-theme .admin-item {
      color: #374151;
    }
    
    body.light-theme .admin-item:hover {
      background: #f3f4f6;
      color: #374151;
    }
    
    /* Main Layout */
    .main { display: grid; grid-template-columns: 200px 1fr; height: calc(100vh - 48px); }
    
    /* Sidebar */
    .sidebar { background: #252526; border-right: 1px solid #3e3e42; overflow-y: auto; }
    .sidebar-header { padding: 12px; font-weight: 600; font-size: 11px; text-transform: uppercase; color: #cccccc; border-bottom: 1px solid #3e3e42; }
    .tree-item { padding: 4px 12px; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .tree-item:hover { background: #2a2d2e; }
    .tree-item.active { background: #094771; }
    .tree-item .icon { width: 16px; height: 16px; }
    .tree-item i { width: 16px; height: 16px; }
    
    /* Content Area */
    .content { display: flex; flex-direction: column; }
    
    /* Toolbar - Diseño moderno de iconos puros */
    .toolbar { height: 48px; background: #ffffff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; padding: 0 16px; gap: 12px; }
    .toolbar .btn { 
      background: transparent; 
      color: #6b7280; 
      border: none; 
      border-radius: 8px; 
      padding: 8px; 
      font-size: 14px; 
      cursor: pointer; 
      display: flex; 
      align-items: center; 
      justify-content: center;
      gap: 6px; 
      transition: all 0.2s ease;
      min-width: 36px;
      height: 36px;
    }
    .toolbar .btn:hover { 
      background: #f3f4f6; 
      color: #374151; 
      transform: translateY(-1px);
    }
    .toolbar .btn:active { 
      transform: translateY(0);
      background: #e5e7eb;
    }
    .toolbar .btn:disabled { 
      opacity: 0.4; 
      cursor: not-allowed; 
      transform: none;
    }
    .toolbar .btn i { 
      width: 18px; 
      height: 18px; 
      font-size: 16px;
    }
    .toolbar .separator { 
      width: 1px; 
      height: 24px; 
      background: #e5e7eb; 
      margin: 0 8px; 
    }
    
    /* Breadcrumb */
    .breadcrumb { height: 32px; background: #1e1e1e; border-bottom: 1px solid #3e3e42; display: flex; align-items: center; padding: 0 12px; font-size: 13px; }
    .breadcrumb a { color: #007acc; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb .sep { margin: 0 6px; color: #666; }
    
    /* File Explorer */
    .explorer { flex: 1; overflow: auto; padding: 8px; }
    .view-toggle { margin-left: auto; display: flex; gap: 4px; }
    .view-toggle button { 
      background: transparent; 
      color: #6b7280; 
      border: none; 
      border-radius: 8px; 
      padding: 8px 12px; 
      cursor: pointer; 
      display: flex; 
      align-items: center; 
      gap: 6px; 
      transition: all 0.2s ease; 
      min-width: auto; 
      justify-content: center; 
      font-size: 13px; 
      font-weight: 500;
      height: 36px;
    }
    .view-toggle button:hover { 
      background: #f3f4f6; 
      color: #374151; 
      transform: translateY(-1px); 
    }
    .view-toggle button.active { 
      background: #e5e7eb; 
      color: #111827; 
      box-shadow: none;
    }
    .view-toggle button i { 
      width: 16px; 
      height: 16px; 
      font-size: 14px;
    }
    
    /* Grid View */
    .grid-view { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .grid-item { display: flex; flex-direction: column; align-items: center; padding: 16px; border-radius: 8px; cursor: pointer; user-select: none; position: relative; transition: all 0.2s; border: 2px solid transparent; }
    .grid-item:hover { background: #2a2d2e; transform: scale(1.02); }
    .grid-item.selected { 
      background: #0066cc !important; 
      border: 2px solid #0088ff; 
      box-shadow: 0 0 8px rgba(0, 136, 255, 0.4);
    }
    .grid-item.selected:hover { 
      background: #0077dd !important; 
      transform: scale(1.02); 
    }
    .grid-item.selected .icon { filter: brightness(1.2); }
    .grid-item.selected .name { color: #fff !important; }
    .grid-item .icon { width: 96px; height: 96px; display: flex; align-items: center; justify-content: center; font-size: 48px; }
    .grid-item .icon i { width: 64px; height: 64px; }
    .grid-item .name { font-size: 14px; text-align: center; margin-top: 8px; word-break: break-word; }
    .grid-item .label { position: absolute; top: 4px; right: 4px; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }

    /* Folder name - dynamic contrast */
    .name-chip { display: inline-block; }
    .name-chip--sm { font-size: 12px; }
    
    /* Dynamic text colors based on background */
    .name-chip.contrast-dark { color: #000000; }
    .name-chip.contrast-light { color: #ffffff; }
    
    /* Colored Folders */
    .grid-item.folder-colored .icon i { transition: color 0.3s; }
    .grid-item.folder-colored:hover .icon i { filter: brightness(1.2); }
    
    /* Custom Icons */
    .grid-item .custom-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; z-index: 2; }
    .grid-item .icon.has-custom { position: relative; }
    .grid-item .icon.has-custom i:first-child { opacity: 0.3; }
    
    /* Shared files info */
    .shared-info { font-size: 10px; margin-top: 4px; text-align: center; color: #888; }
    .shared-info > div { margin: 1px 0; }
    .shared-info .owner { color: #ccc; }
    .shared-info .permission { color: #4ade80; }
    .shared-info .shared-date { color: #666; }
    
    /* Trash Bin */
    .trash-bin { position: fixed; bottom: 20px; right: 20px; width: 80px; height: 80px; background: #2d2d30; border: 2px solid #3e3e42; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; z-index: 100; }
    .trash-bin:hover { background: #3c3c3c; transform: scale(1.05); }
    .trash-bin.drag-over { background: #e74c3c; border-color: #c0392b; transform: scale(1.1); }
    .trash-bin i { font-size: 32px; color: #666; transition: color 0.3s; }
    .trash-bin:hover i { color: #999; }
    .trash-bin.drag-over i { color: #fff; }
    
    /* List View */
    .list-view { display: none; }
    .list-view.active { display: block; }
    .list-header { display: grid; grid-template-columns: 1fr 80px 120px 100px; gap: 12px; padding: 8px 12px; background: #2d2d30; border-bottom: 1px solid #3e3e42; font-size: 13px; font-weight: 600; }
    .list-item { display: grid; grid-template-columns: 1fr 80px 120px 100px; gap: 12px; padding: 8px 12px; border-bottom: 1px solid #3e3e42; cursor: pointer; font-size: 13px; }
    .list-item:hover { background: #2a2d2e; }
    .list-item.selected { 
      background: #0066cc !important; 
      border-left: 3px solid #0088ff;
      padding-left: 9px;
    }
    .list-item.selected:hover { 
      background: #0077dd !important; 
    }
    .list-item .name { display: flex; align-items: center; gap: 6px; }
    .list-item .name i { width: 16px; height: 16px; }
    .list-item .label { padding: 2px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; color: white; margin-left: 8px; }
    
    /* Column View (macOS style) */
    .column-view { display: none; overflow-x: auto; overflow-y: hidden; height: 100%; }
    .column-view.active { display: block !important; }
    .column-container { display: flex; height: 100%; min-height: 400px; }
    .column { min-width: 220px; width: 220px; border-right: 1px solid #3e3e42; background: #252526; display: flex; flex-direction: column; flex-shrink: 0; transition: background 0.2s; }
    .column:last-child { border-right: none; }
    .column.drag-over { background: rgba(0, 122, 204, 0.1) !important; border: 2px solid #007acc; }
    .column-header { padding: 10px 12px; background: #2d2d30; border-bottom: 1px solid #3e3e42; font-size: 12px; font-weight: 600; color: #cccccc; }
    .column-content { flex: 1; overflow-y: auto; position: relative; }
    .column-content.drag-over { background: rgba(0, 122, 204, 0.05); }
    .column-item { padding: 8px 12px; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(62, 62, 66, 0.2); transition: background 0.2s; }
    .column-item:hover { background: #2a2d2e; }
    .column-item.selected { 
      background: #0066cc !important; 
      color: #fff;
      border-left: 3px solid #0088ff;
      padding-left: 9px;
    }
    .column-item.selected:hover { 
      background: #0077dd !important; 
    }
    .column-item.has-children::after { content: '›'; margin-left: auto; color: #888; font-size: 14px; }
    .column-item .icon { width: 16px; height: 16px; flex-shrink: 0; }
    .column-item .label { padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: 600; color: white; margin-left: auto; }
    
    /* Context Menu */
    .context-menu { position: fixed; background: #2d2d30; border: 1px solid #3e3e42; border-radius: 4px; padding: 4px 0; min-width: 160px; z-index: 1000; display: none; }
    .context-menu-item { padding: 6px 12px; cursor: pointer; font-size: 13px; }
    .context-menu-item:hover { background: #094771; }
    .context-menu-separator { height: 1px; background: #3e3e42; margin: 4px 0; }
    /* Empty area menu adapts to theme */
    body.light-theme #empty-context-menu { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme #empty-context-menu .context-menu-item:hover { background: #e5f2ff; }
    
    /* Modals */
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000 !important; display: none; align-items: center; justify-content: center; }
    .modal { background: #2d2d30; border: 1px solid #3e3e42; border-radius: 4px; padding: 20px; min-width: 300px; }
    .modal h3 { margin: 0 0 16px; font-size: 16px; }
    .modal input { width: 100%; padding: 8px; background: #1e1e1e; border: 1px solid #3e3e42; border-radius: 3px; color: #fff; font-size: 13px; }
    .modal label { display: block; margin: 12px 0 4px; font-size: 13px; color: #cccccc; }
    .modal-buttons { display: flex; gap: 8px; margin-top: 16px; justify-content: flex-end; }
    .modal-buttons button { padding: 6px 12px; border: 0; border-radius: 3px; cursor: pointer; font-size: 13px; }
    .modal-buttons .primary { background: #4a4a4a; color: #ffffff; }
    .modal-buttons .secondary { background: #4a4a4a; color: #ffffff; }
    
    /* Color Picker */
    .color-picker { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin: 8px 0; }
    .color-option { width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.2s; }
    .color-option:hover { transform: scale(1.1); }
    .color-option.selected { border-color: #007acc; transform: scale(1.1); }
    
    /* Sharing Modal */
    .sharing-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 200000 !important; display: none; align-items: center; justify-content: center; }
    .sharing-modal-content { background: #2d2d30; border: 1px solid #3e3e42; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
    .sharing-modal-header { padding: 20px 24px 0; border-bottom: 1px solid #3e3e42; }
    .sharing-modal-header h2 { margin: 0 0 16px; font-size: 20px; color: #fff; }
    .sharing-modal-header .file-info { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .sharing-modal-header .file-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: #007acc; }
    .sharing-modal-header .file-name { font-size: 16px; color: #fff; }
    .sharing-modal-body { padding: 24px; }
    
    /* Sharing Options */
    .sharing-options { display: grid; gap: 16px; }
    .sharing-option { border: 1px solid #3e3e42; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s; }
    .sharing-option:hover { border-color: #007acc; background: rgba(0, 122, 204, 0.1); }
    .sharing-option.selected { border-color: #007acc; background: rgba(0, 122, 204, 0.15); }
    .sharing-option-header { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
    .sharing-option-icon { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: #007acc; }
    .sharing-option-title { font-size: 16px; font-weight: 600; color: #fff; }
    .sharing-option-desc { color: #ccc; font-size: 14px; line-height: 1.4; }
    
    /* Sharing Form */
    .sharing-form { display: none; margin-top: 24px; padding-top: 24px; border-top: 1px solid #3e3e42; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; color: #fff; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; background: #1e1e1e; border: 1px solid #3e3e42; border-radius: 6px; color: #fff; font-size: 14px; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #007acc; }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-group-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    
    /* User Input */
    .user-input-container { position: relative; }
    .user-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
    .user-tag { background: #007acc; color: #fff; padding: 4px 8px; border-radius: 16px; font-size: 12px; display: flex; align-items: center; gap: 4px; }
    .user-tag .remove { cursor: pointer; font-weight: bold; }
    .user-tag .remove:hover { background: rgba(255,255,255,0.2); border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; }
    
    /* Permission Badges */
    .permission-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .permission-propietario { background: #dc3545; color: white; }
    .permission-editor { background: #007bff; color: white; }
    .permission-comentarista { background: #ffc107; color: #212529; }
    .permission-lector { background: #6c757d; color: white; }
    
    /* Advanced Options */
    .advanced-options { margin-top: 16px; }
    .advanced-toggle { cursor: pointer; color: #007acc; font-size: 14px; display: flex; align-items: center; gap: 8px; }
    .advanced-toggle:hover { text-decoration: underline; }
    .advanced-content { display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid #3e3e42; }
    .checkbox-group { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
    .checkbox-group input[type="checkbox"] { margin: 0; }
    
    /* Modal Footer */
    .sharing-modal-footer { padding: 0 24px 24px; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-cancel { background: #4a4a4a; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .btn-cancel:hover { background: #5a5a5a; }
    .btn-share { background: #4a4a4a; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; }
    .btn-share:hover { background: #5a5a5a; }
    .btn-share:disabled { background: #6c757d; cursor: not-allowed; }
    
    /* Light theme overrides */
    body.light-theme .sharing-modal-content { background: #fff; border-color: #e5e7eb; }
    body.light-theme .sharing-modal-header { border-bottom-color: #e5e7eb; }
    body.light-theme .sharing-modal-header h2 { color: #111827; }
    body.light-theme .sharing-modal-header .file-name { color: #111827; }
    body.light-theme .sharing-option { border-color: #e5e7eb; }
    body.light-theme .sharing-option:hover { border-color: #007acc; background: rgba(0, 122, 204, 0.05); }
    body.light-theme .sharing-option.selected { background: rgba(0, 122, 204, 0.1); }
    body.light-theme .sharing-option-title { color: #111827; }
    body.light-theme .sharing-option-desc { color: #6b7280; }
    body.light-theme .sharing-form { border-top-color: #e5e7eb; }
    body.light-theme .form-group label { color: #111827; }
    body.light-theme .form-group input, body.light-theme .form-group select, body.light-theme .form-group textarea { background: #fff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .advanced-content { border-top-color: #e5e7eb; }
    
    /* Link Modal */
    .link-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 300000 !important; display: none; align-items: center; justify-content: center; }
    .link-modal { background: #2d2d30; border: 1px solid #3e3e42; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .link-modal-header { padding: 20px 24px; border-bottom: 1px solid #3e3e42; display: flex; justify-content: space-between; align-items: center; }
    .link-modal-header h3 { margin: 0; font-size: 18px; color: #fff; display: flex; align-items: center; gap: 8px; }
    .link-modal-close { background: none; border: none; color: #888; font-size: 18px; cursor: pointer; padding: 4px; border-radius: 4px; transition: all 0.2s; }
    .link-modal-close:hover { background: #3e3e42; color: #fff; }
    .link-modal-body { padding: 24px; }
    
    .link-info { margin-bottom: 24px; }
    .link-item-info { display: flex; align-items: center; gap: 12px; }
    .link-item-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(0, 122, 204, 0.1); border-radius: 8px; color: #007acc; }
    .link-item-icon i { font-size: 18px; }
    .link-item-details { flex: 1; }
    .link-item-name { font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px; }
    .link-item-type { font-size: 13px; color: #888; }
    
    .link-section, .access-code-section { margin-bottom: 20px; }
    .link-section label, .access-code-section label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #fff; }
    .link-container, .code-container { display: flex; gap: 8px; }
    .link-container input, .code-container input { flex: 1; padding: 12px; background: #1e1e1e; border: 1px solid #3e3e42; border-radius: 6px; color: #fff; font-size: 14px; font-family: 'Courier New', monospace; }
    .copy-btn { padding: 12px 16px; background: #007acc; border: none; border-radius: 6px; color: #fff; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .copy-btn:hover { background: #0066aa; transform: translateY(-1px); }
    .copy-btn i { font-size: 14px; }
    
    .access-code-note { margin-top: 8px; display: flex; align-items: center; gap: 8px; padding: 8px 12px; background: rgba(255, 193, 7, 0.1); border-radius: 6px; border-left: 3px solid #ffc107; }
    .access-code-note i { color: #ffc107; }
    .access-code-note span { color: #fff; font-size: 13px; }
    
    .link-permissions { margin-top: 20px; padding-top: 20px; border-top: 1px solid #3e3e42; }
    .permission-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .permission-item i { color: #007acc; width: 16px; }
    .permission-item span { color: #ccc; font-size: 14px; }
    .permission-item strong { color: #fff; }
    
    .link-modal-footer { padding: 20px 24px; border-top: 1px solid #3e3e42; display: flex; justify-content: flex-end; gap: 12px; }
    .btn-secondary { background: #4a4a4a; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; transition: all 0.2s; }
    .btn-secondary:hover { background: #5a5a5a; }
    .btn-primary { background: #007acc; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-primary:hover { background: #0066aa; transform: translateY(-1px); }
    
    /* Light theme overrides for Link Modal */
    body.light-theme .link-modal { background: #fff; border-color: #e5e7eb; }
    body.light-theme .link-modal-header { border-bottom-color: #e5e7eb; }
    body.light-theme .link-modal-header h3 { color: #111827; }
    body.light-theme .link-modal-close { color: #6b7280; }
    body.light-theme .link-modal-close:hover { background: #f3f4f6; color: #111827; }
    body.light-theme .link-item-name { color: #111827; }
    body.light-theme .link-item-type { color: #6b7280; }
    body.light-theme .link-section label, body.light-theme .access-code-section label { color: #111827; }
    body.light-theme .link-container input, body.light-theme .code-container input { background: #fff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .access-code-note { background: rgba(245, 158, 11, 0.1); border-left-color: #f59e0b; }
    body.light-theme .access-code-note span { color: #111827; }
    body.light-theme .link-permissions { border-top-color: #e5e7eb; }
    body.light-theme .permission-item span { color: #6b7280; }
    body.light-theme .permission-item strong { color: #111827; }
    body.light-theme .link-modal-footer { border-top-color: #e5e7eb; }
    
    /* System Modal */
    .system-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 999998 !important; display: none; align-items: center; justify-content: center; }
    .system-modal { background: #2d2d30; border: 1px solid #3e3e42; border-radius: 12px; width: 90%; max-width: 480px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden; position: relative; z-index: 999999 !important; }
    .system-modal-header { padding: 16px 20px; border-bottom: 1px solid #3e3e42; display: flex; align-items: center; gap: 10px; }
    .system-modal-header .title { font-size: 16px; color: #fff; font-weight: 600; }
    .system-modal-header .icon { width: 20px; text-align: center; }
    .system-modal-body { padding: 20px; color: #ddd; font-size: 14px; white-space: pre-wrap; }
    .system-modal-footer { padding: 12px 20px; border-top: 1px solid #3e3e42; display: flex; justify-content: flex-end; gap: 8px; }
    .system-btn { background: #4a4a4a; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .system-btn.primary { background: #007acc; }
    .system-modal.success .system-modal-header { border-bottom-color: #1f6feb; }
    .system-modal.error .system-modal-header { border-bottom-color: #d9534f; }
    .system-modal.info .system-modal-header { border-bottom-color: #6c757d; }
    body.light-theme .system-modal { background: #fff; border-color: #e5e7eb; }
    body.light-theme .system-modal-header { border-bottom-color: #e5e7eb; }
    body.light-theme .system-modal-body { color: #111827; }

    /* Delete Confirmation Modal */
    .delete-modal-overlay { 
      position: fixed; 
      inset: 0; 
      background: rgba(0,0,0,0.7); 
      z-index: 999999 !important; 
      display: none; 
      align-items: center; 
      justify-content: center;
      backdrop-filter: blur(8px);
    }
    .delete-modal { 
      background: linear-gradient(135deg, #1e1e1e 0%, #2d2d30 100%);
      border: 1px solid #3e3e42; 
      border-radius: 16px; 
      width: 90%; 
      max-width: 420px; 
      box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
      overflow: hidden; 
      position: relative; 
      z-index: 1000000 !important;
      animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalSlideIn {
      from { opacity: 0; transform: scale(0.9) translateY(-20px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .delete-modal-header { 
      padding: 24px 24px 16px; 
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .delete-icon {
      width: 64px;
      height: 64px;
      margin: 0 auto 16px;
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    .delete-icon i {
      font-size: 24px;
      color: #fff;
    }
    .delete-modal-header h3 {
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      color: #fff;
      text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    .delete-modal-body { 
      padding: 20px 24px; 
      text-align: center;
    }
    .delete-modal-body p {
      margin: 0 0 16px;
      color: #e5e7eb;
      font-size: 16px;
      line-height: 1.5;
    }
    .delete-warning {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 16px;
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 8px;
      color: #fca5a5;
      font-size: 14px;
    }
    .delete-warning i {
      color: #ef4444;
    }
    .delete-modal-footer { 
      padding: 16px 24px 24px; 
      display: flex; 
      gap: 12px;
      justify-content: center;
    }
    .btn-cancel, .btn-delete {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      min-width: 120px;
      justify-content: center;
    }
    .btn-cancel {
      background: #374151;
      color: #d1d5db;
      border: 1px solid #4b5563;
    }
    .btn-cancel:hover {
      background: #4b5563;
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .btn-delete {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #fff;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-delete i {
      color: #fff !important;
    }
    .btn-delete:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }
    .btn-delete:active {
      transform: translateY(0);
    }
    
    /* Light theme overrides */
    body.light-theme .delete-modal {
      background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
      border-color: #e5e7eb;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.05);
    }
    body.light-theme .delete-modal-header {
      border-bottom-color: #e5e7eb;
    }
    body.light-theme .delete-modal-header h3 {
      color: #111827;
    }
    body.light-theme .delete-modal-body p {
      color: #374151;
    }
    body.light-theme .delete-warning {
      background: rgba(239, 68, 68, 0.05);
      border-color: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }
    body.light-theme .btn-cancel {
      background: #f3f4f6;
      color: #374151;
      border-color: #d1d5db;
    }
    body.light-theme .btn-cancel:hover {
      background: #e5e7eb;
      color: #111827;
    }

    /* Icon Picker */
    .icon-picker { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 8px 0; }
    .icon-option { width: 40px; height: 40px; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s; display: flex; align-items: center; justify-content: center; background: #1e1e1e; }
    .icon-option:hover { transform: scale(1.1); background: #2a2d2e; }
    .icon-option.selected { border-color: #007acc; transform: scale(1.1); background: #094771; }
    .icon-option i { font-size: 18px; color: #cccccc; }
    
    /* Drag & Drop */
    .grid-item, .list-item, .column-item { transition: all 0.2s; }
    .grid-item.dragging, .list-item.dragging, .column-item.dragging { opacity: 0.5; transform: rotate(2deg); }
    .grid-item.drag-over, .list-item.drag-over, .column-item.drag-over { background: rgba(0, 122, 204, 0.2) !important; border: 2px dashed #007acc; }
    .grid-item.drag-target, .list-item.drag-target, .column-item.drag-target { background: rgba(39, 208, 125, 0.2) !important; }
    
    /* Move Confirmation Modal */
    .move-modal { max-width: 400px; }
    .move-info { background: #1e1e1e; padding: 16px; border-radius: 6px; margin: 16px 0; }
    .move-info .item { display: flex; align-items: center; gap: 8px; padding: 8px 0; }
    .move-info .arrow { text-align: center; color: #007acc; font-size: 20px; margin: 8px 0; }
    
    /* Upload */
    .upload-area { border: 2px dashed #3e3e42; border-radius: 4px; padding: 40px; text-align: center; margin: 20px; color: #666; display: flex; flex-direction: column; align-items: center; }
    .upload-area.drag-over { border-color: #007acc; background: rgba(0, 122, 204, 0.1); color: #007acc; }
    .upload-progress { height: 4px; background: #3e3e42; border-radius: 2px; margin: 8px 0; overflow: hidden; }
    .upload-progress-bar { height: 100%; background: #007acc; width: 0%; transition: width 0.3s; }
    
    /* Upload Progress Cards - Indicadores individuales por archivo */
    .upload-progress-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 999999 !important;
      max-width: 350px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      pointer-events: none; /* No bloquear clicks en el fondo */
    }
    
    .upload-progress-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideInRight 0.3s ease-out;
      min-width: 300px;
      pointer-events: auto; /* Habilitar clicks en las tarjetas */
      position: relative;
      z-index: 1000000 !important;
    }
    
    .upload-progress-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      background: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6b7280;
      font-size: 18px;
      flex-shrink: 0;
    }
    
    .upload-progress-content {
      flex: 1;
      min-width: 0;
    }
    
    .upload-progress-filename {
      font-size: 14px;
      font-weight: 600;
      color: #111827;
      margin-bottom: 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .upload-progress-status {
      font-size: 12px;
      color: #6b7280;
      margin-bottom: 8px;
    }
    
    .upload-progress-bar-container {
      width: 100%;
      height: 6px;
      background: #f3f4f6;
      border-radius: 3px;
      overflow: hidden;
    }
    
    .upload-progress-bar-individual {
      height: 100%;
      background: linear-gradient(90deg, #3b82f6, #1d4ed8);
      border-radius: 3px;
      transition: width 0.3s ease;
      position: relative;
    }
    
    .upload-progress-bar-individual::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      animation: shimmer 1.5s infinite;
    }
    
    .upload-progress-percentage {
      font-size: 11px;
      color: #6b7280;
      margin-top: 4px;
      text-align: right;
    }
    
    .upload-progress-close {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #f3f4f6;
      border: none;
      color: #6b7280;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      transition: all 0.2s ease;
      flex-shrink: 0;
    }
    
    .upload-progress-close:hover {
      background: #e5e7eb;
      color: #374151;
    }
    
    /* Estados de upload */
    .upload-progress-card.uploading .upload-progress-icon {
      background: #dbeafe;
      color: #3b82f6;
    }
    
    .upload-progress-card.success .upload-progress-icon {
      background: #dcfce7;
      color: #16a34a;
    }
    
    .upload-progress-card.error .upload-progress-icon {
      background: #fef2f2;
      color: #dc2626;
    }
    
    /* Animaciones */
    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    
    .upload-progress-card.removing {
      animation: slideOutRight 0.3s ease-in forwards;
    }
    
    @keyframes slideOutRight {
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }
    
    input[type="file"] { display: none; }
    .visually-hidden-input { position: fixed; left: -9999px; top: -9999px; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
    .temp-color-picker { position: fixed; z-index: 9999; width: 50px; height: 30px; border: none; padding: 0; cursor: pointer; }
    
    /* Selection Box */
    .selection-box { 
      position: absolute; 
      border: 1px solid #007acc; 
      background: rgba(0, 122, 204, 0.15); 
      pointer-events: none; 
      z-index: 1000; 
      display: none;
      box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    }
    
    /* Prevent text selection during drag */
    body.selecting {
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      cursor: crosshair;
    }
    
    /* File Type Colors */
    .text-blue-600 { color: #2563eb !important; }
    .text-green-600 { color: #059669 !important; }
    .text-orange-500 { color: #f97316 !important; }
    .text-red-500 { color: #ef4444 !important; }
    .text-purple-500 { color: #a855f7 !important; }
    .text-blue-500 { color: #3b82f6 !important; }
    .text-yellow-500 { color: #eab308 !important; }
    .text-indigo-500 { color: #6366f1 !important; }
    .text-green-500 { color: #22c55e !important; }
    .text-gray-500 { color: #6b7280 !important; }
    .text-pink-500 { color: #ec4899 !important; }
    .text-purple-400 { color: #c084fc !important; }
    .text-red-400 { color: #f87171 !important; }
    .text-blue-400 { color: #60a5fa !important; }
    .text-gray-400 { color: #9ca3af !important; }
    .text-blue-300 { color: #93c5fd !important; }
    .text-orange-400 { color: #fb923c !important; }
    .text-green-400 { color: #4ade80 !important; }
    .text-yellow-400 { color: #facc15 !important; }
    .text-yellow-600 { color: #ca8a04 !important; }
    .text-purple-600 { color: #9333ea !important; }
    .text-gray-600 { color: #4b5563 !important; }
    .text-brown-600 { color: #92400e !important; }
  </style>
</head>
<body class="light-theme">
  <!-- Header -->
  <div class="header">
    <div class="logo" onclick="navigateToRoot()" style="cursor: pointer;">
      <i class="fas fa-hdd"></i>
      <span>DRIVE</span>
    </div>

    <div class="user">
      <i class="fas fa-user"></i>
      <span><?= htmlspecialchars($userName) ?></span>
      <?php if ($userRole === 'administrador'): ?>
        <div class="admin-menu">
          <button class="btn btn-admin" onclick="toggleAdminMenu()" title="Administración">
            <i class="fas fa-cog"></i>
          </button>
          <div class="admin-dropdown" id="adminDropdown">
            <a href="/biblioteca/public/index.php/admin/users" class="admin-item">
              <i class="fas fa-users"></i>
              <span>Gestión de Usuarios</span>
            </a>
            <div class="admin-divider"></div>
            <a href="#" class="admin-item" onclick="showSystemInfo()">
              <i class="fas fa-info-circle"></i>
              <span>Información del Sistema</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <button class="btn" onclick="logout()">
      <i class="fas fa-sign-out-alt"></i>
      <span>Salir</span>
    </button>
  </div>

  <!-- Main Layout -->
  <div class="main">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Storage Quota Indicator -->
      <div class="storage-quota-sidebar" id="storage-quota" title="Uso de almacenamiento">
        <div class="quota-header-sidebar">
          <i class="fas fa-chart-pie"></i>
          <span class="quota-label">Almacenamiento</span>
        </div>
        <div class="quota-bar-sidebar">
          <div class="quota-fill-sidebar" id="quota-fill"></div>
        </div>
        <div class="quota-text-sidebar" id="quota-text">Cargando...</div>
      </div>
      
      <div class="sidebar-divider"></div>
      
      <div class="sidebar-header">Explorador</div>
      <div class="tree-item active" data-folder="0" onclick="navigateToRoot()">
        <i class="fas fa-hdd"></i>
        <span>DRIVE</span>
      </div>
      <div class="tree-item" onclick="loadSharedWithMe()" id="shared-with-me-item">
        <i class="fas fa-users"></i>
        <span>Compartido conmigo</span>
      </div>
      <div id="folder-tree"></div>
    </div>

    <!-- Content -->
    <div class="content">
      <!-- Toolbar -->
      <div class="toolbar">
        <button class="btn" onclick="createFolder()">
          <i class="fas fa-folder-plus"></i>
          <span>Nueva Carpeta</span>
        </button>
        <button class="btn" onclick="document.getElementById('file-input').click()">
          <i class="fas fa-upload"></i>
          <span>Subir Archivo</span>
        </button>
        <div class="separator"></div>
        <button class="btn" onclick="renameSelected()" id="btn-rename" disabled>
          <i class="fas fa-edit"></i>
          <span>Renombrar</span>
        </button>
        <button class="btn" onclick="deleteSelected()" id="btn-delete" disabled>
          <i class="fas fa-trash"></i>
          <span>Eliminar</span>
        </button>
        <div class="selection-info" id="selection-info" style="margin-left: auto; padding: 0 16px; font-size: 13px; color: #0099ff; display: none; align-items: center;">
          <i class="fas fa-check-square" style="margin-right: 6px;"></i>
          <span id="selection-count">0</span> elementos seleccionados
        </div>
        <div class="view-toggle">
          <button onclick="setView('grid')" class="active" id="view-grid">
            <i class="fas fa-th"></i>
            <span>Cuadrícula</span>
          </button>
          <button onclick="setView('list')" id="view-list">
            <i class="fas fa-list"></i>
            <span>Lista</span>
          </button>
          <button onclick="setView('columns')" id="view-columns">
            <i class="fas fa-columns"></i>
            <span>Columnas</span>
          </button>
        </div>
      </div>

      <!-- Breadcrumb -->
      <div class="breadcrumb" id="breadcrumb">
        <a href="#" onclick="navigateToFolder(0)">DRIVE</a>
      </div>

      <!-- File Explorer -->
      <div class="explorer" id="explorer">
        <!-- Selection Box -->
        <div class="selection-box" id="selection-box"></div>
        <div class="grid-view" id="grid-view"></div>
        <div class="list-view" id="list-view">
          <div class="list-header">
            <div>Nombre</div>
            <div>Tipo</div>
            <div>Fecha</div>
            <div>Tamaño</div>
          </div>
          <div id="list-content"></div>
        </div>
        <div class="column-view" id="column-view">
          <div class="column-container" id="column-container">
            <!-- Columns will be dynamically added here -->
          </div>
        </div>
        
        <!-- Upload Area -->
        <div class="upload-area" id="upload-area">
          <i class="fas fa-cloud-upload-alt" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
          <div>Arrastra archivos aquí o haz clic en "Subir Archivo"</div>
          <div class="upload-progress" id="upload-progress" style="display:none">
            <div class="upload-progress-bar" id="upload-progress-bar"></div>
          </div>
        </div>
      </div>
      
      <!-- Hidden background image input -->
      <input type="file" id="bg-input" accept="image/*" class="visually-hidden-input" />
      <!-- Hidden background color input -->
      <input type="color" id="bg-color-input" class="visually-hidden-input" value="#ffffff" />
    </div>
  </div>

  <!-- Upload Progress Container -->
  <div class="upload-progress-container" id="upload-progress-container"></div>

  <!-- Trash Bin (visible in all views) -->
  <div class="trash-bin" id="trash-bin" style="display: flex;" title="Arrastra archivos aquí para eliminar">
    <i class="fas fa-trash-alt"></i>
    <div class="trash-tooltip">Arrastra aquí para eliminar</div>
  </div>

  <!-- Context Menu -->
  <div class="context-menu" id="context-menu">
    <div class="context-menu-item" onclick="openSelected()">
      <i class="fas fa-folder-open" style="width: 16px; margin-right: 8px;"></i>
      Abrir
    </div>
    <div class="context-menu-item" onclick="renameSelected()">
      <i class="fas fa-edit" style="width: 16px; margin-right: 8px;"></i>
      Renombrar
    </div>
    <div class="context-menu-item" onclick="setLabelSelected()" id="label-menu-item">
      <i class="fas fa-tag" style="width: 16px; margin-right: 8px;"></i>
      Etiqueta
    </div>
    <div class="context-menu-item" onclick="setIconSelected()" id="icon-menu-item">
      <i class="fas fa-icons" style="width: 16px; margin-right: 8px;"></i>
      Icono
    </div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="shareSelected()">
      <i class="fas fa-share-alt" style="width: 16px; margin-right: 8px;"></i>
      Compartir
    </div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="deleteSelected()">
      <i class="fas fa-trash" style="width: 16px; margin-right: 8px;"></i>
      Eliminar
    </div>
  </div>

  <!-- Empty Area Context Menu -->
  <div class="context-menu" id="empty-context-menu">
    <div class="context-menu-item" onclick="createFolder()">
      <i class="fas fa-folder-plus" style="width: 16px; margin-right: 8px;"></i>
      Nueva carpeta
    </div>
    <div class="context-menu-item" onclick="openBackgroundPicker()">
      <i class="fas fa-image" style="width: 16px; margin-right: 8px;"></i>
      Cambiar fondo
    </div>
    <div class="context-menu-item" onclick="openBackgroundSolidPicker()">
      <i class="fas fa-palette" style="width: 16px; margin-right: 8px;"></i>
      Fondo sólido
    </div>
    <div class="context-menu-item" onclick="clearExplorerBackground()">
      <i class="fas fa-ban" style="width: 16px; margin-right: 8px;"></i>
      Quitar fondo
    </div>
  </div>

  <!-- Modals -->
  <div class="modal-overlay" id="modal-overlay">
    <div class="modal" id="modal">
      <h3 id="modal-title">Título</h3>
      <input type="text" id="modal-input" placeholder="Nombre">
      
      <!-- Label Modal Content -->
      <div id="label-modal-content" style="display: none;">
        <label for="label-input">Etiqueta</label>
        <input type="text" id="label-input" placeholder="Nombre de la etiqueta (opcional)">
        
        <label>Color</label>
        <div class="color-picker" id="color-picker">
          <div class="color-option" data-color="#e74c3c" style="background: #e74c3c;" title="Rojo"></div>
          <div class="color-option" data-color="#f39c12" style="background: #f39c12;" title="Naranja"></div>
          <div class="color-option" data-color="#f1c40f" style="background: #f1c40f;" title="Amarillo"></div>
          <div class="color-option" data-color="#27ae60" style="background: #27ae60;" title="Verde"></div>
          <div class="color-option" data-color="#3498db" style="background: #3498db;" title="Azul"></div>
          <div class="color-option" data-color="#9b59b6" style="background: #9b59b6;" title="Morado"></div>
          <div class="color-option" data-color="#95a5a6" style="background: #95a5a6;" title="Gris"></div>
          <div class="color-option" data-color="#34495e" style="background: #34495e;" title="Azul Oscuro"></div>
          <div class="color-option" data-color="" style="background: transparent; border: 2px dashed #666;" title="Sin color">×</div>
        </div>
      </div>
      
      <!-- Icon Modal Content -->
      <div id="icon-modal-content" style="display: none;">
        <label>Selecciona un icono</label>
        <div class="icon-picker" id="icon-picker">
          <div class="icon-option" data-icon="fas fa-folder" title="Carpeta"><i class="fas fa-folder"></i></div>
          <div class="icon-option" data-icon="fas fa-briefcase" title="Maletín"><i class="fas fa-briefcase"></i></div>
          <div class="icon-option" data-icon="fas fa-home" title="Casa"><i class="fas fa-home"></i></div>
          <div class="icon-option" data-icon="fas fa-cog" title="Configuración"><i class="fas fa-cog"></i></div>
          <div class="icon-option" data-icon="fas fa-star" title="Estrella"><i class="fas fa-star"></i></div>
          <div class="icon-option" data-icon="fas fa-heart" title="Corazón"><i class="fas fa-heart"></i></div>
          <div class="icon-option" data-icon="fas fa-shield-alt" title="Escudo"><i class="fas fa-shield-alt"></i></div>
          <div class="icon-option" data-icon="fas fa-rocket" title="Cohete"><i class="fas fa-rocket"></i></div>
          <div class="icon-option" data-icon="fas fa-crown" title="Corona"><i class="fas fa-crown"></i></div>
          <div class="icon-option" data-icon="fas fa-gem" title="Gema"><i class="fas fa-gem"></i></div>
          <div class="icon-option" data-icon="fas fa-fire" title="Fuego"><i class="fas fa-fire"></i></div>
          <div class="icon-option" data-icon="fas fa-bolt" title="Rayo"><i class="fas fa-bolt"></i></div>
          <div class="icon-option" data-icon="fas fa-leaf" title="Hoja"><i class="fas fa-leaf"></i></div>
          <div class="icon-option" data-icon="fas fa-globe" title="Globo"><i class="fas fa-globe"></i></div>
          <div class="icon-option" data-icon="fas fa-camera" title="Cámara"><i class="fas fa-camera"></i></div>
          <div class="icon-option" data-icon="" title="Sin icono personalizado" style="background: #e74c3c; color: white;">×</div>
        </div>
      </div>
      
      <div class="modal-buttons">
        <button class="secondary" onclick="closeModal()">Cancelar</button>
        <button class="primary" onclick="confirmModal()">Aceptar</button>
      </div>
    </div>
  </div>

  <!-- Move Confirmation Modal -->
  <div class="modal-overlay" id="move-modal-overlay">
    <div class="modal move-modal" id="move-modal">
      <h3>Confirmar Movimiento</h3>
      <p>¿Estás seguro de que quieres mover este elemento?</p>
      
      <div class="move-info" id="move-info">
        <div class="item" id="move-source">
          <i class="fas fa-folder"></i>
          <span>Origen</span>
        </div>
        <div class="arrow">↓</div>
        <div class="item" id="move-target">
          <i class="fas fa-folder"></i>
          <span>Destino</span>
        </div>
      </div>
      
      <div class="modal-buttons">
        <button class="secondary" onclick="closeMoveModal()">Cancelar</button>
        <button class="primary" onclick="confirmMove()">Mover</button>
      </div>
    </div>
  </div>

  <!-- Hidden File Input -->
  <input type="file" id="file-input" multiple>

  <!-- Preview Panel -->
  <div class="preview-panel" id="preview-panel" onclick="openPreviewModal()">
    <div class="preview-header">
      <div class="preview-title" id="preview-title">Vista Previa</div>
      <div class="preview-expand-hint">
        <i class="fas fa-expand-alt"></i>
        Clic para expandir
      </div>
      <button class="preview-close" onclick="event.stopPropagation(); closePreview()">&times;</button>
    </div>
    <div class="preview-content">
      <div class="preview-viewer" id="preview-viewer">
        <div class="file-icon">
          <i class="fas fa-file"></i>
        </div>
      </div>
      <div class="preview-metadata" id="preview-metadata">
        <!-- Metadata will be populated here -->
      </div>
    </div>
  </div>

  <!-- Sharing Modal -->
  <div class="sharing-modal" id="sharing-modal">
    <div class="sharing-modal-content">
      <div class="sharing-modal-header">
        <h2>Compartir</h2>
        <div class="file-info">
          <div class="file-icon" id="share-file-icon">
            <i class="fas fa-file"></i>
          </div>
          <div class="file-name" id="share-file-name">Documento</div>
        </div>
      </div>
      
      <div class="sharing-modal-body">
        <div class="sharing-options">
          <!-- Compartir con usuarios específicos -->
          <div class="sharing-option" data-option="users" onclick="selectSharingOption('users')">
            <div class="sharing-option-header">
              <div class="sharing-option-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="sharing-option-title">Compartir con usuarios específicos</div>
            </div>
            <div class="sharing-option-desc">
              Invita a personas específicas por email y asigna permisos individuales
            </div>
          </div>
          
          <!-- Compartir con grupo -->
          <div class="sharing-option" data-option="group" onclick="selectSharingOption('group')">
            <div class="sharing-option-header">
              <div class="sharing-option-icon">
                <i class="fas fa-user-friends"></i>
              </div>
              <div class="sharing-option-title">Compartir con un grupo</div>
            </div>
            <div class="sharing-option-desc">
              Todos los miembros del grupo recibirán el mismo nivel de acceso
            </div>
          </div>
          
          <!-- Crear enlace público -->
          <div class="sharing-option" data-option="link" onclick="selectSharingOption('link')">
            <div class="sharing-option-header">
              <div class="sharing-option-icon">
                <i class="fas fa-link"></i>
              </div>
              <div class="sharing-option-title">Crear enlace para compartir</div>
            </div>
            <div class="sharing-option-desc">
              Cualquier persona con el enlace puede acceder según los permisos configurados
            </div>
          </div>
        </div>
        
        <!-- Formulario para usuarios específicos -->
        <div class="sharing-form" id="users-form">
          <div class="form-group">
            <label for="user-emails">Agregar personas</label>
            <div class="user-input-container">
              <div class="user-tags" id="user-tags"></div>
              <input type="email" id="user-emails" placeholder="Ingresa emails separados por coma" 
                     onkeydown="handleUserEmailInput(event)" onblur="processUserEmails()">
            </div>
          </div>
          
          <div class="form-group-row">
            <div class="form-group">
              <label for="user-permission">Nivel de acceso</label>
              <select id="user-permission">
                <option value="lector">👁️ Lector - Solo ver</option>
                <option value="comentarista">💬 Comentarista - Ver y comentar</option>
                <option value="editor">✏️ Editor - Ver, comentar y editar</option>
                <option value="propietario">👑 Propietario - Control total</option>
              </select>
            </div>
            <div class="form-group">
              <label for="user-expiry">Fecha de caducidad</label>
              <input type="date" id="user-expiry">
            </div>
          </div>
          
          <div class="form-group">
            <label for="user-message">Mensaje (opcional)</label>
            <textarea id="user-message" placeholder="Escribe un mensaje para acompañar la invitación"></textarea>
          </div>
        </div>
        
        <!-- Formulario para grupos -->
        <div class="sharing-form" id="group-form">
          <div class="form-group-row">
            <div class="form-group">
              <label for="group-select">Seleccionar grupo</label>
              <select id="group-select">
                <option value="">Selecciona un grupo...</option>
                <!-- Los grupos se cargarán dinámicamente -->
              </select>
            </div>
            <div class="form-group">
              <label for="group-permission">Nivel de acceso</label>
              <select id="group-permission">
                <option value="lector">👁️ Lector - Solo ver</option>
                <option value="comentarista">💬 Comentarista - Ver y comentar</option>
                <option value="editor">✏️ Editor - Ver, comentar y editar</option>
                <option value="propietario">👑 Propietario - Control total</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label for="group-expiry">Fecha de caducidad</label>
            <input type="date" id="group-expiry">
          </div>
        </div>
        
        <!-- Formulario para enlace público -->
        <div class="sharing-form" id="link-form">
          <div class="form-group-row">
            <div class="form-group">
              <label for="link-permission">Nivel de acceso</label>
              <select id="link-permission">
                <option value="lector">👁️ Lector - Solo ver</option>
                <option value="comentarista">💬 Comentarista - Ver y comentar</option>
                <option value="editor">✏️ Editor - Ver, comentar y editar</option>
              </select>
            </div>
            <div class="form-group">
              <label for="link-expiry">Fecha de caducidad</label>
              <input type="date" id="link-expiry">
            </div>
          </div>
          
          <div class="form-group">
            <label for="link-password">Contraseña (opcional)</label>
            <input type="password" id="link-password" placeholder="Proteger con contraseña">
          </div>
          
          <div class="form-group">
            <div class="checkbox-group">
              <input type="checkbox" id="use-access-code">
              <label for="use-access-code">🔐 Generar código de acceso de 6 dígitos</label>
            </div>
            <small style="color: #888; margin-top: 4px; display: block;">
              El código se generará automáticamente y será más fácil de compartir que una contraseña
            </small>
          </div>
          
          <div class="form-group">
            <label for="link-domains">Dominios permitidos (opcional)</label>
            <input type="text" id="link-domains" placeholder="ejemplo.com, empresa.org">
          </div>
        </div>
        
        <!-- Opciones avanzadas -->
        <div class="advanced-options">
          <div class="advanced-toggle" onclick="toggleAdvancedOptions()">
            <i class="fas fa-chevron-right" id="advanced-chevron"></i>
            <span>Opciones avanzadas</span>
          </div>
          
          <div class="advanced-content" id="advanced-content">
            <div class="checkbox-group">
              <input type="checkbox" id="can-download" checked>
              <label for="can-download">Permitir descarga</label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="can-print" checked>
              <label for="can-print">Permitir impresión</label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="can-copy" checked>
              <label for="can-copy">Permitir copiar</label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="notify-changes">
              <label for="notify-changes">Notificar cambios</label>
            </div>
            <div class="checkbox-group" id="notify-access-group" style="display: none;">
              <input type="checkbox" id="notify-access">
              <label for="notify-access">Notificar accesos</label>
            </div>
            <div class="checkbox-group" id="require-auth-group" style="display: none;">
              <input type="checkbox" id="require-auth">
              <label for="require-auth">Requerir autenticación</label>
            </div>
          </div>
        </div>
      </div>
      
      <div class="sharing-modal-footer">
        <button class="btn-cancel" onclick="closeSharingModal()">Cancelar</button>
        <button class="btn-share" id="share-btn" onclick="executeSharing()" disabled>Compartir</button>
      </div>
    </div>
  </div>

  <!-- Modal de Enlace Compartido -->
  <div class="link-modal-overlay" id="link-modal-overlay">
    <div class="link-modal">
      <div class="link-modal-header">
        <h3 style="color: #2d3748 !important;"><i class="fas fa-link" style="color: #4a90e2;"></i> Enlace para Compartir</h3>
        <button class="link-modal-close" onclick="closeLinkModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="link-modal-body">
        <div class="link-info">
          <div class="link-item-info">
            <div class="link-item-icon" id="link-item-icon">
              <i class="fas fa-file"></i>
            </div>
            <div class="link-item-details">
              <div class="link-item-name" id="link-item-name" style="color: #2d3748 !important;">Nombre del archivo</div>
              <div class="link-item-type" id="link-item-type" style="color: #6b7280 !important;">Archivo</div>
            </div>
          </div>
        </div>
        
        <div class="link-section">
          <label style="color: #2d3748 !important; font-weight: 600;">Enlace generado:</label>
          <div class="link-container">
            <input type="text" id="generated-link" readonly style="color: #2d3748 !important; background: white !important; -webkit-text-fill-color: #2d3748 !important;">
            <button class="copy-btn" onclick="copyLinkToClipboard()" title="Copiar enlace">
              <i class="fas fa-copy"></i>
            </button>
          </div>
        </div>
        
        <div class="access-code-section" id="access-code-section" style="display: none;">
          <label style="color: #2d3748 !important; font-weight: 600;">Código de acceso:</label>
          <div class="code-container">
            <input type="text" id="access-code" readonly style="color: #2d3748 !important; background: white !important; -webkit-text-fill-color: #2d3748 !important;">
            <button class="copy-btn" onclick="copyCodeToClipboard()" title="Copiar código">
              <i class="fas fa-copy"></i>
            </button>
          </div>
          <div class="access-code-note">
            <i class="fas fa-info-circle" style="color: #f59e0b;"></i>
            <span style="color: #6b7280;">Comparte tanto el enlace como el código de acceso</span>
          </div>
        </div>
        
        <div class="link-permissions" id="link-permissions">
          <div class="permission-item">
            <i class="fas fa-eye" style="color: #4a90e2;"></i>
            <span style="color: #6b7280;">Permisos: <strong id="link-permission-text" style="color: #2d3748;">Solo lectura</strong></span>
          </div>
          <div class="permission-item" id="link-expiry" style="display: none;">
            <i class="fas fa-clock" style="color: #4a90e2;"></i>
            <span style="color: #6b7280;">Expira: <strong id="link-expiry-text" style="color: #2d3748;"></strong></span>
          </div>
        </div>
      </div>
      <div class="link-modal-footer">
        <button class="btn-secondary" onclick="closeLinkModal()">Cerrar</button>
        <button class="btn-primary" onclick="copyLinkToClipboard()">
          <i class="fas fa-copy"></i> Copiar Enlace
        </button>
      </div>
    </div>
  </div>

  <!-- System Modal -->
  <div class="system-modal-overlay" id="system-modal-overlay">
    <div class="system-modal" id="system-modal">
      <div class="system-modal-header">
        <div class="icon" id="system-modal-icon"><i class="fas fa-info-circle"></i></div>
        <div class="title" id="system-modal-title">Mensaje del sistema</div>
      </div>
      <div class="system-modal-body" id="system-modal-message"></div>
      <div class="system-modal-footer">
        <button class="system-btn primary" onclick="closeSystemModal()">Aceptar</button>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="delete-modal-overlay" id="delete-modal-overlay">
    <div class="delete-modal" id="delete-modal">
      <div class="delete-modal-header">
        <div class="delete-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Confirmar Eliminación</h3>
      </div>
      <div class="delete-modal-body">
        <p id="delete-modal-message">¿Estás seguro de que quieres eliminar este elemento?</p>
        <div class="delete-warning">
          <i class="fas fa-info-circle"></i>
          <span>Esta acción no se puede deshacer</span>
        </div>
      </div>
      <div class="delete-modal-footer">
        <button class="btn-cancel" onclick="closeDeleteModal()">
          <i class="fas fa-times"></i>
          <span>Cancelar</span>
        </button>
        <button class="btn-delete" onclick="confirmDelete()">
          <i class="fas fa-trash"></i>
          <span>Eliminar</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal de Vista Previa Expandida -->
  <div class="preview-modal" id="previewModal" style="display: none;">
    <div class="preview-modal-overlay" onclick="closePreviewModal()"></div>
    <div class="preview-modal-content">
      <div class="preview-modal-header">
        <div class="preview-modal-title">
          <i class="fas fa-eye"></i>
          <span id="previewModalTitle">Vista Previa</span>
        </div>
        <button class="preview-modal-close" onclick="closePreviewModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="preview-modal-body" id="previewModalBody">
        <div class="preview-modal-loading">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Cargando vista previa...</span>
        </div>
      </div>
      <div class="preview-modal-footer">
        <button class="btn btn-secondary" onclick="closePreviewModal()">
          <i class="fas fa-times"></i>
          Cerrar
        </button>
        <button class="btn btn-primary" onclick="downloadFromModal()" id="downloadModalBtn">
          <i class="fas fa-download"></i>
          Descargar
        </button>
      </div>
    </div>
  </div>

  <!-- Futuristic particle animation -->
  <canvas id="particles-canvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0;"></canvas>

  <script>
    // Particle animation for futuristic effect
    (function() {
      const canvas = document.getElementById('particles-canvas');
      const ctx = canvas.getContext('2d');
      let particles = [];
      
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      
      class Particle {
        constructor() {
          this.x = Math.random() * canvas.width;
          this.y = Math.random() * canvas.height;
          this.vx = (Math.random() - 0.5) * 0.5;
          this.vy = (Math.random() - 0.5) * 0.5;
          this.radius = Math.random() * 2;
          this.opacity = Math.random() * 0.5 + 0.2;
        }
        
        update() {
          this.x += this.vx;
          this.y += this.vy;
          
          if (this.x < 0 || this.x > canvas.width) this.vx = -this.vx;
          if (this.y < 0 || this.y > canvas.height) this.vy = -this.vy;
        }
        
        draw() {
          ctx.beginPath();
          ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(102, 126, 234, ${this.opacity})`;
          ctx.fill();
        }
      }
      
      function init() {
        for (let i = 0; i < 50; i++) {
          particles.push(new Particle());
        }
      }
      
      function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(particle => {
          particle.update();
          particle.draw();
        });
        
        // Draw connections
        particles.forEach((p1, i) => {
          particles.slice(i + 1).forEach(p2 => {
            const dist = Math.hypot(p1.x - p2.x, p1.y - p2.y);
            if (dist < 100) {
              ctx.beginPath();
              ctx.moveTo(p1.x, p1.y);
              ctx.lineTo(p2.x, p2.y);
              ctx.strokeStyle = `rgba(102, 126, 234, ${0.1 * (1 - dist / 100)})`;
              ctx.stroke();
            }
          });
        });
        
        requestAnimationFrame(animate);
      }
      
      window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      });
      
      init();
      animate();
    })();
  </script>

  <script>
    // Test Font Awesome loading
    document.addEventListener('DOMContentLoaded', function() {
      const testIcon = document.createElement('i');
      testIcon.className = 'fas fa-check';
      testIcon.style.display = 'none';
      document.body.appendChild(testIcon);
      
      setTimeout(() => {
        const computed = window.getComputedStyle(testIcon);
        if (computed.fontFamily.includes('Font Awesome')) {
          console.log('✅ Font Awesome loaded successfully');
        } else {
          console.warn('⚠️ Font Awesome may not be loaded properly');
        }
        document.body.removeChild(testIcon);
      }, 100);
    });
  </script>
  
  <script>
    // Global State
    let currentFolder = 0;
    let selectedItems = new Set();
    let currentView = 'grid';
    let contextTarget = null;
    let modalCallback = null;
    let selectedColor = '';
    let isSharedWithMeView = false;
    let sharedFolderIds = new Set(); // Track which folders are shared
    let columnPath = []; // For column view navigation
    let draggedItem = null;
    let moveSourceItem = null;
    let moveTargetItem = null;
    let selectedIcon = '';
    let isSelecting = false;
    let selectionStart = { x: 0, y: 0 };
    let selectionBox = null;
    let initialSelection = new Set(); // Track items selected before drag started
    const csrf = '<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>';

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, initializing...');
      console.log('Current folder from PHP:', <?= (int)($parent ?? 0) ?>);
      
      // Load initial data from PHP
      const initialFolders = <?= json_encode($folders) ?>;
      const initialFiles = <?= json_encode($files) ?>;
      console.log('Initial folders from PHP:', initialFolders);
      console.log('Initial files from PHP:', initialFiles);
      
      // Set current folder from PHP
      currentFolder = <?= (int)($parent ?? 0) ?>;
      
      // Render initial data immediately
      renderItems(initialFolders, initialFiles);
      updateBreadcrumb(currentFolder);
      
      // Setup event listeners but don't reload via AJAX initially
      setupEventListeners();
      
      // Load storage quota information
      loadStorageQuota();
      
      // Renovar CSRF token automáticamente cada 30 minutos
      setInterval(refreshCsrfToken, 30 * 60 * 1000); // 30 minutos
    });

    function setupEventListeners() {
      // File input
      document.getElementById('file-input').addEventListener('change', handleFileUpload);
      
      // Upload area drag & drop
      const uploadArea = document.getElementById('upload-area');
      uploadArea.addEventListener('dragover', e => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
      });
      uploadArea.addEventListener('dragleave', e => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
      });
      uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        handleFileUpload({ target: { files: e.dataTransfer.files } });
      });

      // Context menus
      document.addEventListener('contextmenu', (e) => {
        // Store cursor position for color picker
        window.lastRightClickX = e.clientX;
        window.lastRightClickY = e.clientY;
        
        // Empty-area context (grid, list, columns, explorer background)
        const isOnItem = e.target.closest('.grid-item, .list-item, .column-item');
        const inExplorer = e.target.closest('#explorer');
        if (inExplorer && !isOnItem) {
          e.preventDefault();
          hideContextMenus();
          const menu = document.getElementById('empty-context-menu');
          menu.style.display = 'block';
          menu.style.left = e.pageX + 'px';
          menu.style.top = e.pageY + 'px';
          return;
        }
        handleContextMenu(e);
      });
      document.addEventListener('click', hideContextMenus);
      
      // Color picker and icon picker
      setupColorPicker();
      setupIconPicker();
      setupTrashBin();
      setupMouseSelection();
      
      // Keyboard shortcuts
      document.addEventListener('keydown', handleKeyboard);
      
      // Click outside to deselect
      document.addEventListener('click', e => {
        if (!e.target.closest('.grid-item, .list-item, .column-item')) {
          clearSelection();
        }
      });
    }

    // Navigation
    async function loadFolder(folderId) {
      try {
        console.log('Loading folder:', folderId);
        const url = '/biblioteca/public/index.php/drive/list?folder=' + folderId;
        console.log('Fetching URL:', url);
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }
        
        const data = await response.json();
        console.log('Data received:', data);
        
        currentFolder = folderId;
        renderItems(data.folders, data.files);
        updateBreadcrumb(folderId);
        clearSelection();
        
        // Reinitialize column view if active
        if (currentView === 'columns' && !isSharedWithMeView) {
          setTimeout(() => {
            initializeColumnView();
          }, 100);
        }
      } catch (error) {
        console.error('Error loading folder:', error);
      }
    }

    function navigateToFolder(folderId) {
      console.log('Navigating to folder:', folderId);
      isSharedWithMeView = false;
      loadFolder(folderId);
      
      // Update active tree item
      document.querySelectorAll('.tree-item').forEach(item => {
        item.classList.toggle('active', parseInt(item.dataset.folder) === folderId);
      });
    }
    
    // Load shared with me files
    async function loadSharedWithMe() {
      try {
        isSharedWithMeView = true;
        console.log('Loading shared with me files');
        const url = '/biblioteca/public/index.php/drive/shared-with-me';
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }
        
        const data = await response.json();
        console.log('Shared data received:', data);
        
        // Track shared folder IDs
        sharedFolderIds.clear();
        if (data.files) {
          data.files.forEach(file => {
            if (file.type === 'folder') {
              sharedFolderIds.add(parseInt(file.id));
            }
          });
        }
        
        currentFolder = 'shared';
        renderSharedItems(data.files || []);
        updateBreadcrumbForShared();
        updateSidebarSelection('shared');
        clearSelection();
        
        // Clear column view if active since shared view doesn't support columns
        if (currentView === 'columns') {
          const columnContainer = document.getElementById('column-container');
          if (columnContainer) {
            columnContainer.innerHTML = '';
          }
        }
      } catch (error) {
        console.error('Error loading shared files:', error);
        showError('Error al cargar archivos compartidos');
      }
    }
    
    // Load shared folder contents
    async function loadSharedFolder(folderId) {
      try {
        console.log('Loading shared folder contents:', folderId);
        const url = '/biblioteca/public/index.php/drive/shared-folder-contents?folder_id=' + folderId;
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          if (response.status === 403) {
            showError('No tienes permisos para acceder a esta carpeta');
            return;
          }
          throw new Error('HTTP ' + response.status);
        }
        
        const data = await response.json();
        console.log('Shared folder data received:', data);
        
        if (data.ok) {
          currentFolder = folderId;
          isSharedWithMeView = false; // We're now in a specific shared folder
          
          // Render the folder contents normally
          const gridView = document.getElementById('grid-view');
          const listContent = document.getElementById('list-content');
          
          gridView.innerHTML = '';
          listContent.innerHTML = '';
          
          // Add normal header for list view
          const listHeader = document.createElement('div');
          listHeader.className = 'list-header';
          listHeader.innerHTML = `
            <div>Nombre</div>
            <div>Tipo</div>
            <div>Fecha</div>
            <div>Tamaño</div>
          `;
          listContent.appendChild(listHeader);
          
          // Render folders
          data.folders.forEach(folder => {
            const folderIcon = 'fas fa-folder';
            const gridItem = createGridItem('folder', folder.id, folder.nombre, folderIcon, folder.etiqueta, folder.color_etiqueta, folder.icono_personalizado);
            const listItem = createListItem('folder', folder.id, folder.nombre, 'Carpeta', '', '', folder.etiqueta, folder.color_etiqueta, folder.icono_personalizado);
            gridView.appendChild(gridItem);
            listContent.appendChild(listItem);
          });
          
          // Render files
          data.files.forEach(file => {
            const icon = getFileIcon(file.mime || file.extension);
            const gridItem = createGridItem('file', file.id, file.nombre, icon);
            const listItem = createListItem('file', file.id, file.nombre, file.mime || 'Archivo', '', '');
            gridView.appendChild(gridItem);
            listContent.appendChild(listItem);
          });
          
          // Update breadcrumb to show shared folder path
          updateBreadcrumbForSharedFolder(data.folder_name);
          updateSidebarSelection(null);
          clearSelection();
        } else {
          throw new Error(data.error || 'Error desconocido');
        }
      } catch (error) {
        console.error('Error loading shared folder:', error);
        showError('Error al cargar carpeta compartida');
      }
    }
    
    // Update sidebar selection
    function updateSidebarSelection(type, folderId = null) {
      // Remove active class from all sidebar items
      document.querySelectorAll('.tree-item').forEach(item => {
        item.classList.remove('active');
      });
      
      if (type === 'shared') {
        document.getElementById('shared-with-me-item').classList.add('active');
      } else if (type === 'drive') {
        // Select the main DRIVE item (folder 0)
        const driveItem = document.querySelector('[data-folder="0"]');
        if (driveItem) {
          driveItem.classList.add('active');
        }
      } else if (type === 'folder') {
        const folderItem = document.querySelector(`[data-folder="${folderId}"]`);
        if (folderItem) {
          folderItem.classList.add('active');
        }
      }
    }
    
    // Update breadcrumb for shared section
    function updateBreadcrumbForShared() {
      const breadcrumb = document.getElementById('breadcrumb');
      if (breadcrumb) {
        breadcrumb.innerHTML = `
          <span class="breadcrumb-item" onclick="loadFolder(0)">
            <i class="fas fa-hdd"></i> DRIVE
          </span>
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-item active">
            <i class="fas fa-users"></i> Compartido conmigo
          </span>
        `;
      }
    }
    
    // Update breadcrumb for shared folder
    function updateBreadcrumbForSharedFolder(folderName) {
      const breadcrumb = document.getElementById('breadcrumb');
      if (breadcrumb) {
        breadcrumb.innerHTML = `
          <span class="breadcrumb-item" onclick="loadFolder(0)">
            <i class="fas fa-hdd"></i> DRIVE
          </span>
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-item" onclick="loadSharedWithMe()">
            <i class="fas fa-users"></i> Compartido conmigo
          </span>
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-item active">
            <i class="fas fa-folder"></i> ` + folderName + `
          </span>
        `;
      }
    }
    
    // Render shared items with additional info
    function renderSharedItems(files) {
      const gridView = document.getElementById('grid-view');
      const listContent = document.getElementById('list-content');
      
      // Clear all views
      gridView.innerHTML = '';
      listContent.innerHTML = '';
      
      if (files.length === 0) {
        const emptyStateHtml = `
          <div class="empty-state">
            <div class="empty-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="empty-title">No hay archivos compartidos</div>
            <div class="empty-subtitle">Los archivos que otros usuarios compartan contigo aparecerán aquí</div>
          </div>
        `;
        gridView.innerHTML = emptyStateHtml;
        listContent.innerHTML = emptyStateHtml;
        return;
      }
      
      // Add custom header for list view of shared files
      const listHeader = document.createElement('div');
      listHeader.className = 'list-header';
      listHeader.style.gridTemplateColumns = '1fr 150px 120px 100px 80px';
      listHeader.innerHTML = `
        <div>Nombre</div>
        <div>Propietario</div>
        <div>Permiso</div>
        <div>Compartido</div>
        <div>Tamaño</div>
      `;
      listContent.appendChild(listHeader);
      
      // Render items for both views
      files.forEach(file => {
        const gridItem = createSharedGridElement(file);
        const listItem = createSharedListElement(file);
        
        gridView.appendChild(gridItem);
        listContent.appendChild(listItem);
      });
    }
    
    // Create shared grid element with owner info
    function createSharedGridElement(file) {
      const item = document.createElement('div');
      item.className = file.type === 'folder' ? 'grid-item shared-folder' : 'grid-item';
      item.dataset.id = file.id;
      item.dataset.type = file.type || 'file';
      item.dataset.name = file.nombre;
      
      const ownerName = file.owner_name || 'Usuario desconocido';
      const sharedDate = file.shared_date ? new Date(file.shared_date).toLocaleDateString() : '';
      const permission = file.permission || 'lector';
      const permissionIcon = getPermissionIcon(permission);
      
      // Determinar el icono correcto según el tipo
      let fileIcon;
      if (file.type === 'folder') {
        fileIcon = 'fas fa-folder';
      } else {
        fileIcon = getFileIcon(file.tipo_mime || file.extension);
      }
      
      item.innerHTML = `
        <div class="shared-indicator" title="Compartido por ${ownerName}">
          <i class="fas fa-share-alt"></i>
        </div>
        <div class="icon">
          <i class="${fileIcon}"></i>
        </div>
        <div class="name" title="${file.nombre}">${file.nombre}</div>
        <div class="shared-info">
          <div class="owner">
            <i class="fas fa-user"></i>
            <span>${ownerName}</span>
          </div>
          <div class="permission">
            <i class="${permissionIcon}"></i>
            <span>${getPermissionName(permission)}</span>
          </div>
          ${sharedDate ? `<div class="shared-date">${sharedDate}</div>` : ''}
        </div>
      `;
      
      // Add click event
      item.addEventListener('click', (e) => {
        if (e.ctrlKey || e.metaKey) {
          toggleSelection(item);
        } else {
          selectItem(item);
        }
      });
      
      // Add click event for opening (single click)
      item.addEventListener('click', (e) => {
        // Only open if not selecting multiple items
        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
          openItem(file.type, parseInt(file.id));
        }
      });
      
      // Add double-click event for download (only for files)
      if (file.type === 'file') {
        item.addEventListener('dblclick', (e) => {
          e.stopPropagation();
          e.preventDefault();
          console.log('Double click download shared file:', file.id);
          downloadFile(file.id, file.nombre);
        });
      }
      
      return item;
    }
    
    // Create shared list element with owner info
    function createSharedListElement(file) {
      const item = document.createElement('div');
      item.className = file.type === 'folder' ? 'list-item shared-file shared-folder' : 'list-item shared-file';
      item.dataset.id = file.id;
      item.dataset.type = file.type || 'file';
      item.dataset.name = file.nombre;
      
      const ownerName = file.owner_name || 'Usuario desconocido';
      const sharedDate = file.shared_date ? new Date(file.shared_date).toLocaleDateString() : '';
      const permission = file.permission || 'lector';
      const permissionIcon = getPermissionIcon(permission);
      
      // Determinar el icono correcto según el tipo
      let fileIcon;
      if (file.type === 'folder') {
        fileIcon = 'fas fa-folder';
      } else {
        fileIcon = getFileIcon(file.tipo_mime || file.extension);
      }
      
      const fileSize = file.tamaño ? formatBytes(file.tamaño) : '';
      
      item.innerHTML = `
        <div class="shared-indicator" title="Compartido por ${ownerName}">
          <i class="fas fa-share-alt"></i>
        </div>
        <div class="icon">
          <i class="${fileIcon}"></i>
        </div>
        <div class="name" title="${file.nombre}">${file.nombre}</div>
        <div class="owner" title="Propietario: ${ownerName}">
          <i class="fas fa-user" style="margin-right: 4px;"></i>
          ${ownerName}
        </div>
        <div class="permission" title="Permiso: ${getPermissionName(permission)}">
          <i class="${permissionIcon}" style="margin-right: 4px;"></i>
          ${getPermissionName(permission)}
        </div>
        <div class="shared-date" title="Compartido: ${sharedDate}">${sharedDate}</div>
        <div class="size">${fileSize}</div>
      `;
      
      // Add click event
      item.addEventListener('click', (e) => {
        if (e.ctrlKey || e.metaKey) {
          toggleSelection(item);
        } else {
          selectItem(item);
        }
      });
      
      // Add click event for opening (single click)
      item.addEventListener('click', (e) => {
        // Only open if not selecting multiple items
        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
          openItem(file.type, parseInt(file.id));
        }
      });
      
      // Add double-click event for download (only for files)
      if (file.type === 'file') {
        item.addEventListener('dblclick', (e) => {
          e.stopPropagation();
          e.preventDefault();
          console.log('Double click download shared file:', file.id);
          downloadFile(file.id, file.nombre);
        });
      }
      
      return item;
    }
    
    // Helper functions for shared files
    function formatBytes(bytes) {
      if (bytes === 0 || !bytes) return '';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function getPermissionIcon(permission) {
      const icons = {
        'propietario': 'fas fa-crown',
        'editor': 'fas fa-edit',
        'comentarista': 'fas fa-comment',
        'lector': 'fas fa-eye'
      };
      return icons[permission] || 'fas fa-eye';
    }
    
    function getPermissionName(permission) {
      const names = {
        'propietario': 'Propietario',
        'editor': 'Editor',
        'comentarista': 'Comentarista',
        'lector': 'Lector'
      };
      return names[permission] || 'Lector';
    }

    async function updateBreadcrumb(folderId) {
      const breadcrumb = document.getElementById('breadcrumb');
      if (folderId === 0) {
        breadcrumb.innerHTML = '<a href="#" onclick="navigateToFolder(0)">Mi Biblioteca</a>';
        return;
      }

      try {
        const response = await fetch('/biblioteca/public/index.php/drive/breadcrumb?id=' + folderId);
        const data = await response.json();
        
        let html = '<a href="#" onclick="navigateToFolder(0)">Mi Biblioteca</a>';
        data.breadcrumb.forEach(item => {
          html += '<span class="sep">></span><a href="#" onclick="navigateToFolder(' + item.id + ')">' + item.nombre + '</a>';
        });
        breadcrumb.innerHTML = html;
      } catch (error) {
        console.error('Error loading breadcrumb:', error);
      }
    }

    // Rendering
    function renderItems(folders, files) {
      console.log('Rendering items - Folders:', folders, 'Files:', files);
      
      const gridView = document.getElementById('grid-view');
      const listContent = document.getElementById('list-content');
      
      if (!gridView || !listContent) {
        console.error('Grid view or list content elements not found');
        return;
      }
      
      gridView.innerHTML = '';
      listContent.innerHTML = '';

      // Render folders
      console.log('Rendering', folders.length, 'folders');
      folders.forEach(folder => {
        console.log('Rendering folder:', folder);
        
        // Check if Font Awesome is loaded for folder icon
        const testEl = document.createElement('i');
        testEl.className = 'fas fa-check';
        testEl.style.display = 'none';
        document.body.appendChild(testEl);
        const hasFontAwesome = window.getComputedStyle(testEl).fontFamily.includes('Font Awesome');
        document.body.removeChild(testEl);
        
        const folderIcon = hasFontAwesome ? 'fas fa-folder' : '📁';
        // Ensure we have all the folder properties (from AJAX or initial PHP)
        const etiqueta = folder.etiqueta || null;
        const color = folder.color_etiqueta || null;
        const icono = folder.icono_personalizado || null;
        
        const gridItem = createGridItem('folder', folder.id, folder.nombre, folderIcon, etiqueta, color, icono);
        const listItem = createListItem('folder', folder.id, folder.nombre, 'Carpeta', '', '', etiqueta, color, icono);
        gridView.appendChild(gridItem);
        listContent.appendChild(listItem);
      });

      // Render files
      console.log('Rendering', files.length, 'files');
      files.forEach(file => {
        console.log('Rendering file:', file);
        const icon = getFileIcon(file.mime || file.extension);
        const gridItem = createGridItem('file', file.id, file.nombre, icon);
        const listItem = createListItem('file', file.id, file.nombre, file.mime || 'Archivo', '', '');
        gridView.appendChild(gridItem);
        listContent.appendChild(listItem);
      });
      
      console.log('Finished rendering. Grid children:', gridView.children.length);
    }

    function createGridItem(type, id, name, icon, label = null, labelColor = null, customIcon = null) {
      const item = document.createElement('div');
      item.className = 'grid-item';
      item.dataset.type = type;
      item.dataset.id = id;
      
      // Apply folder coloring if it's a folder with a color
      if (type === 'folder' && labelColor) {
        item.classList.add('folder-colored');
      }
      
      // Check if icon is Font Awesome class or emoji
      let iconHtml;
      if (icon.includes('fa-')) {
        // For folders, default to light gray if no label color
        const iconColor = (type === 'folder') ? (labelColor || '#9ca3af') : '';
        const colorStyle = iconColor ? ' style="color: ' + iconColor + ';"' : '';
        iconHtml = '<i class="' + icon + '"' + colorStyle + '></i>';
      } else {
        iconHtml = '<span style="font-size: 48px;">' + icon + '</span>';
      }
      
      // Add custom icon if exists
      const customIconHtml = customIcon ? '<i class="' + customIcon + ' custom-icon"></i>' : '';
      const iconContainerClass = customIcon ? ' has-custom' : '';
      
      // Add label if exists
      const labelHtml = (label && labelColor) ? '<div class="label" style="background-color: ' + labelColor + ';">' + label + '</div>' : '';
      
      item.innerHTML = '<div class="icon' + iconContainerClass + '">' + iconHtml + customIconHtml + '</div><div class="name"><span class="name-chip">' + name + '</span></div>' + labelHtml;
      
      item.addEventListener('click', e => {
        handleItemClick(e, item);
        // Open with single click if no modifier keys pressed
        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
          console.log('Single click on:', type, id);
          openItem(type, id);
        }
      });
      
      // Add double-click event for download (only for files)
      if (type === 'file') {
        item.addEventListener('dblclick', (e) => {
          e.stopPropagation();
          e.preventDefault();
          console.log('Double click download:', type, id);
          downloadFile(id, name);
        });
      }
      
      // Add drag & drop functionality
      setupDragAndDrop(item);
      
      return item;
    }

    function createListItem(type, id, name, fileType, date, size, label = null, labelColor = null, customIcon = null) {
      const item = document.createElement('div');
      item.className = 'list-item';
      item.dataset.type = type;
      item.dataset.id = id;
      const iconName = type === 'folder' ? 'fas fa-folder' : getFileIcon(fileType);
      
      // Check if icon is Font Awesome class or emoji and apply color for folders
      let iconHtml;
      if (iconName.includes('fa-')) {
        const iconColor = (type === 'folder') ? (labelColor || '#9ca3af') : '';
        const colorStyle = iconColor ? ' style="color: ' + iconColor + ';"' : '';
        iconHtml = '<i class="' + iconName + '"' + colorStyle + '></i>';
      } else {
        iconHtml = '<span>' + iconName + '</span>';
      }
      
      // Add custom icon for folders in list view (smaller)
      const customIconHtml = (type === 'folder' && customIcon) ? '<i class="' + customIcon + '" style="margin-left: 4px; font-size: 12px;"></i>' : '';
      
      // Add label if exists
      const labelHtml = (label && labelColor) ? '<span class="label" style="background-color: ' + labelColor + ';">' + label + '</span>' : '';
      
      item.innerHTML = '<div class="name">' + iconHtml + '<span class="name-chip name-chip--sm">' + name + '</span>' + customIconHtml + labelHtml + '</div><div>' + fileType + '</div><div>' + date + '</div><div>' + size + '</div>';
      
      item.addEventListener('click', e => {
        handleItemClick(e, item);
        // Open with single click if no modifier keys pressed
        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
          console.log('Single click on:', type, id);
          openItem(type, id);
        }
      });
      
      // Add double-click event for download (only for files)
      if (type === 'file') {
        item.addEventListener('dblclick', (e) => {
          e.stopPropagation();
          e.preventDefault();
          console.log('Double click download:', type, id);
          downloadFile(id, name);
        });
      }
      
      // Add drag & drop functionality
      setupDragAndDrop(item);
      
      return item;
    }

    function getFileIcon(mimeType) {
      // Enhanced file type detection with specific icons
      if (!mimeType) return 'fas fa-file';
      
      const mime = mimeType.toLowerCase();
      
      console.log('DEBUG: Getting icon for mime type:', mime); // Debug log
      
      // Si mimeType es una extensión (no contiene '/'), tratarlo como extensión
      if (!mime.includes('/')) {
        return getFileIconByExtension(mime);
      }
      
      // Microsoft Office Documents
      if (mime.includes('word') || mime.includes('msword') || mime.includes('wordprocessingml')) return 'fas fa-file-word';
      if (mime.includes('excel') || mime.includes('spreadsheetml') || mime.includes('ms-excel')) return 'fas fa-file-excel';
      if (mime.includes('powerpoint') || mime.includes('presentationml') || mime.includes('ms-powerpoint')) return 'fas fa-file-powerpoint';
      
      // PDF
      if (mime.includes('pdf')) return 'fas fa-file-pdf';
      
      // Images - specific types
      if (mime.includes('jpeg') || mime.includes('jpg')) return 'fas fa-file-image';
      if (mime.includes('png')) return 'fas fa-file-image';
      if (mime.includes('gif')) return 'fas fa-file-image';
      if (mime.includes('svg')) return 'fas fa-file-image';
      if (mime.includes('webp')) return 'fas fa-file-image';
      if (mime.includes('bmp')) return 'fas fa-file-image';
      if (mime.includes('tiff')) return 'fas fa-file-image';
      if (mime.startsWith('image/')) return 'fas fa-file-image';
      
      // Video formats
      if (mime.includes('mp4')) return 'fas fa-file-video';
      if (mime.includes('avi')) return 'fas fa-file-video';
      if (mime.includes('mov') || mime.includes('quicktime')) return 'fas fa-file-video';
      if (mime.includes('wmv')) return 'fas fa-file-video';
      if (mime.includes('flv')) return 'fas fa-file-video';
      if (mime.includes('webm')) return 'fas fa-file-video';
      if (mime.startsWith('video/')) return 'fas fa-file-video';
      
      // Audio formats
      if (mime.includes('mp3')) return 'fas fa-file-audio';
      if (mime.includes('wav')) return 'fas fa-file-audio';
      if (mime.includes('flac')) return 'fas fa-file-audio';
      if (mime.includes('aac')) return 'fas fa-file-audio';
      if (mime.includes('ogg')) return 'fas fa-file-audio';
      if (mime.startsWith('audio/')) return 'fas fa-file-audio';
      
      // Archive formats
      if (mime.includes('zip')) return 'fas fa-file-archive';
      if (mime.includes('rar')) return 'fas fa-file-archive';
      if (mime.includes('7z')) return 'fas fa-file-archive';
      if (mime.includes('tar') || mime.includes('gzip')) return 'fas fa-file-archive';
      
      // Code files
      if (mime.includes('javascript') || mime.includes('json')) return 'fas fa-file-code';
      if (mime.includes('html')) return 'fas fa-file-code';
      if (mime.includes('css')) return 'fas fa-file-code';
      if (mime.includes('php')) return 'fas fa-file-code';
      if (mime.includes('python')) return 'fas fa-file-code';
      if (mime.includes('java')) return 'fas fa-file-code';
      if (mime.includes('xml')) return 'fas fa-file-code';
      
      // Text files
      if (mime.includes('text/plain')) return 'fas fa-file-alt';
      if (mime.includes('rtf')) return 'fas fa-file-alt';
      if (mime.includes('csv')) return 'fas fa-file-csv';
      
      // Adobe files
      if (mime.includes('photoshop') || mime.includes('psd')) return 'fas fa-file-image';
      if (mime.includes('illustrator') || mime.includes('ai')) return 'fas fa-file-image';
      if (mime.includes('indesign')) return 'fas fa-file-image';
      
      // Other common formats
      if (mime.includes('font') || mime.includes('ttf') || mime.includes('woff')) return 'fas fa-font';
      if (mime.includes('calendar') || mime.includes('ics')) return 'fas fa-calendar';
      
      // Default fallbacks
      if (mime.startsWith('text/')) return 'fas fa-file-alt';
      if (mime.startsWith('application/')) return 'fas fa-file';
      
      return 'fas fa-file';
    }

    // Función para obtener ícono por extensión de archivo
    function getFileIconByExtension(extension) {
      if (!extension) return 'fas fa-file';
      
      const ext = extension.toLowerCase().replace('.', '');
      console.log('DEBUG: Getting icon for extension:', ext);
      
      // Microsoft Office
      if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word';
      if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel';
      if (['ppt', 'pptx'].includes(ext)) return 'fas fa-file-powerpoint';
      
      // PDF
      if (ext === 'pdf') return 'fas fa-file-pdf';
      
      // Imágenes
      if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'].includes(ext)) return 'fas fa-file-image';
      
      // Video
      if (['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'].includes(ext)) return 'fas fa-file-video';
      
      // Audio
      if (['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'].includes(ext)) return 'fas fa-file-audio';
      
      // Archivos comprimidos y discos
      if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'dmg', 'iso', 'img'].includes(ext)) return 'fas fa-file-archive';
      
      // Archivos ejecutables
      if (['exe', 'msi', 'deb', 'rpm', 'pkg', 'app'].includes(ext)) return 'fas fa-cog';
      
      // Código
      if (['js', 'html', 'css', 'php', 'py', 'java', 'cpp', 'c', 'xml', 'json'].includes(ext)) return 'fas fa-file-code';
      
      // Texto
      if (['txt', 'rtf', 'md'].includes(ext)) return 'fas fa-file-alt';
      if (ext === 'csv') return 'fas fa-file-csv';
      
      // Otros
      if (['ttf', 'woff', 'woff2', 'otf'].includes(ext)) return 'fas fa-font';
      if (ext === 'ics') return 'fas fa-calendar';
      
      return 'fas fa-file';
    }

    // Selection and Actions
    function handleItemClick(e, item) {
      e.stopPropagation();
      
      if (e.ctrlKey || e.metaKey) {
        toggleSelection(item);
      } else if (e.shiftKey) {
        toggleSelection(item);
      } else {
        clearSelection();
        selectItem(item);
      }
      updateToolbar();
    }

    function selectItem(item) {
      item.classList.add('selected');
      selectedItems.add(item);
    }
    
    function deselectItem(item) {
      item.classList.remove('selected');
      selectedItems.delete(item);
    }

    function toggleSelection(item) {
      if (selectedItems.has(item)) {
        item.classList.remove('selected');
        selectedItems.delete(item);
      } else {
        selectItem(item);
      }
    }

    function clearSelection() {
      selectedItems.forEach(item => item.classList.remove('selected'));
      selectedItems.clear();
      updateToolbar();
    }

    function updateToolbar() {
      const hasSelection = selectedItems.size > 0;
      document.getElementById('btn-rename').disabled = selectedItems.size !== 1;
      document.getElementById('btn-delete').disabled = !hasSelection;
      
      // Update selection counter
      const selectionInfo = document.getElementById('selection-info');
      const selectionCount = document.getElementById('selection-count');
      
      if (hasSelection) {
        selectionInfo.style.display = 'flex';
        selectionCount.textContent = selectedItems.size;
      } else {
        selectionInfo.style.display = 'none';
      }
    }

    function openItem(type, id) {
      console.log('Opening item:', type, id);
      if (type === 'folder') {
        console.log('Navigating to folder:', id);
        
        // Check if this is a shared folder
        if (sharedFolderIds.has(parseInt(id))) {
          console.log('Opening shared folder:', id);
          loadSharedFolder(id);
        } else {
          // Regular folder navigation
          navigateToFolder(id);
        }
      } else if (type === 'file') {
        console.log('Opening file preview:', id);
        openFilePreview(id);
      } else {
        console.warn('Unknown item type:', type);
      }
    }

    function openSelected() {
      if (selectedItems.size === 1) {
        const item = Array.from(selectedItems)[0];
        openItem(item.dataset.type, parseInt(item.dataset.id));
      }
    }

    // Modal functions
    function showModal(title, defaultValue, callback) {
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-input').value = defaultValue;
      document.getElementById('modal-overlay').style.display = 'flex';
      document.getElementById('modal-input').focus();
      document.getElementById('modal-input').select();
      modalCallback = callback;
    }

    function closeModal() {
      document.getElementById('modal-overlay').style.display = 'none';
      modalCallback = null;
    }

    function confirmModal() {
      const value = document.getElementById('modal-input').value.trim();
      if (value && modalCallback) {
        modalCallback(value);
      }
      closeModal();
    }

    // File operations
    async function createFolder() {
      showModal('Nueva Carpeta', 'Nueva Carpeta', async (name) => {
        try {
          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('name', name);
          if (currentFolder > 0) {
            formData.append('parent', currentFolder);
          }

          const response = await fetch('/biblioteca/public/index.php/drive/folder', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          });

          const data = await response.json();
          if (data.ok) {
            // Add new folder to DOM instead of reloading everything
            const gridView = document.getElementById('grid-view');
            const listContent = document.getElementById('list-content');
            
            const folderIcon = 'fas fa-folder';
            const gridItem = createGridItem('folder', data.id, name, folderIcon, null, null, null);
            const listItem = createListItem('folder', data.id, name, 'Carpeta', '', '', null, null, null);
            
            gridView.appendChild(gridItem);
            listContent.appendChild(listItem);
            
            // Apply current contrast to new items
            applyCurrentContrastToNewItems();
            
            console.log('New folder added:', name, 'ID:', data.id);
          } else {
            console.error('Error creating folder:', data);
            showError(data.error || 'Error al crear carpeta');
          }
        } catch (error) {
          console.error('Network error:', error);
          showError('Error de red al crear carpeta');
        }
      });
    }

    async function renameSelected() {
      if (selectedItems.size !== 1) return;
      
      const item = Array.from(selectedItems)[0];
      const currentName = item.querySelector('.name').textContent.trim();
      
      showModal('Renombrar', currentName, async (newName) => {
        try {
          const type = item.dataset.type;
          const id = item.dataset.id;
          const endpoint = type === 'folder' ? '/drive/folder/rename' : '/drive/file/rename';
          
          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('id', id);
          formData.append('name', newName);

          const response = await fetch('/biblioteca/public/index.php' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          });

          const data = await response.json();
          if (data.ok) {
            // Update item name in DOM instead of reloading everything
            const nameElement = item.querySelector('.name .name-chip');
            if (nameElement) {
              nameElement.textContent = newName;
            }
            console.log('Item renamed to:', newName);
          } else {
            showError(data.error || 'Error al renombrar');
          }
        } catch (error) {
          console.error('Rename error:', error);
          showError('Error al renombrar');
        }
      });
    }

    async function deleteSelected() {
      if (selectedItems.size === 0) return;
      
      if (!confirm('¿Eliminar ' + selectedItems.size + ' elemento(s)?')) return;

      try {
        const itemsToDelete = Array.from(selectedItems);
        
        for (const item of itemsToDelete) {
          const type = item.dataset.type;
          const id = item.dataset.id;
          const endpoint = type === 'folder' ? '/drive/folder/delete' : '/drive/file/delete';
          
          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('id', id);

          const response = await fetch('/biblioteca/public/index.php' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          });
          
          const result = await response.json();
          if (result.ok) {
            // Remove item from DOM instead of reloading everything
            item.remove();
            selectedItems.delete(item);
          } else {
            console.error('Error deleting item:', result);
            showError('Error al eliminar: ' + (result.error || 'Error desconocido'));
          }
        }
        
        updateToolbar();
      } catch (error) {
        console.error('Delete error:', error);
        showError('Error al eliminar');
      }
    }

    // Share file/folder - Open sharing modal
    function shareSelected() {
      if (!contextTarget && selectedItems.size !== 1) return;
      
      let item;
      if (contextTarget) {
        item = contextTarget;
      } else {
        item = Array.from(selectedItems)[0];
      }
      
      const id = item.dataset.id;
      const type = item.dataset.type;
      const name = item.dataset.name || item.querySelector('.name')?.textContent || 'Elemento';
      
      openSharingModal(id, type, name);
    }
    
    // Variables globales para el modal de compartición
    let currentSharingItem = null;
    let selectedSharingOption = null;
    let userEmails = [];
    
    // Abrir modal de compartición
    function openSharingModal(itemId, itemType, itemName) {
      currentSharingItem = { id: itemId, type: itemType, name: itemName };
      
      // Actualizar información del archivo
      document.getElementById('share-file-name').textContent = itemName;
      
      // Determinar icono según tipo y extensión
      const iconElement = document.getElementById('share-file-icon').querySelector('i');
      if (itemType === 'folder') {
        iconElement.className = 'fas fa-folder';
      } else {
        // Determinar icono por extensión
        const ext = itemName.split('.').pop().toLowerCase();
        switch (ext) {
          case 'pdf': iconElement.className = 'fas fa-file-pdf'; break;
          case 'doc': case 'docx': iconElement.className = 'fas fa-file-word'; break;
          case 'xls': case 'xlsx': iconElement.className = 'fas fa-file-excel'; break;
          case 'ppt': case 'pptx': iconElement.className = 'fas fa-file-powerpoint'; break;
          case 'jpg': case 'jpeg': case 'png': case 'gif': iconElement.className = 'fas fa-file-image'; break;
          case 'mp4': case 'avi': case 'mov': iconElement.className = 'fas fa-file-video'; break;
          case 'mp3': case 'wav': case 'flac': iconElement.className = 'fas fa-file-audio'; break;
          case 'zip': case 'rar': case '7z': iconElement.className = 'fas fa-file-archive'; break;
          case 'txt': iconElement.className = 'fas fa-file-alt'; break;
          default: iconElement.className = 'fas fa-file';
        }
      }
      
      // Resetear formularios
      resetSharingForms();
      
      // Mostrar modal
      document.getElementById('sharing-modal').style.display = 'flex';
    }
    
    // Cerrar modal de compartición
    function closeSharingModal() {
      document.getElementById('sharing-modal').style.display = 'none';
      currentSharingItem = null;
      selectedSharingOption = null;
      resetSharingForms();
    }
    
    // Resetear formularios
    function resetSharingForms() {
      // Limpiar selección de opciones
      document.querySelectorAll('.sharing-option').forEach(option => {
        option.classList.remove('selected');
      });
      
      // Ocultar todos los formularios
      document.querySelectorAll('.sharing-form').forEach(form => {
        form.style.display = 'none';
      });
      
      // Resetear campos
      document.getElementById('user-emails').value = '';
      document.getElementById('user-tags').innerHTML = '';
      document.getElementById('user-message').value = '';
      document.getElementById('user-expiry').value = '';
      document.getElementById('group-expiry').value = '';
      document.getElementById('link-password').value = '';
      document.getElementById('link-expiry').value = '';
      document.getElementById('link-domains').value = '';
      
      // Resetear checkboxes avanzados
      document.getElementById('can-download').checked = true;
      document.getElementById('can-print').checked = true;
      document.getElementById('can-copy').checked = true;
      document.getElementById('notify-changes').checked = false;
      document.getElementById('notify-access').checked = false;
      document.getElementById('require-auth').checked = false;
      
      // Ocultar opciones avanzadas
      document.getElementById('advanced-content').style.display = 'none';
      document.getElementById('advanced-chevron').className = 'fas fa-chevron-right';
      
      // Deshabilitar botón compartir
      document.getElementById('share-btn').disabled = true;
      
      userEmails = [];
    }
    
    // Funciones del modal de enlace compartido
    function showLinkModal(linkData, sharingItem = null) {
      // Usar el item pasado como parámetro o el global si no se proporciona
      const item = sharingItem || currentSharingItem;
      
      if (!item) {
        console.error('No se encontró información del item para mostrar en el modal');
        return;
      }
      
      // Configurar información del archivo/carpeta
      const iconElement = document.getElementById('link-item-icon');
      const nameElement = document.getElementById('link-item-name');
      const typeElement = document.getElementById('link-item-type');
      
      // Establecer icono según el tipo
      if (item.type === 'folder') {
        iconElement.innerHTML = '<i class="fas fa-folder"></i>';
        typeElement.textContent = 'Carpeta';
      } else {
        iconElement.innerHTML = '<i class="fas fa-file"></i>';
        typeElement.textContent = 'Archivo';
      }
      
      nameElement.textContent = item.name;
      
      // Configurar enlace
      document.getElementById('generated-link').value = linkData.url;
      
      // Configurar código de acceso si existe
      const accessCodeSection = document.getElementById('access-code-section');
      if (linkData.access_code) {
        document.getElementById('access-code').value = linkData.access_code;
        accessCodeSection.style.display = 'block';
      } else {
        accessCodeSection.style.display = 'none';
      }
      
      // Configurar permisos
      document.getElementById('link-permission-text').textContent = linkData.permission || 'Solo lectura';
      
      // Configurar expiración si existe
      const expiryElement = document.getElementById('link-expiry');
      const expiryTextElement = document.getElementById('link-expiry-text');
      if (linkData.expires) {
        const expiryDate = new Date(linkData.expires);
        expiryTextElement.textContent = expiryDate.toLocaleDateString('es-ES', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        expiryElement.style.display = 'block';
      } else {
        expiryElement.style.display = 'none';
      }
      
      // Mostrar modal
      document.getElementById('link-modal-overlay').style.display = 'flex';
    }
    
    function closeLinkModal() {
      document.getElementById('link-modal-overlay').style.display = 'none';
    }
    
    async function copyLinkToClipboard() {
      const linkInput = document.getElementById('generated-link');
      const copyBtn = document.querySelector('.btn-primary');
      const originalText = copyBtn.innerHTML;
      
      try {
        await navigator.clipboard.writeText(linkInput.value);
        
        // Feedback visual
        copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        copyBtn.style.background = '#28a745';
        
        // Seleccionar texto temporalmente
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        
        setTimeout(() => {
          copyBtn.innerHTML = originalText;
          copyBtn.style.background = '#007acc';
          linkInput.setSelectionRange(0, 0);
        }, 2000);
        
      } catch (err) {
        console.error('Error copiando al portapapeles:', err);
        // Fallback para navegadores que no soportan clipboard API
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        
        try {
          document.execCommand('copy');
          copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
          copyBtn.style.background = '#28a745';
          
          setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.style.background = '#007acc';
          }, 2000);
        } catch (fallbackErr) {
          showInfo('No se pudo copiar automáticamente. Por favor, selecciona y copia manualmente el enlace.');
        }
      }
    }
    
    async function copyCodeToClipboard() {
      const codeInput = document.getElementById('access-code');
      const copyBtn = codeInput.nextElementSibling;
      const originalIcon = copyBtn.innerHTML;
      
      try {
        await navigator.clipboard.writeText(codeInput.value);
        
        // Feedback visual
        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
        copyBtn.style.background = '#28a745';
        
        setTimeout(() => {
          copyBtn.innerHTML = originalIcon;
          copyBtn.style.background = '#007acc';
        }, 2000);
        
      } catch (err) {
        console.error('Error copiando código:', err);
        // Fallback
        codeInput.select();
        codeInput.setSelectionRange(0, 99999);
        
        try {
          document.execCommand('copy');
          copyBtn.innerHTML = '<i class="fas fa-check"></i>';
          copyBtn.style.background = '#28a745';
          
          setTimeout(() => {
            copyBtn.innerHTML = originalIcon;
            copyBtn.style.background = '#007acc';
          }, 2000);
        } catch (fallbackErr) {
          showInfo('No se pudo copiar automáticamente. Por favor, selecciona y copia manualmente el código.');
        }
      }
    }
    
    // Seleccionar opción de compartición
    function selectSharingOption(option) {
      // Limpiar selección anterior
      document.querySelectorAll('.sharing-option').forEach(opt => {
        opt.classList.remove('selected');
      });
      
      // Ocultar todos los formularios
      document.querySelectorAll('.sharing-form').forEach(form => {
        form.style.display = 'none';
      });
      
      // Seleccionar nueva opción
      document.querySelector(`[data-option="${option}"]`).classList.add('selected');
      selectedSharingOption = option;
      
      // Mostrar formulario correspondiente
      document.getElementById(`${option}-form`).style.display = 'block';
      
      // Cargar datos específicos según la opción
      if (option === 'group') {
        loadAvailableGroups();
      }
      
      // Mostrar/ocultar opciones específicas para enlaces
      const notifyAccessGroup = document.getElementById('notify-access-group');
      const requireAuthGroup = document.getElementById('require-auth-group');
      
      if (option === 'link') {
        notifyAccessGroup.style.display = 'flex';
        requireAuthGroup.style.display = 'flex';
      } else {
        notifyAccessGroup.style.display = 'none';
        requireAuthGroup.style.display = 'none';
      }
      
      // Habilitar botón compartir
      document.getElementById('share-btn').disabled = false;
    }

    // System modal helpers
    function openSystemModal(message, type = 'info', title = null) {
      const overlay = document.getElementById('system-modal-overlay');
      const modal = document.getElementById('system-modal');
      const titleEl = document.getElementById('system-modal-title');
      const iconEl = document.getElementById('system-modal-icon');
      const msgEl = document.getElementById('system-modal-message');

      modal.classList.remove('success', 'error', 'info');
      modal.classList.add(type);

      const iconByType = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle' };
      iconEl.innerHTML = `<i class="fas ${iconByType[type] || iconByType.info}"></i>`;
      titleEl.textContent = title || (type === 'success' ? 'Operación exitosa' : type === 'error' ? 'Ocurrió un error' : 'Mensaje del sistema');
      msgEl.textContent = message;

      overlay.style.display = 'flex';
    }

    function closeSystemModal() {
      document.getElementById('system-modal-overlay').style.display = 'none';
    }

    function showSuccess(msg) { openSystemModal(msg, 'success'); }
    function showError(msg) { openSystemModal(msg, 'error'); }

    // Delete Confirmation Modal Functions
    let deleteCallback = null;
    let deleteItemElement = null;

    function openDeleteModal(itemName, itemType, callback, itemElement) {
      deleteCallback = callback;
      deleteItemElement = itemElement;
      
      const message = `¿Estás seguro de que quieres eliminar ${itemType} "${itemName}"?`;
      document.getElementById('delete-modal-message').textContent = message;
      document.getElementById('delete-modal-overlay').style.display = 'flex';
    }

    function closeDeleteModal() {
      document.getElementById('delete-modal-overlay').style.display = 'none';
      deleteCallback = null;
      deleteItemElement = null;
    }

    function confirmDelete() {
      if (deleteCallback && deleteItemElement) {
        deleteCallback(deleteItemElement);
      }
      closeDeleteModal();
    }
    function showInfo(msg) { openSystemModal(msg, 'info'); }

    
    // Manejar input de emails
    function handleUserEmailInput(event) {
      if (event.key === 'Enter' || event.key === ',') {
        event.preventDefault();
        processUserEmails();
      }
    }
    
    // Procesar emails ingresados
    function processUserEmails() {
      const input = document.getElementById('user-emails');
      const value = input.value.trim();
      
      if (value) {
        const emails = value.split(',').map(email => email.trim()).filter(email => email);
        
        emails.forEach(email => {
          if (email && !userEmails.includes(email) && isValidEmail(email)) {
            userEmails.push(email);
            addUserTag(email);
          }
        });
        
        input.value = '';
      }
    }
    
    // Validar email
    function isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }
    
    // Agregar tag de usuario
    function addUserTag(email) {
      const container = document.getElementById('user-tags');
      const tag = document.createElement('div');
      tag.className = 'user-tag';
      tag.innerHTML = `
        <span>${email}</span>
        <span class="remove" onclick="removeUserTag('${email}')">×</span>
      `;
      container.appendChild(tag);
    }
    
    // Remover tag de usuario
    function removeUserTag(email) {
      const index = userEmails.indexOf(email);
      if (index > -1) {
        userEmails.splice(index, 1);
      }
      
      // Remover del DOM
      const tags = document.querySelectorAll('.user-tag');
      tags.forEach(tag => {
        if (tag.textContent.includes(email)) {
          tag.remove();
        }
      });
    }
    
    // Toggle opciones avanzadas
    function toggleAdvancedOptions() {
      const content = document.getElementById('advanced-content');
      const chevron = document.getElementById('advanced-chevron');
      
      if (content.style.display === 'none' || !content.style.display) {
        content.style.display = 'block';
        chevron.className = 'fas fa-chevron-down';
      } else {
        content.style.display = 'none';
        chevron.className = 'fas fa-chevron-right';
      }
    }
    
    // Ejecutar compartición
    async function executeSharing() {
      if (!currentSharingItem || !selectedSharingOption) return;
      
      const shareBtn = document.getElementById('share-btn');
      shareBtn.disabled = true;
      shareBtn.textContent = 'Compartiendo...';
      
      try {
        let result;
        
        switch (selectedSharingOption) {
          case 'users':
            result = await shareWithUsers();
            break;
          case 'group':
            result = await shareWithGroup();
            break;
          case 'link':
            result = await shareWithLink();
            break;
        }
        
        if (result && result.success) {
          showSuccess('¡Compartido exitosamente!');
          closeSharingModal();
        } else {
          const errorMsg = result ? (result.error || 'Error desconocido al compartir') : 'Respuesta inválida del servidor';
          showError(errorMsg);
        }
        
      } catch (error) {
        console.error('Error en executeSharing:', error);
        showError('Error de conexión al compartir: ' + error.message);
      } finally {
        shareBtn.disabled = false;
        shareBtn.textContent = 'Compartir';
      }
    }
    
    // Compartir con usuarios específicos
    async function shareWithUsers() {
      if (userEmails.length === 0) {
        throw new Error('Debe agregar al menos un email');
      }
      
      const formData = new FormData();
      formData.append('resource_type', currentSharingItem.type === 'folder' ? 'carpeta' : 'archivo');
      formData.append('resource_id', currentSharingItem.id);
      formData.append('permission', document.getElementById('user-permission').value);
      formData.append('expiry_date', document.getElementById('user-expiry').value);
      formData.append('message', document.getElementById('user-message').value);
      formData.append('can_download', document.getElementById('can-download').checked ? 1 : 0);
      formData.append('can_print', document.getElementById('can-print').checked ? 1 : 0);
      formData.append('can_copy', document.getElementById('can-copy').checked ? 1 : 0);
      formData.append('notify_changes', document.getElementById('notify-changes').checked ? 1 : 0);
      
      // Agregar emails
      userEmails.forEach(email => {
        formData.append('user_emails[]', email);
      });
      
      const response = await fetch('/biblioteca/public/index.php/sharing/share-with-users', {
        method: 'POST',
        body: formData
      });
      
      return await response.json();
    }

    // Cargar grupos disponibles
    async function loadAvailableGroups() {
      try {
        const response = await fetch('/biblioteca/public/index.php/admin/groups/api', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'include'
        });
        
        if (!response.ok) {
          throw new Error('Error al cargar grupos');
        }
        
        const data = await response.json();
        const groupSelect = document.getElementById('group-select');
        
        // Limpiar opciones existentes
        groupSelect.innerHTML = '<option value="">Selecciona un grupo...</option>';
        
        // Agregar grupos
        if (data.groups && data.groups.length > 0) {
          data.groups.forEach(group => {
            const option = document.createElement('option');
            option.value = group.id;
            option.textContent = `${group.nombre} (${group.miembros_count || 0} miembros)`;
            groupSelect.appendChild(option);
          });
        } else {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = 'No hay grupos disponibles';
          option.disabled = true;
          groupSelect.appendChild(option);
        }
        
      } catch (error) {
        console.error('Error loading groups:', error);
        const groupSelect = document.getElementById('group-select');
        groupSelect.innerHTML = '<option value="">Error al cargar grupos</option>';
            }
    }

    // ===== FUNCIONES DE CUOTA DE ALMACENAMIENTO =====
    
    async function loadStorageQuota() {
      try {
        const response = await fetch('/biblioteca/public/index.php/drive/storage-quota', {
          method: 'GET',
          credentials: 'include'
        });
        
        if (response.ok) {
          const data = await response.json();
          updateStorageQuotaDisplay(data);
        } else {
          console.error('Error loading storage quota:', response.status);
          document.getElementById('quota-text').textContent = 'Error al cargar cuota';
        }
      } catch (error) {
        console.error('Error loading storage quota:', error);
        document.getElementById('quota-text').textContent = 'Error al cargar cuota';
      }
    }

    function updateStorageQuotaDisplay(quotaData) {
      const quotaFill = document.getElementById('quota-fill');
      const quotaText = document.getElementById('quota-text');
      
      const percentage = Math.min(100, Math.round((quotaData.used / quotaData.quota) * 100));
      
      // Actualizar barra de progreso
      quotaFill.style.width = percentage + '%';
      
      // Actualizar clase según el porcentaje
      quotaFill.className = 'quota-fill-sidebar';
      if (percentage >= 95) {
        quotaFill.classList.add('danger');
      } else if (percentage >= 80) {
        quotaFill.classList.add('warning');
      } else {
        quotaFill.classList.add('normal');
      }
      
      // Actualizar texto
      const usedFormatted = formatStorageBytes(quotaData.used);
      const quotaFormatted = formatStorageBytes(quotaData.quota);
      const availableFormatted = formatStorageBytes(quotaData.quota - quotaData.used);
      
      // Mostrar información más detallada adaptada al sidebar
      quotaText.innerHTML = `
        <div style="font-weight: 600; margin-bottom: 2px;">${usedFormatted} / ${quotaFormatted}</div>
        <div style="opacity: 0.8; font-size: 10px;">${percentage}% usado</div>
        <div style="opacity: 0.7; font-size: 10px;">${availableFormatted} libre</div>
      `;
      
      // Actualizar tooltip con información completa
      const quotaContainer = document.getElementById('storage-quota');
      let statusText = 'Normal';
      if (percentage >= 95) statusText = 'Límite alcanzado';
      else if (percentage >= 80) statusText = 'Cerca del límite';
      
      quotaContainer.title = `Estado: ${statusText}
Usado: ${usedFormatted} (${percentage}%)
Disponible: ${availableFormatted}
Cuota total: ${quotaFormatted}

${percentage >= 90 ? '⚠️ Considera eliminar archivos innecesarios o contacta al administrador para aumentar tu cuota.' : '✅ Tienes suficiente espacio disponible.'}`;

      // Mostrar notificación si está cerca del límite (solo una vez por sesión)
      if (percentage >= 90 && !sessionStorage.getItem('quota-warning-shown')) {
        showQuotaWarning(percentage, availableFormatted);
        sessionStorage.setItem('quota-warning-shown', 'true');
      }
    }

    function formatStorageBytes(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showQuotaWarning(percentage, availableFormatted) {
      const warningHtml = `
        <div style="position: fixed; top: 60px; right: 20px; z-index: 10000; 
                    background: linear-gradient(135deg, #ffc107, #fd7e14); 
                    color: #000; padding: 16px 20px; border-radius: 8px; 
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
                    max-width: 350px; font-size: 14px; 
                    border-left: 4px solid #dc3545;
                    animation: slideIn 0.5s ease-out;" id="quota-warning">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <i class="fas fa-exclamation-triangle" style="color: #dc3545; font-size: 18px;"></i>
            <strong>⚠️ Espacio de almacenamiento limitado</strong>
          </div>
          <div style="margin-bottom: 8px; opacity: 0.9;">
            Has usado el <strong>${percentage}%</strong> de tu cuota de almacenamiento.
            Solo tienes <strong>${availableFormatted}</strong> disponibles.
          </div>
          <div style="font-size: 12px; opacity: 0.8;">
            💡 Considera eliminar archivos innecesarios o contacta al administrador para aumentar tu cuota.
          </div>
          <button onclick="document.getElementById('quota-warning').remove()" 
                  style="position: absolute; top: 8px; right: 8px; 
                         background: none; border: none; font-size: 16px; 
                         cursor: pointer; color: #dc3545; font-weight: bold;">&times;</button>
        </div>
      `;
      
      document.body.insertAdjacentHTML('beforeend', warningHtml);
      
      // Auto-hide after 10 seconds
      setTimeout(() => {
        const warning = document.getElementById('quota-warning');
        if (warning) {
          warning.style.animation = 'slideOut 0.5s ease-in';
          setTimeout(() => warning.remove(), 500);
        }
      }, 10000);
    }

    // Compartir con grupo
    async function shareWithGroup() {
      const groupId = document.getElementById('group-select').value;
      if (!groupId) {
        throw new Error('Debe seleccionar un grupo');
      }
      
      const formData = new FormData();
      formData.append('resource_type', currentSharingItem.type === 'folder' ? 'carpeta' : 'archivo');
      formData.append('resource_id', currentSharingItem.id);
      formData.append('group_id', groupId);
      formData.append('permission', document.getElementById('group-permission').value);
      formData.append('expiry_date', document.getElementById('group-expiry').value);
      formData.append('can_download', document.getElementById('can-download').checked ? 1 : 0);
      formData.append('can_print', document.getElementById('can-print').checked ? 1 : 0);
      formData.append('can_copy', document.getElementById('can-copy').checked ? 1 : 0);
      formData.append('notify_changes', document.getElementById('notify-changes').checked ? 1 : 0);
      
      const response = await fetch('/biblioteca/public/index.php/sharing/share-with-group', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      });
      
      return await response.json();
    }
    
    // Compartir con enlace público
    async function shareWithLink() {
      const formData = new FormData();
      formData.append('resource_type', currentSharingItem.type === 'folder' ? 'carpeta' : 'archivo');
      formData.append('resource_id', currentSharingItem.id);
      formData.append('permission', document.getElementById('link-permission').value);
      formData.append('expiry_date', document.getElementById('link-expiry').value);
      formData.append('password', document.getElementById('link-password').value);
      formData.append('use_access_code', document.getElementById('use-access-code').checked ? 1 : 0);
      formData.append('allowed_domains', document.getElementById('link-domains').value);
      formData.append('can_download', document.getElementById('can-download').checked ? 1 : 0);
      formData.append('can_print', document.getElementById('can-print').checked ? 1 : 0);
      formData.append('can_copy', document.getElementById('can-copy').checked ? 1 : 0);
      formData.append('notify_access', document.getElementById('notify-access').checked ? 1 : 0);
      formData.append('requires_auth', document.getElementById('require-auth').checked ? 1 : 0);
      
      const response = await fetch('/biblioteca/public/index.php/sharing/create-public-link', {
        method: 'POST',
        body: formData
      });
      
      console.log('Response status:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      
      const result = await response.json();
      console.log('Share link result:', result);
      
      if (result.success) {
        // Guardar referencia al item antes de cerrar el modal
        const sharingItem = currentSharingItem;
        
        // Cerrar modal de compartición
        closeSharingModal();
        
        // Mostrar modal del enlace generado
        showLinkModal(result, sharingItem);
      }
      
      return result;
    }
    
    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(event) {
      const sharingModal = document.getElementById('sharing-modal');
      const linkModal = document.getElementById('link-modal-overlay');
      const deleteModal = document.getElementById('delete-modal-overlay');
      
      if (event.target === sharingModal) {
        closeSharingModal();
      }
      
      if (event.target === linkModal) {
        closeLinkModal();
      }
      
      if (event.target === deleteModal) {
        closeDeleteModal();
      }
    });

    // Upload con indicadores individuales
    async function handleFileUpload(e) {
      const files = e.target.files;
      if (!files.length) return;

      // Crear indicadores individuales para cada archivo
      for (const file of files) {
        const uploadId = 'upload_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        createUploadProgressCard(uploadId, file);
        
        try {
          await uploadFile(file, uploadId);
        } catch (error) {
          console.error('Upload error:', error);
          updateUploadProgressCard(uploadId, 'error', 0, 'Error al subir archivo');
        }
      }

      // Limpiar input
      e.target.value = '';
    }

    function uploadFile(file, uploadId) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        
        formData.append('_csrf', csrf);
        formData.append('folder_id', currentFolder);
        formData.append('file', file);

        // Actualizar estado inicial
        updateUploadProgressCard(uploadId, 'uploading', 0, 'Preparando archivo...');

        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            updateUploadProgressCard(uploadId, 'uploading', percent, `Subiendo... ${percent}%`);
          }
        });

        xhr.addEventListener('load', () => {
          console.log('Upload response status:', xhr.status);
          console.log('Upload response text:', xhr.responseText);
          
          if (xhr.status === 200) {
            try {
              // Check if response is actually JSON
              if (!xhr.responseText.trim().startsWith('{') && !xhr.responseText.trim().startsWith('[')) {
                console.error('Response is not JSON:', xhr.responseText);
                updateUploadProgressCard(uploadId, 'error', 0, 'Respuesta no válida del servidor');
                reject(new Error('Respuesta no válida del servidor'));
                return;
              }
              
              const data = JSON.parse(xhr.responseText);
              if (data.ok) {
                // Actualizar indicador de cuota después de subir archivo
                loadStorageQuota();
                
                // Actualizar tarjeta de progreso a éxito
                updateUploadProgressCard(uploadId, 'success', 100, 'Archivo subido exitosamente');
                
                const icon = getFileIcon(file.type);
                const gridItem = createGridItem('file', data.id, file.name, icon, null, null, null);
                const listItem = createListItem('file', data.id, file.name, file.type || 'Archivo', '', '', null, null, null);
                
                // Añadir a la vista actual
                if (currentView === 'grid') {
                  document.getElementById('grid-view').appendChild(gridItem);
                } else if (currentView === 'list') {
                  document.getElementById('list-content').appendChild(listItem);
                }
                
                console.log('New file added:', file.name, 'ID:', data.id);
                
                // Auto-remover la tarjeta después de 3 segundos
                setTimeout(() => {
                  removeUploadProgressCard(uploadId);
                }, 3000);
              } else {
                updateUploadProgressCard(uploadId, 'error', 0, data.error || 'Error al subir archivo');
              }
              resolve(data);
            } catch (e) {
              console.error('JSON parse error:', e);
              console.error('Response text:', xhr.responseText);
              updateUploadProgressCard(uploadId, 'error', 0, 'Error al procesar respuesta del servidor');
              reject(new Error('Error al procesar respuesta del servidor'));
            }
          } else if (xhr.status === 419) {
            // Error de CSRF token - renovar y reintentar
            console.warn('CSRF token expirado, renovando...');
            updateUploadProgressCard(uploadId, 'uploading', 0, 'Renovando token de seguridad...');
            
            // Renovar CSRF token
            fetch('/biblioteca/public/index.php/drive/dashboard')
              .then(response => response.text())
              .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newCsrf = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (newCsrf) {
                  csrf = newCsrf;
                  console.log('CSRF token renovado, reintentando upload...');
                  // Reintentar el upload con el nuevo token
                  uploadFileWithRetry(file, uploadId, 1);
                } else {
                  updateUploadProgressCard(uploadId, 'error', 0, 'Error: No se pudo renovar el token de seguridad');
                  reject(new Error('No se pudo renovar CSRF token'));
                }
              })
              .catch(error => {
                console.error('Error renovando CSRF token:', error);
                updateUploadProgressCard(uploadId, 'error', 0, 'Error: No se pudo renovar el token de seguridad');
                reject(error);
              });
          } else {
            console.error('HTTP error:', xhr.status, xhr.responseText);
            updateUploadProgressCard(uploadId, 'error', 0, `Error HTTP: ${xhr.status}`);
            reject(new Error('Error HTTP: ' + xhr.status));
          }
        });

        xhr.addEventListener('error', () => {
          updateUploadProgressCard(uploadId, 'error', 0, 'Error de conexión');
          reject(new Error('Network error'));
        });

        xhr.open('POST', '/biblioteca/public/index.php/drive/upload');
        xhr.send(formData);
      });
    }

    // Función para reintentar upload después de renovar CSRF token
    function uploadFileWithRetry(file, uploadId, retryCount = 1) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        
        formData.append('_csrf', csrf);
        formData.append('folder_id', currentFolder);
        formData.append('file', file);

        updateUploadProgressCard(uploadId, 'uploading', 0, `Reintentando subida... (${retryCount}/2)`);

        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            updateUploadProgressCard(uploadId, 'uploading', percent, `Subiendo... ${percent}%`);
          }
        });

        xhr.addEventListener('load', () => {
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              if (data.ok) {
                loadStorageQuota();
                updateUploadProgressCard(uploadId, 'success', 100, 'Archivo subido exitosamente');
                
                const icon = getFileIcon(file.type);
                const gridItem = createGridItem('file', data.id, file.name, icon, null, null, null);
                const listItem = createListItem('file', data.id, file.name, file.type || 'Archivo', '', '', null, null, null);
                
                if (currentView === 'grid') {
                  document.getElementById('grid-view').appendChild(gridItem);
                } else if (currentView === 'list') {
                  document.getElementById('list-content').appendChild(listItem);
                }
                
                setTimeout(() => {
                  removeUploadProgressCard(uploadId);
                }, 3000);
                
                resolve(data);
              } else {
                updateUploadProgressCard(uploadId, 'error', 0, data.error || 'Error al subir archivo');
                reject(new Error(data.error || 'Error al subir archivo'));
              }
            } catch (e) {
              updateUploadProgressCard(uploadId, 'error', 0, 'Error al procesar respuesta del servidor');
              reject(new Error('Error al procesar respuesta del servidor'));
            }
          } else if (xhr.status === 419 && retryCount < 2) {
            // Segundo intento fallido, dar por perdido
            updateUploadProgressCard(uploadId, 'error', 0, 'Token de seguridad expirado. Por favor, recarga la página.');
            reject(new Error('CSRF token expirado después de reintento'));
          } else {
            updateUploadProgressCard(uploadId, 'error', 0, `Error HTTP: ${xhr.status}`);
            reject(new Error('Error HTTP: ' + xhr.status));
          }
        });

        xhr.addEventListener('error', () => {
          updateUploadProgressCard(uploadId, 'error', 0, 'Error de conexión');
          reject(new Error('Network error'));
        });

        xhr.open('POST', '/biblioteca/public/index.php/drive/upload');
        xhr.send(formData);
      });
    }

    // Funciones auxiliares para las tarjetas de progreso
    function createUploadProgressCard(uploadId, file) {
      const container = document.getElementById('upload-progress-container');
      const fileIcon = getFileIcon(file.type);
      
      const card = document.createElement('div');
      card.className = 'upload-progress-card uploading';
      card.id = uploadId;
      
      card.innerHTML = `
        <div class="upload-progress-icon">
          <i class="${fileIcon}"></i>
        </div>
        <div class="upload-progress-content">
          <div class="upload-progress-filename">${file.name}</div>
          <div class="upload-progress-status">Preparando archivo...</div>
          <div class="upload-progress-bar-container">
            <div class="upload-progress-bar-individual" style="width: 0%"></div>
          </div>
          <div class="upload-progress-percentage">0%</div>
        </div>
        <button class="upload-progress-close" onclick="removeUploadProgressCard('${uploadId}')">
          <i class="fas fa-times"></i>
        </button>
      `;
      
      container.appendChild(card);
    }

    function updateUploadProgressCard(uploadId, status, percentage, message) {
      const card = document.getElementById(uploadId);
      if (!card) return;
      
      // Actualizar clase de estado
      card.className = `upload-progress-card ${status}`;
      
      // Actualizar elementos
      const statusElement = card.querySelector('.upload-progress-status');
      const barElement = card.querySelector('.upload-progress-bar-individual');
      const percentageElement = card.querySelector('.upload-progress-percentage');
      const iconElement = card.querySelector('.upload-progress-icon i');
      
      if (statusElement) statusElement.textContent = message;
      if (barElement) barElement.style.width = percentage + '%';
      if (percentageElement) percentageElement.textContent = percentage + '%';
      
      // Cambiar icono según estado
      if (iconElement) {
        if (status === 'success') {
          iconElement.className = 'fas fa-check';
        } else if (status === 'error') {
          iconElement.className = 'fas fa-exclamation-triangle';
        }
      }
    }

    function removeUploadProgressCard(uploadId) {
      const card = document.getElementById(uploadId);
      if (!card) return;
      
      card.classList.add('removing');
      setTimeout(() => {
        if (card.parentNode) {
          card.parentNode.removeChild(card);
        }
      }, 300);
    }

    // View Toggle
    function setView(view) {
      console.log('Setting view to:', view);
      currentView = view;
      
      // Update button states
      document.getElementById('view-grid').classList.toggle('active', view === 'grid');
      document.getElementById('view-list').classList.toggle('active', view === 'list');
      document.getElementById('view-columns').classList.toggle('active', view === 'columns');
      
      // Show/hide views
      document.getElementById('grid-view').style.display = view === 'grid' ? 'grid' : 'none';
      document.getElementById('list-view').classList.toggle('active', view === 'list');
      document.getElementById('column-view').classList.toggle('active', view === 'columns');
      
      // Show trash bin in all views
      const trashBin = document.getElementById('trash-bin');
      trashBin.style.display = 'flex'; // Siempre visible en todas las vistas
      
      console.log('Column view display:', document.getElementById('column-view').style.display);
      console.log('Column view classes:', document.getElementById('column-view').className);
      
      // Initialize column view if selected
      if (view === 'columns') {
        setTimeout(() => {
          if (!isSharedWithMeView) {
            initializeColumnView();
          } else {
            // Show message for shared with me in column view
            showColumnViewMessage('La vista de columnas no está disponible para "Compartido conmigo".<br>Usa las vistas de cuadrícula o lista.');
          }
        }, 100);
      }
    }

    // Context Menu
    function handleContextMenu(e) {
      e.preventDefault();
      
      const item = e.target.closest('.grid-item, .list-item, .column-item');
      if (!item) return;

      contextTarget = item;
      
      if (!selectedItems.has(item)) {
        clearSelection();
        selectItem(item);
        updateToolbar();
      }

      // Show/hide label and icon options based on item type
      const labelMenuItem = document.getElementById('label-menu-item');
      const iconMenuItem = document.getElementById('icon-menu-item');
      const isFolder = item.dataset.type === 'folder';
      labelMenuItem.style.display = isFolder ? 'block' : 'none';
      iconMenuItem.style.display = isFolder ? 'block' : 'none';

      const menu = document.getElementById('context-menu');
      menu.style.display = 'block';
      menu.style.left = e.pageX + 'px';
      menu.style.top = e.pageY + 'px';
    }

    function hideContextMenus() {
      document.getElementById('context-menu').style.display = 'none';
      const emptyMenu = document.getElementById('empty-context-menu');
      if (emptyMenu) emptyMenu.style.display = 'none';
      contextTarget = null;
    }

    // Background changer
    function openBackgroundPicker() {
      const input = document.getElementById('bg-input');
      if (!input) return;
      input.value = '';
      input.click();
    }

    document.addEventListener('DOMContentLoaded', () => {
      const bgInput = document.getElementById('bg-input');
      const bgColorInput = document.getElementById('bg-color-input');
      
      if (bgInput) {
        bgInput.addEventListener('change', async (e) => {
          const file = e.target.files && e.target.files[0];
          if (!file) return;
          
          // Validar tamaño del archivo antes de procesar
          if (file.size > 2 * 1024 * 1024) { // 2MB
            showError('La imagen es demasiado grande. Máximo 2MB.');
            return;
          }
          
          const reader = new FileReader();
          reader.onload = async () => {
            try {
              const dataUrl = reader.result;
              
              // Comprimir imagen si es muy grande
              const compressedDataUrl = await compressImage(dataUrl, 0.7, 1920, 1080);
              
              // Aplicar inmediatamente para feedback visual
              applyExplorerBackground(compressedDataUrl);
              
              // Guardar en servidor
              await saveBackgroundImage(compressedDataUrl);
              
            } catch (error) {
              console.error('Error processing image:', error);
              showError('Error al procesar la imagen');
            }
          };
          reader.readAsDataURL(file);
        });
      }
      
      if (bgColorInput) {
        bgColorInput.addEventListener('change', async (e) => {
          const color = e.target.value;
          applyExplorerBackgroundColor(color);
          await saveBackgroundColor(color);
        });
      }
      
      // Load user background settings from PHP
      const backgroundSettings = <?= json_encode($backgroundSettings ?? []) ?>;
      console.log('Loading background settings:', backgroundSettings);
      loadUserBackground(backgroundSettings);
    });

    function applyExplorerBackground(dataUrl) {
      const explorer = document.getElementById('explorer');
      if (!explorer) return;
      explorer.style.backgroundImage = `url('${dataUrl}')`;
      explorer.style.backgroundSize = 'cover';
      explorer.style.backgroundPosition = 'center center';
      explorer.style.backgroundRepeat = 'no-repeat';
      explorer.style.backgroundColor = '';
      
      // For images, assume average contrast and use light text
      updateNameChipContrast('light');
    }

    function clearExplorerBackground() {
      const explorer = document.getElementById('explorer');
      if (!explorer) return;
      explorer.style.backgroundImage = '';
      explorer.style.backgroundSize = '';
      explorer.style.backgroundPosition = '';
      explorer.style.backgroundRepeat = '';
      explorer.style.backgroundColor = '';
      hideContextMenus();
      
      // Reset to default contrast
      updateNameChipContrast('auto');
      
      // Clear from database
      clearBackgroundFromServer();
    }

    function openBackgroundSolidPicker() {
      const input = document.getElementById('bg-color-input');
      if (!input) return;
      hideContextMenus();
      
      // Make input temporarily visible at cursor position
      input.className = 'temp-color-picker';
      input.style.left = (window.lastRightClickX || 100) + 'px';
      input.style.top = (window.lastRightClickY || 100) + 'px';
      
      // Focus and click
      input.focus();
      input.click();
      
      // Hide after a delay
      setTimeout(() => {
        input.className = 'visually-hidden-input';
      }, 100);
    }

    function applyExplorerBackgroundColor(color) {
      const explorer = document.getElementById('explorer');
      if (!explorer) return;
      explorer.style.backgroundImage = '';
      explorer.style.backgroundColor = color;
      
      // Calculate contrast and update text colors
      const contrastType = getContrastType(color);
      updateNameChipContrast(contrastType);
    }
    
    // Calculate if color is light or dark for contrast
    function getContrastType(hexColor) {
      if (!hexColor || hexColor === 'transparent') return 'auto';
      
      // Convert hex to RGB
      const hex = hexColor.replace('#', '');
      const r = parseInt(hex.substr(0, 2), 16);
      const g = parseInt(hex.substr(2, 2), 16);
      const b = parseInt(hex.substr(4, 2), 16);
      
      // Calculate luminance (0-255)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b);
      
      // If luminance > 128, it's a light color, use dark text
      return luminance > 128 ? 'dark' : 'light';
    }
    
    // Update all name chips with appropriate contrast class
    function updateNameChipContrast(contrastType) {
      const nameChips = document.querySelectorAll('.name-chip');
      nameChips.forEach(chip => {
        chip.classList.remove('contrast-dark', 'contrast-light');
        if (contrastType === 'dark') {
          chip.classList.add('contrast-dark');
        } else if (contrastType === 'light') {
          chip.classList.add('contrast-light');
        }
        // 'auto' leaves default styling
      });
      
      // Store current contrast type for new items
      window.currentContrastType = contrastType;
    }
    
    // Apply current contrast to newly created items
    function applyCurrentContrastToNewItems() {
      if (window.currentContrastType && window.currentContrastType !== 'auto') {
        setTimeout(() => {
          updateNameChipContrast(window.currentContrastType);
        }, 50);
      }
    }

    // Background management functions
    function loadUserBackground(settings) {
      if (!settings) {
        console.log('No background settings provided');
        return;
      }
      
      console.log('Background type:', settings.background_type);
      
      switch (settings.background_type) {
        case 'color':
          if (settings.background_color) {
            console.log('Applying saved color:', settings.background_color);
            applyExplorerBackgroundColor(settings.background_color);
          }
          break;
        case 'image':
          if (settings.background_image) {
            console.log('Applying saved image (length):', settings.background_image.length);
            applyExplorerBackground(settings.background_image);
          }
          break;
        default:
          console.log('Using default background');
          // Default background, no action needed
          break;
      }
    }

    async function saveBackgroundColor(color) {
      try {
        const formData = new URLSearchParams();
        formData.append('_csrf', csrf);
        formData.append('color', color);

        const response = await fetch('/biblioteca/public/index.php/drive/background/color', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString()
        });

        const result = await response.json();
        if (!result.ok) {
          console.error('Error saving background color:', result.error);
          if (result.detail) {
            console.error('Error detail:', result.detail);
          }
        }
      } catch (error) {
        console.error('Network error saving background color:', error);
      }
    }

    async function saveBackgroundImage(dataUrl) {
      try {
        const formData = new URLSearchParams();
        formData.append('_csrf', csrf);
        formData.append('image_data', dataUrl);

        const response = await fetch('/biblioteca/public/index.php/drive/background/image', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString()
        });

        const result = await response.json();
        if (result.ok) {
          console.log('Background image saved successfully');
        } else {
          console.error('Error saving background image:', result.error);
          if (result.detail) {
            console.error('Error detail:', result.detail);
          }
          showError('Error al guardar imagen: ' + (result.detail || result.error));
          // Revert background on error
          clearExplorerBackground();
        }
      } catch (error) {
        console.error('Network error saving background image:', error);
        showError('Error de red al guardar imagen');
        clearExplorerBackground();
      }
    }

    async function clearBackgroundFromServer() {
      try {
        const formData = new URLSearchParams();
        formData.append('_csrf', csrf);

        const response = await fetch('/biblioteca/public/index.php/drive/background/clear', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString()
        });

        const result = await response.json();
        if (!result.ok) {
          console.error('Error clearing background:', result.error);
          if (result.detail) {
            console.error('Error detail:', result.detail);
          }
        }
      } catch (error) {
        console.error('Network error clearing background:', error);
      }
    }

    // Image compression function
    function compressImage(dataUrl, quality = 0.7, maxWidth = 1920, maxHeight = 1080) {
      return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          
          // Calculate new dimensions
          let { width, height } = img;
          if (width > maxWidth) {
            height = (height * maxWidth) / width;
            width = maxWidth;
          }
          if (height > maxHeight) {
            width = (width * maxHeight) / height;
            height = maxHeight;
          }
          
          canvas.width = width;
          canvas.height = height;
          
          // Draw and compress
          ctx.drawImage(img, 0, 0, width, height);
          const compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
          
          console.log('Image compressed:', {
            original: dataUrl.length,
            compressed: compressedDataUrl.length,
            ratio: (compressedDataUrl.length / dataUrl.length * 100).toFixed(1) + '%'
          });
          
          resolve(compressedDataUrl);
        };
        img.src = dataUrl;
      });
    }

    // Keyboard Shortcuts
    function handleKeyboard(e) {
      if (e.target.tagName === 'INPUT') return;

      switch (e.key) {
        case 'Delete':
          e.preventDefault();
          deleteSelected();
          break;
        case 'F2':
          e.preventDefault();
          renameSelected();
          break;
        case 'Enter':
          e.preventDefault();
          openSelected();
          break;
        case 'Escape':
          clearSelection();
          hideContextMenu();
          break;
        case 'a':
          if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            document.querySelectorAll('.grid-item, .list-item').forEach(selectItem);
            updateToolbar();
          }
          break;
      }
    }

    // Admin Menu Functions
    function toggleAdminMenu() {
      const dropdown = document.getElementById('adminDropdown');
      dropdown.classList.toggle('show');
    }
    
    function showSystemInfo() {
      openSystemModal('Versión: 1.0.0\nPHP: <?= PHP_VERSION ?>\nUsuarios activos: <?= $uid ?>', 'info', 'Información del Sistema');
    }
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
      const adminMenu = event.target.closest('.admin-menu');
      if (!adminMenu) {
        const dropdown = document.getElementById('adminDropdown');
        if (dropdown) {
          dropdown.classList.remove('show');
        }
      }
    });

    // Logout
    async function logout() {
      try {
        const formData = new URLSearchParams();
        formData.append('_csrf', csrf);
        
        await fetch('/biblioteca/public/index.php/auth/logout', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData
        });
        
        location.href = '/biblioteca/public/index.php/';
      } catch (error) {
        location.href = '/biblioteca/public/index.php/';
      }
    }

    // Label System
    function setupColorPicker() {
      document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', () => {
          document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
          option.classList.add('selected');
          selectedColor = option.dataset.color;
        });
      });
    }

    function setLabelSelected() {
      if (selectedItems.size !== 1) return;
      
      const item = Array.from(selectedItems)[0];
      if (item.dataset.type !== 'folder') return;

      // Show label modal
      document.getElementById('modal-title').textContent = 'Establecer Etiqueta';
      document.getElementById('modal-input').style.display = 'none';
      document.getElementById('label-modal-content').style.display = 'block';
      document.getElementById('modal-overlay').style.display = 'flex';
      
      // Reset form
      document.getElementById('label-input').value = '';
      selectedColor = '';
      document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
      
      modalCallback = async (data) => {
        const etiqueta = document.getElementById('label-input').value.trim();
        const color = selectedColor;
        
        if (!etiqueta && !color) {
          showInfo('Selecciona una etiqueta o color');
          return;
        }

        try {
          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('id', item.dataset.id);
          formData.append('etiqueta', etiqueta);
          formData.append('color', color);

          const response = await fetch('/biblioteca/public/index.php/drive/folder/label', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          });

          const result = await response.json();
          if (result.ok) {
            // Update item in DOM
            updateItemLabel(item, etiqueta, color);
            console.log('Label updated:', etiqueta, color);
          } else {
            showError(result.error || 'Error al establecer etiqueta');
          }
        } catch (error) {
          console.error('Label error:', error);
          showError('Error al establecer etiqueta');
        }
      };
    }

    function updateItemLabel(item, label, color) {
      // Remove existing label
      const existingLabel = item.querySelector('.label');
      if (existingLabel) {
        existingLabel.remove();
      }

      // Update folder icon color
      if (item.dataset.type === 'folder') {
        const icon = item.querySelector('.icon i');
        if (icon && color) {
          icon.style.color = color;
          item.classList.add('folder-colored');
        } else if (icon) {
          // Default to light gray when no color
          icon.style.color = '#9ca3af';
          item.classList.remove('folder-colored');
        }
      }

      // Add new label if provided
      if (label && color) {
        const labelEl = document.createElement('div');
        labelEl.className = 'label';
        labelEl.style.backgroundColor = color;
        labelEl.textContent = label;
        
        if (item.classList.contains('grid-item')) {
          item.appendChild(labelEl);
        } else {
          const nameDiv = item.querySelector('.name');
          if (nameDiv) {
            const labelSpan = document.createElement('span');
            labelSpan.className = 'label';
            labelSpan.style.backgroundColor = color;
            labelSpan.textContent = label;
            nameDiv.appendChild(labelSpan);
          }
        }
      }
    }

    // Override confirm modal for different modal types
    function confirmModal() {
      const isLabelModal = document.getElementById('label-modal-content').style.display !== 'none';
      const isIconModal = document.getElementById('icon-modal-content').style.display !== 'none';
      
      if (isLabelModal || isIconModal) {
        if (modalCallback) {
          modalCallback();
        }
      } else {
        const value = document.getElementById('modal-input').value.trim();
        if (value && modalCallback) {
          modalCallback(value);
        }
      }
      closeModal();
    }

    // Override close modal
    function closeModal() {
      document.getElementById('modal-overlay').style.display = 'none';
      document.getElementById('modal-input').style.display = 'block';
      document.getElementById('label-modal-content').style.display = 'none';
      document.getElementById('icon-modal-content').style.display = 'none';
      modalCallback = null;
      selectedColor = '';
      selectedIcon = '';
    }

    // Icon Picker System
    function setupIconPicker() {
      document.querySelectorAll('.icon-option').forEach(option => {
        option.addEventListener('click', () => {
          document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
          option.classList.add('selected');
          selectedIcon = option.dataset.icon;
        });
      });
    }

    function setIconSelected() {
      if (selectedItems.size !== 1) return;
      
      const item = Array.from(selectedItems)[0];
      if (item.dataset.type !== 'folder') return;

      // Show icon modal
      document.getElementById('modal-title').textContent = 'Seleccionar Icono';
      document.getElementById('modal-input').style.display = 'none';
      document.getElementById('label-modal-content').style.display = 'none';
      document.getElementById('icon-modal-content').style.display = 'block';
      document.getElementById('modal-overlay').style.display = 'flex';
      
      // Reset form
      selectedIcon = '';
      document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
      
      modalCallback = async (data) => {
        if (!selectedIcon && selectedIcon !== '') {
          showInfo('Selecciona un icono');
          return;
        }

        try {
          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('id', item.dataset.id);
          formData.append('icono', selectedIcon);

          const response = await fetch('/biblioteca/public/index.php/drive/folder/icon', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
          });

          const result = await response.json();
          if (result.ok) {
            // Update item icon in DOM
            updateItemIcon(item, selectedIcon);
            console.log('Icon updated:', selectedIcon);
            console.log('Item after update:', item.innerHTML);
          } else {
            console.error('Error response:', result);
            showError(result.error || 'Error al establecer icono');
          }
        } catch (error) {
          console.error('Icon error:', error);
          showError('Error al establecer icono');
        }
      };
    }

    function updateItemIcon(item, iconClass) {
      console.log('Updating icon for item:', item, 'with class:', iconClass);
      
      const iconContainer = item.querySelector('.icon');
      if (!iconContainer) {
        console.error('Icon container not found');
        return;
      }

      if (iconClass) {
        // Add custom icon
        iconContainer.classList.add('has-custom');
        
        // Remove existing custom icon
        const existingCustom = iconContainer.querySelector('.custom-icon');
        if (existingCustom) {
          existingCustom.remove();
        }

        // Make base icon semi-transparent
        const baseIcon = iconContainer.querySelector('i:first-child');
        if (baseIcon) {
          baseIcon.style.opacity = '0.3';
        }

        // Add new custom icon
        const customIcon = document.createElement('i');
        customIcon.className = iconClass + ' custom-icon';
        customIcon.style.position = 'absolute';
        customIcon.style.top = '50%';
        customIcon.style.left = '50%';
        customIcon.style.transform = 'translate(-50%, -50%)';
        customIcon.style.fontSize = '32px';
        customIcon.style.zIndex = '2';
        customIcon.style.color = '#007acc';
        
        iconContainer.style.position = 'relative';
        iconContainer.appendChild(customIcon);
        
        console.log('Custom icon added:', customIcon);
      } else {
        // Remove custom icon
        iconContainer.classList.remove('has-custom');
        const customIcon = iconContainer.querySelector('.custom-icon');
        if (customIcon) {
          customIcon.remove();
        }
        const baseIcon = iconContainer.querySelector('i:first-child');
        if (baseIcon) {
          baseIcon.style.opacity = '';
        }
        iconContainer.style.position = '';
      }
    }

    // Trash Bin System
    function setupTrashBin() {
      const trashBin = document.getElementById('trash-bin');
      
      // Click para eliminar elementos seleccionados
      trashBin.addEventListener('click', () => {
        if (selectedItems.size > 0) {
          deleteSelected();
        } else {
          showToast('Selecciona archivos o carpetas para eliminar', 'info');
        }
      });
      
      trashBin.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (draggedItem) {
          trashBin.classList.add('drag-over');
        }
      });

      trashBin.addEventListener('dragleave', () => {
        trashBin.classList.remove('drag-over');
      });

      trashBin.addEventListener('drop', (e) => {
        e.preventDefault();
        trashBin.classList.remove('drag-over');
        
        if (draggedItem) {
          if (selectedItems.size > 1) {
            showMultipleDeleteConfirmation(Array.from(selectedItems));
          } else {
            showDeleteConfirmation(draggedItem);
          }
        }
      });
    }

    function showDeleteConfirmation(item) {
      const itemName = getItemName(item);
      const itemType = item.dataset.type === 'folder' ? 'carpeta' : 'archivo';
      
      openDeleteModal(itemName, itemType, deleteItem, item);
    }

    function showMultipleDeleteConfirmation(items) {
      const count = items.length;
      const folders = items.filter(item => item.dataset.type === 'folder').length;
      const files = items.filter(item => item.dataset.type === 'file').length;
      
      let message = `¿Estás seguro de que quieres eliminar ${count} elementos?`;
      if (folders > 0 && files > 0) {
        message = `¿Eliminar ${folders} carpeta(s) y ${files} archivo(s)?`;
      } else if (folders > 0) {
        message = `¿Eliminar ${folders} carpeta(s)?`;
      } else if (files > 0) {
        message = `¿Eliminar ${files} archivo(s)?`;
      }
      
      if (confirm(message)) {
        deleteMultipleItems(items);
      }
    }

    async function deleteMultipleItems(items) {
      try {
        for (const item of items) {
          await deleteItem(item);
        }
        console.log('Multiple items deleted successfully');
      } catch (error) {
        console.error('Error deleting multiple items:', error);
      }
    }

    async function deleteItem(item) {
      try {
        const type = item.dataset.type;
        const id = item.dataset.id;
        const endpoint = type === 'folder' ? '/drive/folder/delete' : '/drive/file/delete';
        
        const formData = new URLSearchParams();
        formData.append('_csrf', csrf);
        formData.append('id', id);

        const response = await fetch('/biblioteca/public/index.php' + endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData
        });

        const result = await response.json();
        if (result.ok) {
          item.remove();
          selectedItems.delete(item);
          updateToolbar();
          console.log('Item deleted successfully');
        } else {
          showError(result.error || 'Error al eliminar');
        }
      } catch (error) {
        console.error('Delete error:', error);
        showError('Error al eliminar');
      }
    }

    function getItemName(item) {
      const nameEl = item.querySelector('.name .name-chip') || 
                    item.querySelector('.name span:last-child') || 
                    item.querySelector('.name > span');
      return nameEl ? nameEl.textContent.trim() : 'Elemento';
    }

    // Mouse Selection System
    function setupMouseSelection() {
      selectionBox = document.getElementById('selection-box');
      
      // Add selection listeners to specific view containers
      document.getElementById('grid-view').addEventListener('mousedown', startSelection);
      document.getElementById('list-content').addEventListener('mousedown', startSelection);
      document.addEventListener('mousemove', updateSelection);
      document.addEventListener('mouseup', endSelection);
    }

    function startSelection(e) {
      // Don't start selection on items
      if (e.target.closest('.grid-item, .list-item, .column-item')) {
        return;
      }
      
      // Don't start selection if it's a right click
      if (e.button !== 0) return;
      
      e.preventDefault();
      isSelecting = true;
      document.body.classList.add('selecting');
      
      // Store absolute position for selection
      selectionStart.x = e.clientX;
      selectionStart.y = e.clientY;
      
      // Position the selection box relative to explorer
      const explorerRect = document.getElementById('explorer').getBoundingClientRect();
      const boxLeft = e.clientX - explorerRect.left;
      const boxTop = e.clientY - explorerRect.top;
      
      selectionBox.style.left = boxLeft + 'px';
      selectionBox.style.top = boxTop + 'px';
      selectionBox.style.width = '0px';
      selectionBox.style.height = '0px';
      selectionBox.style.display = 'block';
      
      // Save initial selection state
      initialSelection = new Set(selectedItems);
      
      // Clear current selection if not holding Ctrl
      if (!e.ctrlKey && !e.metaKey) {
        clearSelection();
        initialSelection.clear();
      }
    }

    function updateSelection(e) {
      if (!isSelecting) return;
      
      e.preventDefault();
      
      const explorerRect = document.getElementById('explorer').getBoundingClientRect();
      
      // Calculate box position relative to explorer
      const startX = selectionStart.x - explorerRect.left;
      const startY = selectionStart.y - explorerRect.top;
      const currentX = e.clientX - explorerRect.left;
      const currentY = e.clientY - explorerRect.top;
      
      const left = Math.min(startX, currentX);
      const top = Math.min(startY, currentY);
      const width = Math.abs(currentX - startX);
      const height = Math.abs(currentY - startY);
      
      selectionBox.style.left = left + 'px';
      selectionBox.style.top = top + 'px';
      selectionBox.style.width = width + 'px';
      selectionBox.style.height = height + 'px';
      
      // Check which items intersect with selection box
      selectItemsInBox(left, top, width, height);
    }

    function endSelection(e) {
      if (!isSelecting) return;
      
      isSelecting = false;
      document.body.classList.remove('selecting');
      selectionBox.style.display = 'none';
      
      console.log('Selection ended. Selected items:', selectedItems.size);
      selectedItems.forEach(item => {
        console.log('- Item selected:', item.dataset.id, item.classList.contains('selected'));
      });
      
      updateToolbar();
    }

    function selectItemsInBox(boxLeft, boxTop, boxWidth, boxHeight) {
      // Don't process tiny selections (like a click)
      if (boxWidth < 3 && boxHeight < 3) {
        console.log('Box too small, skipping selection');
        return;
      }
      
      const items = currentView === 'grid' ? 
        document.querySelectorAll('#grid-view .grid-item') : 
        currentView === 'list' ?
        document.querySelectorAll('#list-content .list-item') :
        document.querySelectorAll('#column-view .column-item');
      
      if (items.length === 0) {
        console.log('No items found to select');
        return;
      }
      
      const explorerRect = document.getElementById('explorer').getBoundingClientRect();
      const boxRight = boxLeft + boxWidth;
      const boxBottom = boxTop + boxHeight;
      
      console.log('Selection box:', { left: boxLeft, top: boxTop, right: boxRight, bottom: boxBottom });
      
      // Start fresh for this drag operation
      if (!window.event?.ctrlKey && !window.event?.metaKey) {
        // Clear all selections if not holding Ctrl
        items.forEach(item => {
          item.classList.remove('selected');
        });
        selectedItems.clear();
      }
      
      // Check each item for intersection
      items.forEach(item => {
        const itemRect = item.getBoundingClientRect();
        const itemLeft = itemRect.left - explorerRect.left;
        const itemTop = itemRect.top - explorerRect.top;
        const itemRight = itemLeft + itemRect.width;
        const itemBottom = itemTop + itemRect.height;
        
        // Check if item intersects with selection box
        const intersects = !(itemRight < boxLeft || 
                           itemLeft > boxRight || 
                           itemBottom < boxTop || 
                           itemTop > boxBottom);
        
        if (intersects) {
          // Add to selection
          item.classList.add('selected');
          selectedItems.add(item);
          console.log('Item intersects:', item.dataset.id);
        }
      });
      
      console.log('Total selected:', selectedItems.size);
    }

    function showMultipleMoveConfirmation(items, targetItem) {
      moveSourceItem = items; // Array of items
      moveTargetItem = targetItem;

      const count = items.length;
      const targetName = getItemName(targetItem);

      document.getElementById('move-source').innerHTML = 
        `<i class="fas fa-copy"></i><span>${count} elementos seleccionados</span>`;
      document.getElementById('move-target').innerHTML = 
        `<i class="fas fa-folder"></i><span>${targetName}</span>`;

      document.getElementById('move-modal-overlay').style.display = 'flex';
    }

    // Drag & Drop System
    function setupDragAndDrop(item) {
      if (!item || !item.dataset) {
        console.warn('Invalid item for drag & drop setup');
        return;
      }

      // Make items draggable
      item.draggable = true;
      
      item.addEventListener('dragstart', (e) => {
        console.log('Drag start:', item.dataset.type, item.dataset.id);
        
        // If item is not selected, select it
        if (!selectedItems.has(item)) {
          clearSelection();
          selectItem(item);
        }
        
        draggedItem = item;
        
        // Add dragging class to all selected items
        selectedItems.forEach(selectedItem => {
          selectedItem.classList.add('dragging');
        });
        
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', item.dataset.id);
        
        // Store selected items count for visual feedback
        e.dataTransfer.setData('text/count', selectedItems.size.toString());
      });

      item.addEventListener('dragend', () => {
        console.log('Drag end');
        
        // Remove dragging class from all selected items
        selectedItems.forEach(selectedItem => {
          selectedItem.classList.remove('dragging');
        });
        
        // Clear all drag states
        document.querySelectorAll('.drag-over, .drag-target').forEach(el => {
          el.classList.remove('drag-over', 'drag-target');
        });
        draggedItem = null;
      });

      // Only folders can be drop targets
      if (item.dataset.type === 'folder') {
        item.addEventListener('dragover', (e) => {
          e.preventDefault();
          if (draggedItem && draggedItem !== item && draggedItem.dataset.id !== item.dataset.id) {
            item.classList.add('drag-over');
          }
        });

        item.addEventListener('dragleave', () => {
          item.classList.remove('drag-over');
        });

        item.addEventListener('drop', (e) => {
          e.preventDefault();
          item.classList.remove('drag-over');
          
          console.log('Drop event - Dragged:', draggedItem?.dataset.id, 'Target:', item.dataset.id);
          
          if (draggedItem && draggedItem !== item && draggedItem.dataset.id !== item.dataset.id) {
            // Show confirmation for multiple items if more than one selected
            if (selectedItems.size > 1) {
              showMultipleMoveConfirmation(Array.from(selectedItems), item);
            } else {
              showMoveConfirmation(draggedItem, item);
            }
          }
        });
      }
    }

    function showMoveConfirmation(sourceItem, targetItem) {
      moveSourceItem = sourceItem;
      moveTargetItem = targetItem;

      // Get source name safely
      let sourceName = 'Elemento';
      const sourceNameEl = sourceItem.querySelector('.name .name-chip') || 
                          sourceItem.querySelector('.name span:last-child') || 
                          sourceItem.querySelector('.name > span');
      if (sourceNameEl) {
        sourceName = sourceNameEl.textContent.trim();
      }

      // Get target name safely  
      let targetName = 'Carpeta';
      const targetNameEl = targetItem.querySelector('.name .name-chip') || 
                          targetItem.querySelector('.name span:last-child') || 
                          targetItem.querySelector('.name > span');
      if (targetNameEl) {
        targetName = targetNameEl.textContent.trim();
      }

      console.log('Move confirmation - Source:', sourceName, 'Target:', targetName);

      document.getElementById('move-source').innerHTML = 
        `<i class="fas fa-${sourceItem.dataset.type === 'folder' ? 'folder' : 'file'}"></i><span>${sourceName}</span>`;
      document.getElementById('move-target').innerHTML = 
        `<i class="fas fa-folder"></i><span>${targetName}</span>`;

      document.getElementById('move-modal-overlay').style.display = 'flex';
    }

    function closeMoveModal() {
      document.getElementById('move-modal-overlay').style.display = 'none';
      moveSourceItem = null;
      moveTargetItem = null;
    }

    async function confirmMove() {
      if (!moveSourceItem || !moveTargetItem) {
        closeMoveModal();
        return;
      }

      try {
        // Obtener targetId de forma robusta
        let targetId = null;
        if (moveTargetItem && moveTargetItem.dataset && (moveTargetItem.dataset.id !== undefined)) {
          targetId = moveTargetItem.dataset.id;
        } else if (typeof moveTargetItem === 'object' && ('id' in moveTargetItem)) {
          targetId = moveTargetItem.id;
        }
        
        if (targetId === null || targetId === undefined) {
          throw new Error('Destino inválido');
        }
        
        // Handle multiple items
        if (Array.isArray(moveSourceItem)) {
          console.log('Moving multiple items:', moveSourceItem.length);
          
          for (const item of moveSourceItem) {
            const sourceType = item.dataset.type;
            const sourceId = item.dataset.id;
            const endpoint = sourceType === 'folder' ? '/drive/folder/move' : '/drive/file/move';
            const paramName = sourceType === 'folder' ? 'parent' : 'folder_id';

            const formData = new URLSearchParams();
            formData.append('_csrf', csrf);
            formData.append('id', sourceId);
            // Para carpetas, mover a raíz si targetId es 0 omitiendo 'parent'
            if (!(sourceType === 'folder' && String(targetId) === '0')) {
              formData.append(paramName, targetId);
            }

            const response = await fetch('/biblioteca/public/index.php' + endpoint, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            });

            let result;
            const text = await response.text();
            try {
              result = JSON.parse(text);
            } catch (e) {
              console.error('Non-JSON response for move:', text);
              const msg = extractPlainTextError(text) || 'Respuesta no válida del servidor al mover';
              throw new Error(msg);
            }
            if (result.ok) {
              item.remove();
              selectedItems.delete(item);
            } else {
              console.error('Error moving item:', sourceId, result);
            }
          }
          
          console.log('Multiple items moved successfully');
          updateToolbar();
          
        } else {
          // Handle single item (existing logic)
          const sourceType = moveSourceItem.dataset.type;
          const sourceId = moveSourceItem.dataset.id;
          const endpoint = sourceType === 'folder' ? '/drive/folder/move' : '/drive/file/move';
          const paramName = sourceType === 'folder' ? 'parent' : 'folder_id';

          const formData = new URLSearchParams();
          formData.append('_csrf', csrf);
          formData.append('id', sourceId);
          // Para carpetas, mover a raíz si targetId es 0 omitiendo 'parent'
          if (!(sourceType === 'folder' && String(targetId) === '0')) {
            formData.append(paramName, targetId);
          }

          const response = await fetch('/biblioteca/public/index.php' + endpoint, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          });

          let result;
          const text = await response.text();
          try {
            result = JSON.parse(text);
          } catch (e) {
            console.error('Non-JSON response for move:', text);
            const msg = extractPlainTextError(text) || 'Respuesta no válida del servidor al mover';
            throw new Error(msg);
          }
          if (result.ok) {
            moveSourceItem.remove();
            console.log('Item moved successfully');
            
            // For column view, only refresh affected columns instead of reinitializing
            if (currentView === 'columns') {
              await refreshAffectedColumns(sourceId, targetId);
            }
          } else {
            showError(result.error || 'Error al mover elemento');
          }
        }
      } catch (error) {
        console.error('Move error:', error);
        showError(error && error.message ? error.message : 'Error al mover elemento');
      }

      closeMoveModal();
    }

    // Refresh only affected columns without losing navigation
    async function refreshAffectedColumns(sourceId, targetId) {
      console.log('Refreshing affected columns - Source:', sourceId, 'Target:', targetId);
      
      const container = document.getElementById('column-container');
      const columns = container.querySelectorAll('.column');
      
      // Find and refresh columns that might be affected
      for (const column of columns) {
        const folderId = column.dataset.folderId;
        const content = column.querySelector('.column-content');
        
        if (!content) continue;
        
        // Check if this column contains the moved item or is the target
        const movedItem = content.querySelector(`[data-id="${sourceId}"]`);
        const isTargetColumn = folderId == targetId;
        
        if (movedItem || isTargetColumn) {
          console.log('Refreshing column:', folderId);
          await refreshSingleColumn(column, folderId);
        }
      }
    }

    async function refreshSingleColumn(column, folderId) {
      const content = column.querySelector('.column-content');
      if (!content) return;
      
      const columnIndex = Array.from(column.parentElement.children).indexOf(column);
      
      try {
        const response = await fetch('/biblioteca/public/index.php/drive/list?folder=' + folderId);
        const data = await response.json();
        
        // Clear and repopulate content
        content.innerHTML = '';
        
        // Add folders
        data.folders.forEach(folder => {
          const item = document.createElement('div');
          item.className = 'column-item has-children';
          item.dataset.type = 'folder';
          item.dataset.id = folder.id;
          
          const iconColor = folder.color_etiqueta ? ` style="color: ${folder.color_etiqueta};"` : ` style="color: #9ca3af;"`;
          const labelHtml = (folder.etiqueta && folder.color_etiqueta) ? 
            `<span class="label" style="background-color: ${folder.color_etiqueta};">${folder.etiqueta}</span>` : '';
          
          item.innerHTML = `<i class="fas fa-folder icon"${iconColor}></i><span>${folder.nombre}</span>${labelHtml}`;
          
          // Add selection event listeners
          item.addEventListener('click', (e) => {
            handleItemClick(e, item);
            // Expand column only if not holding modifier keys
            if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
              expandColumn(folder.id, folder.nombre, columnIndex + 1);
            }
          });
          item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            e.preventDefault();
            console.log('Double click on folder in column:', folder.id);
            navigateToFolder(folder.id);
          });
          // Add context menu support
          item.addEventListener('contextmenu', (e) => {
            handleContextMenu(e);
          });
          setupDragAndDrop(item);
          content.appendChild(item);
        });

        // Add files
        data.files.forEach(file => {
          const item = document.createElement('div');
          item.className = 'column-item';
          item.dataset.type = 'file';
          item.dataset.id = file.id;
          
          const icon = getFileIcon(file.mime || file.extension);
          const iconHtml = icon.includes('fa-') ? `<i class="${icon} icon"></i>` : `<span class="icon">${icon}</span>`;
          
          item.innerHTML = `${iconHtml}<span>${file.nombre}</span>`;
          
          // Add selection event listeners for files
          item.addEventListener('click', (e) => {
            handleItemClick(e, item);
            // Open preview if not holding modifier keys
            if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
              openItem('file', file.id);
            }
          });
          
          item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            e.preventDefault();
            console.log('Double click on file in column:', file.id);
            openItem('file', file.id);
          });
          // Add context menu support
          item.addEventListener('contextmenu', (e) => {
            handleContextMenu(e);
          });
          setupDragAndDrop(item);
          content.appendChild(item);
        });

      } catch (error) {
        console.error('Error refreshing column:', error);
      }
    }

    // Column Drag & Drop
    function setupColumnDragAndDrop(column, folderId, folderName) {
      const content = column.querySelector('.column-content');
      if (!content) return;

      content.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (draggedItem) {
          content.classList.add('drag-over');
          column.classList.add('drag-over');
        }
      });

      content.addEventListener('dragleave', (e) => {
        // Only remove if we're really leaving the column area
        if (!content.contains(e.relatedTarget)) {
          content.classList.remove('drag-over');
          column.classList.remove('drag-over');
        }
      });

      content.addEventListener('drop', (e) => {
        e.preventDefault();
        content.classList.remove('drag-over');
        column.classList.remove('drag-over');
        
        if (draggedItem) {
          console.log('Dropped on column:', folderId, folderName);
          
          // Create a virtual target item for the confirmation modal
          const virtualTarget = {
            dataset: { type: 'folder', id: folderId },
            _isVirtual: true,
            _name: folderName
          };
          
          showMoveConfirmation(draggedItem, virtualTarget);
        }
      });
    }

    function showMoveConfirmation(sourceItem, targetItem) {
      moveSourceItem = sourceItem;
      moveTargetItem = targetItem;

      // Get source name safely
      let sourceName = 'Elemento';
      if (sourceItem.querySelector) {
        const sourceNameEl = sourceItem.querySelector('.name span:last-child') || 
                            sourceItem.querySelector('.name > span') || 
                            sourceItem.querySelector('span:last-child');
        if (sourceNameEl) {
          sourceName = sourceNameEl.textContent.trim();
        }
      }

      // Get target name safely  
      let targetName = 'Carpeta';
      if (targetItem._isVirtual) {
        // Virtual target from column drop
        targetName = targetItem._name;
      } else if (targetItem.querySelector) {
        const targetNameEl = targetItem.querySelector('.name span:last-child') || 
                            targetItem.querySelector('.name > span') || 
                            targetItem.querySelector('span:last-child');
        if (targetNameEl) {
          targetName = targetNameEl.textContent.trim();
        }
      }

      console.log('Move confirmation - Source:', sourceName, 'Target:', targetName);

      document.getElementById('move-source').innerHTML = 
        `<i class="fas fa-${sourceItem.dataset.type === 'folder' ? 'folder' : 'file'}"></i><span>${sourceName}</span>`;
      document.getElementById('move-target').innerHTML = 
        `<i class="fas fa-folder"></i><span>${targetName}</span>`;

      document.getElementById('move-modal-overlay').style.display = 'flex';
    }

    // Show message in column view
    function showColumnViewMessage(message) {
      const columnContainer = document.getElementById('column-container');
      if (columnContainer) {
        columnContainer.innerHTML = `
          <div style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; text-align: center; color: #888; font-size: 16px; line-height: 1.5;">
            <div>
              <i class="fas fa-info-circle" style="font-size: 48px; margin-bottom: 20px; color: #666;"></i>
              <div>${message}</div>
            </div>
          </div>
        `;
      }
    }

    // Column View (macOS Finder style)
    async function initializeColumnView() {
      console.log('Initializing column view');
      const columnView = document.getElementById('column-view');
      const container = document.getElementById('column-container');
      
      console.log('Column view element:', columnView);
      console.log('Column container element:', container);
      
      if (!columnView || !container) {
        console.error('Column view elements not found');
        return;
      }
      
      columnPath = [0]; // Start with root
      await renderColumns();
    }

    async function renderColumns() {
      const container = document.getElementById('column-container');
      container.innerHTML = '';
      
      console.log('Rendering columns for path:', columnPath);

      // Create initial column with root folders
      const rootColumn = document.createElement('div');
      rootColumn.className = 'column';
      rootColumn.dataset.folderId = '0';
      rootColumn.innerHTML = `
        <div class="column-header">DRIVE</div>
        <div class="column-content" id="column-0"></div>
      `;
      container.appendChild(rootColumn);
      
      // Setup drag & drop for root column
      setupColumnDragAndDrop(rootColumn, 0, 'DRIVE');

      // Load root folders
      try {
        const response = await fetch('/biblioteca/public/index.php/drive/list?folder=0');
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }
        
        const data = await response.json();
        console.log('Root data for columns:', data);
        console.log('Folders count:', data.folders ? data.folders.length : 0);
        
        const content = document.getElementById('column-0');
        if (!content) {
          console.error('Column content element not found');
          return;
        }

        if (data.folders && data.folders.length > 0) {
          data.folders.forEach(folder => {
            console.log('Adding folder to column:', folder);
            const item = document.createElement('div');
            item.className = 'column-item has-children';
            item.dataset.type = 'folder';
            item.dataset.id = folder.id;
            
            const iconColor = folder.color_etiqueta ? ` style="color: ${folder.color_etiqueta};"` : ` style="color: #9ca3af;"`;
            const labelHtml = (folder.etiqueta && folder.color_etiqueta) ? 
              `<span class="label" style="background-color: ${folder.color_etiqueta};">${folder.etiqueta}</span>` : '';
            const customIconHtml = folder.icono_personalizado ? 
              `<i class="${folder.icono_personalizado}" style="margin-left: 4px; font-size: 12px;"></i>` : '';
            
            item.innerHTML = `<i class="fas fa-folder icon"${iconColor}></i><span>${folder.nombre}</span>${customIconHtml}${labelHtml}`;
          
          item.addEventListener('click', () => expandColumn(folder.id, folder.nombre, 1));
          // Add context menu support
          item.addEventListener('contextmenu', (e) => {
            handleContextMenu(e);
          });
          setupDragAndDrop(item);
          content.appendChild(item);
          });
        } else {
          console.log('No folders found in root');
          content.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Sin carpetas</div>';
        }

      } catch (error) {
        console.error('Error loading root columns:', error);
        const content = document.getElementById('column-0');
        if (content) {
          content.innerHTML = '<div style="padding: 20px; text-align: center; color: #e74c3c;">Error al cargar</div>';
        }
      }
    }

    async function expandColumn(folderId, folderName, columnIndex) {
      console.log('Expanding column:', folderId, folderName, columnIndex);
      
      // Remove existing columns after current
      const container = document.getElementById('column-container');
      const existingColumns = container.querySelectorAll('.column');
      for (let i = columnIndex; i < existingColumns.length; i++) {
        existingColumns[i].remove();
      }

      // Clear selection in current column
      if (existingColumns[columnIndex - 1]) {
        existingColumns[columnIndex - 1].querySelectorAll('.column-item').forEach(item => {
          item.classList.remove('selected');
        });
        
        // Select clicked item
        const clickedItem = existingColumns[columnIndex - 1].querySelector(`[data-id="${folderId}"]`);
        if (clickedItem) {
          clickedItem.classList.add('selected');
        }
      }

      // Create new column
      const newColumn = document.createElement('div');
      newColumn.className = 'column';
      newColumn.dataset.folderId = folderId;
      newColumn.innerHTML = `
        <div class="column-header">${folderName}</div>
        <div class="column-content" id="column-${columnIndex}"></div>
      `;
      container.appendChild(newColumn);
      
      // Setup drag & drop for the column
      setupColumnDragAndDrop(newColumn, folderId, folderName);

      // Load content for new column
      try {
        const response = await fetch('/biblioteca/public/index.php/drive/list?folder=' + folderId);
        const data = await response.json();
        console.log('Column data:', data);
        
        const content = document.getElementById(`column-${columnIndex}`);
        
        // Add folders
        data.folders.forEach(folder => {
          const item = document.createElement('div');
          item.className = 'column-item has-children';
          item.dataset.type = 'folder';
          item.dataset.id = folder.id;
          
          const iconColor = folder.color_etiqueta ? ` style="color: ${folder.color_etiqueta};"` : ` style="color: #9ca3af;"`;
          const labelHtml = (folder.etiqueta && folder.color_etiqueta) ? 
            `<span class="label" style="background-color: ${folder.color_etiqueta};">${folder.etiqueta}</span>` : '';
          
          item.innerHTML = `<i class="fas fa-folder icon"${iconColor}></i><span>${folder.nombre}</span>${labelHtml}`;
          
          // Add selection event listeners
          item.addEventListener('click', (e) => {
            handleItemClick(e, item);
            // Expand column only if not holding modifier keys
            if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
              expandColumn(folder.id, folder.nombre, columnIndex + 1);
            }
          });
          item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            e.preventDefault();
            console.log('Double click on folder in column:', folder.id);
            navigateToFolder(folder.id);
          });
          // Add context menu support
          item.addEventListener('contextmenu', (e) => {
            handleContextMenu(e);
          });
          setupDragAndDrop(item);
          content.appendChild(item);
        });

        // Add files
        data.files.forEach(file => {
          const item = document.createElement('div');
          item.className = 'column-item';
          item.dataset.type = 'file';
          item.dataset.id = file.id;
          
          const icon = getFileIcon(file.mime || file.extension);
          const iconHtml = icon.includes('fa-') ? `<i class="${icon} icon"></i>` : `<span class="icon">${icon}</span>`;
          
          item.innerHTML = `${iconHtml}<span>${file.nombre}</span>`;
          
          // Add selection event listeners for files
          item.addEventListener('click', (e) => {
            handleItemClick(e, item);
            // Open preview if not holding modifier keys
            if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
              openItem('file', file.id);
            }
          });
          
          item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            e.preventDefault();
            console.log('Double click on file in column:', file.id);
            openItem('file', file.id);
          });
          // Add context menu support
          item.addEventListener('contextmenu', (e) => {
            handleContextMenu(e);
          });
          setupDragAndDrop(item);
          content.appendChild(item);
        });

      } catch (error) {
        console.error('Error loading column content:', error);
      }
    }

    // Preview Panel Functions
    async function openFilePreview(fileId) {
      try {
        console.log('Opening preview for file:', fileId);
        console.log('Current view:', currentView);
        
        // Fetch file information
        const response = await fetch('/biblioteca/public/index.php/drive/file-info?id=' + fileId, {
          credentials: 'include'  // Include cookies for authentication
        });
        console.log('Response status:', response.status, response.statusText);
        
        if (!response.ok) {
          throw new Error('Failed to fetch file info: ' + response.status);
        }
        
        const fileInfo = await response.json();
        console.log('File info response:', fileInfo);
        
        if (!fileInfo.ok) {
          throw new Error(fileInfo.error || 'Error getting file info');
        }
        
        const file = fileInfo.file;
        console.log('File data:', file);
        
        // Update preview panel
        showPreviewPanel(file);
        
      } catch (error) {
        console.error('Error opening file preview:', error);
        showError('Error al cargar la vista previa del archivo: ' + error.message);
      }
    }
    
    function showPreviewPanel(file) {
      console.log('Showing preview panel for file:', file);
      console.log('Current view:', currentView);
      
      const panel = document.getElementById('preview-panel');
      const title = document.getElementById('preview-title');
      const viewer = document.getElementById('preview-viewer');
      const metadata = document.getElementById('preview-metadata');
      const main = document.querySelector('.main');
      
      // Guardar archivo actual para el modal
      currentPreviewFile = file;
      
      // Update title (truncate to 30 chars + …)
      const name = file.nombre || 'Vista Previa';
      title.textContent = name.length > 30 ? name.slice(0, 30) + '…' : name;
      
      // Generate preview content based on file type
      const previewContent = generatePreviewContent(file);
      viewer.innerHTML = previewContent;
      
      // Generate metadata
      const metadataContent = generateMetadataContent(file);
      metadata.innerHTML = metadataContent;
      
      // Show panel
      panel.classList.add('open');
      main.classList.add('preview-open');
    }
    
    function generatePreviewContent(file) {
      const fileExtension = (file.extension || '').toLowerCase();
      const mimeType = file.tipo_mime || '';
      const previewUrl = '/biblioteca/public/index.php/drive/preview?id=' + file.id;
      
      console.log('Generating preview for:', {
        extension: fileExtension,
        mimeType: mimeType,
        previewUrl: previewUrl,
        fileName: file.nombre
      });
      
      // Image files
      if (mimeType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(fileExtension)) {
        console.log('Creating image preview for:', file.nombre);
        return `<img src="${previewUrl}" alt="${file.nombre || 'Image'}" 
                     onload="this.style.opacity=1; console.log('Image loaded successfully:', this.src);" 
                     onerror="handleImageError(this);" 
                     style="opacity:0;transition:opacity 0.3s">`;
      }
      
      // PDF files
      if (mimeType === 'application/pdf' || fileExtension === 'pdf') {
        return '<iframe src="' + previewUrl + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf"></iframe>';
      }
      
      // Text files
      if (mimeType.startsWith('text/') || ['txt', 'md', 'json', 'xml', 'csv', 'log'].includes(fileExtension)) {
        return '<iframe src="' + previewUrl + '" style="background: white;"></iframe>';
      }
      
      // Video files
      if (mimeType.startsWith('video/') || ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'].includes(fileExtension)) {
        return '<video controls style="width: 100%; max-height: 300px;"><source src="' + previewUrl + '" type="' + mimeType + '">Tu navegador no soporta video.</video>';
      }
      
      // Audio files
      if (mimeType.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'flac', 'aac'].includes(fileExtension)) {
        return '<audio controls style="width: 100%;"><source src="' + previewUrl + '" type="' + mimeType + '">Tu navegador no soporta audio.</audio>';
      }
      
      // Default: show file icon
      const icon = getFileIcon(mimeType);
      return '<div class="file-icon"><i class="' + icon + '"></i><div style="margin-top: 12px; font-size: 14px; color: #888;">Vista previa no disponible</div></div>';
    }
    
    function generateMetadataContent(file) {
      let html = '';
      
      if (file.nombre) {
        html += '<div class="metadata-item"><span class="metadata-label">Nombre:</span><span class="metadata-value">' + file.nombre + '</span></div>';
      }
      
      if (file.extension) {
        html += '<div class="metadata-item"><span class="metadata-label">Tipo:</span><span class="metadata-value">' + file.extension.toUpperCase() + '</span></div>';
      }
      
      if (file.tipo_mime) {
        html += '<div class="metadata-item"><span class="metadata-label">MIME:</span><span class="metadata-value">' + file.tipo_mime + '</span></div>';
      }
      
      if (file.tamaño) {
        const size = formatBytes(file.tamaño);
        html += '<div class="metadata-item"><span class="metadata-label">Tamaño:</span><span class="metadata-value">' + size + '</span></div>';
      }
      
      if (file.fecha_creacion) {
        const date = new Date(file.fecha_creacion).toLocaleString();
        html += '<div class="metadata-item"><span class="metadata-label">Creado:</span><span class="metadata-value">' + date + '</span></div>';
      }
      
      if (file.fecha_modificacion) {
        const date = new Date(file.fecha_modificacion).toLocaleString();
        html += '<div class="metadata-item"><span class="metadata-label">Modificado:</span><span class="metadata-value">' + date + '</span></div>';
      }
      
      if (file.propietario_nombre) {
        html += '<div class="metadata-item"><span class="metadata-label">Propietario:</span><span class="metadata-value">' + file.propietario_nombre + '</span></div>';
      }
      
      return html;
    }
    
    function closePreview() {
      const panel = document.getElementById('preview-panel');
      const main = document.querySelector('.main');
      
      panel.classList.remove('open');
      main.classList.remove('preview-open');
    }
    
    // Handle image loading errors
    function handleImageError(img) {
      console.error('Failed to load image:', img.src);
      const errorHtml = `
        <div class="file-icon">
          <i class="fas fa-image"></i>
          <div style="margin-top: 12px; font-size: 14px; color: #888;">
            Error cargando imagen
          </div>
        </div>
      `;
      img.parentNode.innerHTML = errorHtml;
    }

    // Extraer mensaje de error legible desde HTML/texto
    function extractPlainTextError(text) {
      try {
        // Si es JSON válido
        const data = JSON.parse(text);
        if (data && (data.error || data.message)) {
          return data.error || data.message;
        }
      } catch (_) {}
      // Quitar tags HTML básicos
      const noTags = String(text)
        .replace(/<script[\s\S]*?<\/script>/gi, '')
        .replace(/<style[\s\S]*?<\/style>/gi, '')
        .replace(/<br\s*\/?>(\s*)/gi, '\n')
        .replace(/<\/?[^>]+>/g, '')
        .replace(/\n\s*\n\s*\n+/g, '\n\n')
        .trim();
      // Tomar primeras 200 letras
      return noTags ? noTags.slice(0, 200) : '';
    }

    // Variables para el modal de vista previa
    let currentPreviewFile = null;

    // Función para escapar strings en JavaScript
    function escapeJsString(str) {
      if (!str) return '';
      return str.replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\\/g, '\\\\').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
    }

    // Abrir modal de vista previa expandida
    function openPreviewModal() {
      if (!currentPreviewFile) return;
      
      console.log('Opening preview modal for file:', currentPreviewFile);
      
      const modal = document.getElementById('previewModal');
      const title = document.getElementById('previewModalTitle');
      const body = document.getElementById('previewModalBody');
      const downloadBtn = document.getElementById('downloadModalBtn');
      
      // Establecer título (truncate to 30 chars + …)
      const fullName = currentPreviewFile.nombre || 'Vista Previa';
      title.textContent = fullName.length > 30 ? fullName.slice(0, 30) + '…' : fullName;
      
      // Mostrar loading
      body.innerHTML = `
        <div class="preview-modal-loading">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Cargando vista previa expandida...</span>
        </div>
      `;
      
      // Configurar botón de descarga
      downloadBtn.onclick = () => downloadFromModal();
      
      // Mostrar modal
      modal.style.display = 'flex';
      
      // Generar contenido expandido
      generateExpandedPreviewContent(currentPreviewFile);
    }

    // Cerrar modal de vista previa
    function closePreviewModal() {
      const modal = document.getElementById('previewModal');
      modal.style.display = 'none';
    }

    // Descargar desde el modal
    function downloadFromModal() {
      if (currentPreviewFile) {
        downloadFile(currentPreviewFile.id, currentPreviewFile.nombre);
      }
    }

    // Generar contenido expandido para el modal
    function generateExpandedPreviewContent(file) {
      const body = document.getElementById('previewModalBody');
      const extension = file.extension ? file.extension.toLowerCase() : '';
      const mimeType = file.tipo_mime || '';
      
      console.log('DEBUG: Generating expanded preview for file:', file);
      console.log('DEBUG: File ID:', file.id, 'MIME type:', mimeType, 'Extension:', extension);
      
      if (mimeType.startsWith('image/')) {
        // Imagen en tamaño completo
        const imageUrl = window.location.origin + '/biblioteca/public/index.php/drive/preview?id=' + file.id;
        console.log('DEBUG: Image URL:', imageUrl);
        
        // Test if URL is accessible
        fetch(imageUrl, { 
          method: 'HEAD',
          credentials: 'include' 
        })
        .then(response => {
          console.log('DEBUG: Image URL test response:', response.status, response.statusText);
        })
        .catch(error => {
          console.error('DEBUG: Image URL test failed:', error);
        });
        
        body.innerHTML = `
          <img src="${imageUrl}" 
               alt="${file.nombre || 'Imagen'}"
               style="max-width: 100%; max-height: 100%; object-fit: contain;"
               onload="console.log('Image loaded successfully:', this.src);"
               onerror="console.error('Image failed to load:', this.src); this.style.display='none'; this.parentElement.innerHTML='<div class=\\'file-info-large\\'><div class=\\'file-icon-large\\'><i class=\\'fas fa-image\\'></i></div><h3>Error al cargar imagen</h3><p>URL: ${imageUrl}</p><p>No se pudo mostrar la imagen</p></div>'">
        `;
      } else if (mimeType === 'application/pdf') {
        // PDF embebido
        const pdfUrl = window.location.origin + '/biblioteca/public/index.php/drive/preview?id=' + file.id;
        console.log('DEBUG: PDF URL:', pdfUrl);
        
        // Test if PDF URL is accessible
        fetch(pdfUrl, { 
          method: 'HEAD',
          credentials: 'include' 
        })
        .then(response => {
          console.log('DEBUG: PDF URL test response:', response.status, response.statusText);
          if (!response.ok) {
            console.error('DEBUG: PDF URL returned error:', response.status);
          }
        })
        .catch(error => {
          console.error('DEBUG: PDF URL test failed:', error);
        });
        
        // Crear contenedor con iframe y fallbacks
        const pdfContainer = document.createElement('div');
        pdfContainer.className = 'pdf-container';
        
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.title = file.nombre || 'PDF';
        iframe.style.cssText = 'width: 100%; height: 60vh; border: none; border-radius: 8px;';
        
        let loadTimeout;
        
        iframe.onload = function() {
          console.log('PDF loaded successfully:', this.src);
          clearTimeout(loadTimeout);
        };
        
        iframe.onerror = function() {
          console.error('PDF failed to load in iframe:', this.src);
          showPDFError();
        };
        
        // Timeout para detectar PDFs que no cargan
        loadTimeout = setTimeout(() => {
          console.warn('PDF loading timeout, showing fallback options');
          showPDFError();
        }, 5000);
        
        function showPDFError() {
          pdfContainer.innerHTML = 
            '<div class="file-info-large">' +
              '<div class="file-icon-large">' +
                '<i class="fas fa-file-pdf"></i>' +
              '</div>' +
              '<h3>Vista previa de PDF no disponible</h3>' +
              '<p>URL: ' + pdfUrl + '</p>' +
              '<p>El navegador no puede mostrar este PDF directamente</p>' +
              '<div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">' +
                '<button class="btn btn-primary" onclick="window.open(\'' + pdfUrl + '\', \'_blank\')">' +
                  '<i class="fas fa-external-link-alt"></i>' +
                  ' Abrir en nueva ventana' +
                '</button>' +
                '<button class="btn btn-secondary" onclick="downloadFile(' + file.id + ', \'' + escapeJsString(file.nombre) + '\')">' +
                  '<i class="fas fa-download"></i>' +
                  ' Descargar PDF' +
                '</button>' +
              '</div>' +
            '</div>';
        }
        
        pdfContainer.appendChild(iframe);
        body.appendChild(pdfContainer);
      } else if (mimeType.startsWith('text/') || ['txt', 'md', 'json', 'xml', 'csv'].includes(extension)) {
        // Archivo de texto
        const textUrl = window.location.origin + '/biblioteca/public/index.php/drive/preview?id=' + file.id;
        console.log('DEBUG: Text file URL:', textUrl);
        
        fetch(textUrl, {
          credentials: 'include'
        })
          .then(response => {
            console.log('DEBUG: Text file response:', response.status, response.statusText);
            if (!response.ok) {
              throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.text();
          })
          .then(content => {
            console.log('DEBUG: Text file loaded successfully, length:', content.length);
            body.innerHTML = 
              '<div class="document-preview">' +
                '<div class="document-header">' +
                  '<i class="fas fa-file-alt"></i>' +
                  '<span>Archivo de texto - ' + escapeHtml(file.nombre || '') + '</span>' +
                  '<small>(' + content.length + ' caracteres)</small>' +
                '</div>' +
                '<pre style="white-space: pre-wrap; font-family: \'Courier New\', monospace; padding: 20px; background: #f8f9fa; border-radius: 4px; max-height: 50vh; overflow: auto;">' + escapeHtml(content) + '</pre>' +
              '</div>';
          })
          .catch(error => {
            console.error('DEBUG: Text file failed to load:', error);
            body.innerHTML = generateFileInfoLarge(file);
          });
        return;
      } else {
        // Otros tipos de archivo - mostrar información
        body.innerHTML = generateFileInfoLarge(file);
      }
    }

    // Generar información de archivo grande
    function generateFileInfoLarge(file) {
      const icon = getFileIcon(file.tipo_mime || file.extension);
      const size = file.tamaño ? formatBytes(file.tamaño) : 'Desconocido';
      
      return `
        <div class="file-info-large">
          <div class="file-icon-large">
            <i class="${icon}"></i>
          </div>
          <h3>${escapeHtml(file.nombre || 'Archivo sin nombre')}</h3>
          <p><strong>Tipo:</strong> ${file.tipo_mime || 'Desconocido'}</p>
          <p><strong>Tamaño:</strong> ${size}</p>
          <p><strong>Fecha:</strong> ${file.fecha_creacion || 'Desconocida'}</p>
          <p style="margin-top: 20px; opacity: 0.7;">
            Vista previa no disponible para este tipo de archivo.
          </p>
        </div>
      `;
    }
    
    // Download file function
    function downloadFile(fileId, fileName) {
      console.log('Downloading file:', fileId, fileName);
      const downloadUrl = '/biblioteca/public/index.php/drive/download?id=' + fileId;
      
      // Create a temporary link to trigger download
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = fileName || 'archivo';
      link.style.display = 'none';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Navigate to root function
    function navigateToRoot() {
      console.log('Navigating to root (DRIVE)');
      
      // Clear shared with me state
      isSharedWithMeView = false;
      sharedFolderIds.clear();
      
      // Update sidebar selection
      updateSidebarSelection('drive');
      
      // Navigate to folder 0 (root)
      navigateToFolder(0);
    }

    // Close preview with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('previewModal');
        const panel = document.getElementById('preview-panel');
        
        // Cerrar modal si está abierto
        if (modal && modal.style.display === 'flex') {
          closePreviewModal();
        } 
        // Sino, cerrar panel si está abierto
        else if (panel.classList.contains('open')) {
          closePreview();
        }
      }
    });

  </script>
</body>
</html>
