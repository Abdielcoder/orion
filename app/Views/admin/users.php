<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Usuarios - Drive</title>
  <!-- Font Awesome Icons - Local -->
  <link rel="stylesheet" href="/biblioteca/public/assets/css/fontawesome.min.css">
  <link rel="stylesheet" href="/biblioteca/public/assets/css/theme.css">
  <style>
    * { box-sizing: border-box; }
    html { background: #ffffff; }
    body { 
      font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Ubuntu, Cantarell, 'Noto Sans', sans-serif; 
      margin: 0; 
      background: #ffffff;
      color: #111827; 
      overflow: hidden;
      min-height: 100vh;
    }

    /* Light Theme Overrides */
    body.light-theme { background: #ffffff; color: #111827; }
    body.light-theme .header { 
      background: #ffffff;
      border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    }
    body.light-theme .header .logo { color: #0ea5e9; }
    body.light-theme .header .user { color: #374151; }
    body.light-theme .header .btn { 
      background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
      box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }
    body.light-theme .header .btn:hover { 
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
      transform: translateY(-1px);
    }
    body.light-theme .header .btn.success {
      background: #10b981;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    body.light-theme .header .btn.success:hover {
      background: #059669;
    }

    body.light-theme .main { background: transparent; }
    body.light-theme .sidebar { 
      background: #ffffff;
      border-right: 1px solid rgba(229, 231, 235, 0.5);
    }
    body.light-theme .sidebar-header { 
      color: #6b7280; 
      border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    }
    body.light-theme .tree-item:hover { 
      background: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
    }
    body.light-theme .tree-item.active { 
      background: #ffffff;
      color: #1e3a8a;
      border-left: 3px solid #1e3a8a;
    }

    body.light-theme .content { background: transparent; }
    body.light-theme .toolbar { 
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(229, 231, 235, 0.5);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    body.light-theme .toolbar .btn { 
      background: rgba(255, 255, 255, 0.8);
      color: #374151;
      border: 1px solid rgba(229, 231, 235, 0.5);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    body.light-theme .toolbar .btn:hover { 
      background: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
      transform: translateY(-1px);
    }
    body.light-theme .toolbar .separator { background: rgba(229, 231, 235, 0.5); }
    
    /* Font Awesome Icons */
    i { display: inline-block; }
    .fas, .far, .fab { font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands"; font-weight: 900; }
    
    /* Header - Super Tecnológico */
    .header { 
      height: 56px; 
      background: #f8fafc;
      border-bottom: 1px solid rgba(229, 231, 235, 0.6);
      display: flex; 
      align-items: center; 
      padding: 0 24px; 
      gap: 16px;
      position: relative;
      z-index: 1000;
    }
    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      pointer-events: none;
    }
    .header .logo { 
      font-weight: 700; 
      color: #60a5fa; 
      display: flex; 
      align-items: center; 
      gap: 12px;
      font-size: 16px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    .header .logo i { 
      width: 24px; 
      height: 24px;
      background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .header .user { 
      margin-left: auto; 
      font-size: 14px; 
      color: #e5e7eb; 
      display: flex; 
      align-items: center; 
      gap: 8px;
      font-weight: 500;
    }
    .header .user i { 
      width: 18px; 
      height: 18px;
      color: #60a5fa;
    }
    .header .btn { 
      background: #2563eb;
      color: #fff; 
      border: none; 
      border-radius: 12px; 
      padding: 10px 16px; 
      font-size: 13px; 
      font-weight: 600;
      cursor: pointer; 
      display: flex; 
      align-items: center; 
      gap: 8px; 
      text-decoration: none;
      transition: background .2s ease, transform .2s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .header .btn:hover { 
      background: #1d4ed8;
      transform: translateY(-1px);
    }
    .header .btn.success {
      background: #10b981;
    }
    .header .btn.success:hover {
      background: #059669;
    }
    .header .btn i { 
      width: 16px; 
      height: 16px;
    }
    
    /* Main Layout */
    .main { 
      display: grid; 
      grid-template-columns: 240px 1fr; 
      height: calc(100vh - 56px);
      gap: 0;
    }
    
    /* Sidebar - Super Tecnológico */
    .sidebar { 
      background: #ffffff;
      border-right: 1px solid rgba(229, 231, 235, 0.6);
      overflow-y: auto;
      position: relative;
    }
    .sidebar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      pointer-events: none;
    }
    .sidebar-header { 
      padding: 20px 16px 12px; 
      font-weight: 700; 
      font-size: 12px; 
      text-transform: uppercase; 
      color: #9ca3af;
      border-bottom: 1px solid rgba(62, 62, 66, 0.5);
      letter-spacing: 1px;
      position: relative;
      z-index: 1;
    }
    .tree-item { 
      padding: 12px 16px; 
      cursor: pointer; 
      font-size: 14px; 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      text-decoration: none; 
      color: #374151; /* gris oscuro */
      transition: all 0.2s ease;
      border-radius: 0 12px 12px 0;
      margin: 4px 0;
      position: relative;
      z-index: 1;
      font-weight: 500;
    }
    .tree-item:hover { 
      background: rgba(59, 130, 246, 0.1);
      color: #60a5fa;
      transform: translateX(4px);
    }
    .tree-item.active { 
      background: #ffffff; /* fondo blanco */
      color: #1e3a8a;       /* azul oscuro para texto */
      border-left: 3px solid #1e3a8a; /* acento discreto */
    }
    .tree-item .icon { width: 18px; height: 18px; }
    .tree-item i { 
      width: 18px; 
      height: 18px;
      transition: all 0.2s ease;
    }
    .tree-item.active i { color: #1e3a8a; }
    
    /* Content */
    .content { 
      background: transparent;
      overflow: hidden; 
      display: flex; 
      flex-direction: column;
      position: relative;
      padding: 16px;
      gap: 12px;
    }
    .content::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
      pointer-events: none;
    }
    
    /* Toolbar - Super Tecnológico */
    .toolbar { 
      height: 60px; 
      background: #ffffff;
      border: 1px solid #e5e7eb;
      display: flex; 
      align-items: center; 
      padding: 0 16px; 
      gap: 12px; 
      flex-shrink: 0;
      border-radius: 12px;
      position: sticky;
      top: 8px;
      z-index: 10;
    }
    .toolbar .btn { 
      background: #0ea5e9;
      color: #fff; 
      border: none;
      border-radius: 10px; 
      padding: 10px 16px; 
      font-size: 13px; 
      font-weight: 600;
      cursor: pointer; 
      display: flex; 
      align-items: center; 
      gap: 8px;
      transition: background .2s ease, transform .2s ease;
      box-shadow: none;
    }
    .toolbar .btn-icon {
      padding: 10px;
      width: 40px;
      height: 40px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: #ffffff;
      border: 1px solid #e5e7eb;
      color: #374151;
    }
    .toolbar .btn-icon:hover { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    }
    .toolbar .btn-icon i { width: 16px; height: 16px; }
    .toolbar .btn:hover { 
      background: #0284c7;
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(2, 132, 199, 0.25);
    }
    .toolbar .btn i { width: 16px; height: 16px; }
    .toolbar .separator { 
      width: 1px; 
      height: 24px; 
      background: rgba(62, 62, 66, 0.5); 
      margin: 0 8px;
    }
    .toolbar .btn.primary { 
      background: #2563eb; color: #fff; border: none; box-shadow: none; 
    }
    .toolbar .btn.primary:hover { 
      background: #1d4ed8; transform: translateY(-1px);
    }
    .toolbar .btn.success { 
      background: #10b981; color: #fff; border: none; box-shadow: none; 
    }
    .toolbar .btn.success:hover { 
      background: #059669; transform: translateY(-1px);
    }
    
    /* Search and filters - Super Tecnológico */
    .search-filters { 
      display: flex; 
      gap: 12px; 
      margin-left: auto; 
      align-items: center;
    }
    .search-box { 
      position: relative;
    }
    .search-box input { 
      background: #ffffff;
      border: 1px solid #e5e7eb;
      color: #111827; 
      padding: 10px 12px 10px 36px; 
      border-radius: 10px; 
      font-size: 13px; 
      width: 220px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .search-box input:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
      background: #ffffff;
    }
    .search-box i { 
      position: absolute; 
      left: 12px; 
      top: 50%; 
      transform: translateY(-50%); 
      color: #9ca3af; 
      width: 14px; 
      height: 14px;
      transition: color 0.2s ease;
    }
    .search-box input:focus + i {
      color: #3b82f6;
    }
    .filter-select { 
      background: #ffffff; 
      border: 1px solid #e5e7eb; 
      color: #111827; 
      padding: 10px 12px; 
      border-radius: 10px; 
      font-size: 13px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
      cursor: pointer;
    }
    .filter-select:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
    }
    .filter-select:hover { background: #f8fafc; }
    
    /* Users Table */
    .users-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; }
    .users-table-container { 
      flex: 1; overflow: auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; 
    }
    .users-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 14px; }
    .users-table th { 
      background: #ffffff; 
      border-bottom: 1px solid #e5e7eb; 
      padding: 12px 14px; 
      text-align: left; 
      font-weight: 700; 
      color: #374151; 
      position: sticky; 
      top: 0; 
      z-index: 2;
    }
    .users-table td { 
      padding: 12px 14px; 
      border-bottom: 1px solid #f1f5f9; 
      vertical-align: middle; 
      color: #111827; 
    }
    .users-table tbody tr:nth-child(odd) td { background: #fafafa; }
    .users-table tbody tr:hover td { background: #eef2ff; }
    
    /* Role badges */
    .role-badge { 
      display: inline-flex; 
      padding: 6px 10px; 
      border-radius: 9999px; 
      font-size: 11px; 
      font-weight: 700; 
      letter-spacing: .4px;
    }
    .role-admin { background: #fee2e2; color: #991b1b; }
    .role-owner { background: #e9d5ff; color: #6b21a8; }
    .role-editor { background: #dbeafe; color: #1e3a8a; }
    .role-commenter { background: #fef3c7; color: #92400e; }
    .role-viewer { background: #e5e7eb; color: #374151; }
    
    /* Status badges */
    .status-badge { 
      display: inline-flex; 
      padding: 6px 10px; 
      border-radius: 9999px; 
      font-size: 11px; 
      font-weight: 700; 
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    
    /* Action buttons */
    .action-btn { 
      background: #ffffff; 
      border: 1px solid #e5e7eb; 
      color: #6b7280; 
      width: 36px; height: 36px;
      padding: 0; 
      border-radius: 10px; 
      cursor: pointer; 
      font-size: 13px; 
      margin-right: 6px;
      display: inline-flex; align-items: center; justify-content: center;
      transition: all .2s ease;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .action-btn:hover { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; transform: translateY(-1px); }
    .action-btn.edit { color: #2563eb; }
    .action-btn.delete { color: #ef4444; }
    .action-btn.toggle { color: #059669; }
    
    /* Modal */
    .modal { 
      display: none; 
      position: fixed; 
      z-index: 1000; 
      left: 0; 
      top: 0; 
      width: 100%; 
      height: 100%; 
      background: rgba(0,0,0,0.7); 
    }
    .modal-content { 
      background: #2d2d30; 
      border: 1px solid #3e3e42; 
      margin: 5% auto; 
      border-radius: 4px; 
      width: 90%; 
      max-width: 500px; 
      max-height: 90vh; 
      overflow-y: auto; 
    }
    .modal-header { 
      padding: 12px; 
      border-bottom: 1px solid #3e3e42; 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
    }
    .modal-header h3 { margin: 0; color: #cccccc; font-size: 14px; }
    .modal-body { padding: 12px; }
    .modal-footer { 
      padding: 12px; 
      border-top: 1px solid #3e3e42; 
      display: flex; 
      justify-content: flex-end; 
      gap: 8px; 
    }
    
    .form-group { margin-bottom: 12px; }
    .form-group label { 
      display: block; 
      margin-bottom: 4px; 
      font-weight: 600; 
      color: #cccccc; 
      font-size: 12px; 
    }
    .form-group input, .form-group select { 
      width: 100%; 
      padding: 6px 8px; 
      background: #37373d; 
      border: 1px solid #3e3e42; 
      color: #cccccc; 
      border-radius: 3px; 
      font-size: 13px; 
    }
    .form-group input:focus, .form-group select:focus { 
      outline: none; 
      border-color: #007acc; 
    }
    
    .close { 
      background: none; 
      border: none; 
      color: #858585; 
      font-size: 18px; 
      cursor: pointer; 
    }
    .close:hover { color: #cccccc; }
    
    /* Alert */
    .alert { 
      padding: 8px 12px; 
      margin-bottom: 12px; 
      border-radius: 3px; 
      font-size: 12px; 
    }
    .alert-success { background: #0f7b0f; color: white; }
    .alert-danger { background: #dc3545; color: white; }
    
    /* Loading and empty states */
    .loading, .empty-state { 
      text-align: center; 
      padding: 40px; 
      color: #858585; 
    }
    .loading i, .empty-state i { 
      font-size: 24px; 
      margin-bottom: 12px; 
      opacity: 0.5; 
    }
    
    /* Light theme overrides for new elements */
    body.light-theme .modal-content { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .modal-header h3 { color: #374151; }
    body.light-theme .form-group label { color: #374151; }
    body.light-theme .form-group input, body.light-theme .form-group select { 
      background: #ffffff; 
      border-color: #e5e7eb; 
      color: #111827; 
    }
    body.light-theme .users-table th { background: #f9fafb; color: #374151; border-bottom-color: #e5e7eb; }
    body.light-theme .users-table td { border-bottom-color: #e5e7eb; }
    body.light-theme .users-table tr:hover { background: #f3f4f6; }
    body.light-theme .action-btn { background: #f3f4f6; border-color: #e5e7eb; color: #374151; }
    body.light-theme .action-btn:hover { background: #e5e7eb; }
    body.light-theme .search-box input { background: #ffffff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .filter-select { background: #ffffff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .loading, body.light-theme .empty-state { color: #6b7280; }
    body.light-theme .close { color: #6b7280; }
    body.light-theme .close:hover { color: #374151; }

    /* Groups Management Styles */
    .members-container { margin-top: 8px; }
    .search-members { position: relative; margin-bottom: 12px; }
    .search-members input { width: 100%; padding: 8px; background: #1e1e1e; border: 1px solid #3e3e42; border-radius: 3px; color: #fff; font-size: 13px; }
    .user-suggestions { 
      position: absolute; 
      top: 100%; 
      left: 0; 
      right: 0; 
      background: #2d2d30; 
      border: 1px solid #3e3e42; 
      border-radius: 3px; 
      max-height: 200px; 
      overflow-y: auto; 
      z-index: 1000; 
      display: none; 
    }
    .user-suggestion { 
      padding: 8px 12px; 
      cursor: pointer; 
      font-size: 13px; 
      display: flex; 
      align-items: center; 
      gap: 8px; 
      border-bottom: 1px solid #3e3e42; 
    }
    .user-suggestion:hover { background: #3e3e42; }
    .user-suggestion:last-child { border-bottom: none; }
    
    .selected-members { 
      display: flex; 
      flex-wrap: wrap; 
      gap: 6px; 
      min-height: 40px; 
      padding: 8px; 
      background: #1e1e1e; 
      border: 1px solid #3e3e42; 
      border-radius: 3px; 
    }
    .member-tag { 
      background: #007acc; 
      color: #fff; 
      padding: 4px 8px; 
      border-radius: 12px; 
      font-size: 12px; 
      display: flex; 
      align-items: center; 
      gap: 4px; 
    }
    .member-tag .remove { 
      cursor: pointer; 
      font-weight: bold; 
      padding: 0 2px; 
      border-radius: 50%; 
    }
    .member-tag .remove:hover { 
      background: rgba(255,255,255,0.2); 
    }
    
    .members-management { display: flex; flex-direction: column; gap: 20px; }
    .add-member-section h4, .current-members-section h4 { 
      margin: 0 0 8px 0; 
      font-size: 14px; 
      color: #cccccc; 
    }
    .current-members { 
      display: flex; 
      flex-direction: column; 
      gap: 6px; 
      max-height: 300px; 
      overflow-y: auto; 
    }
    .current-member { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      padding: 8px 12px; 
      background: #2d2d30; 
      border: 1px solid #3e3e42; 
      border-radius: 3px; 
      font-size: 13px; 
    }
    .current-member .member-info { display: flex; align-items: center; gap: 8px; }
    .current-member .remove-member { 
      color: #e74c3c; 
      cursor: pointer; 
      padding: 4px; 
      border-radius: 3px; 
    }
    .current-member .remove-member:hover { background: rgba(231, 76, 60, 0.1); }

    /* Light theme overrides for groups */
    body.light-theme .search-members input { background: #ffffff; border-color: #e5e7eb; color: #111827; }
    body.light-theme .user-suggestions { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .user-suggestion { border-bottom-color: #e5e7eb; }
    body.light-theme .user-suggestion:hover { background: #f3f4f6; }
    body.light-theme .selected-members { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .current-member { background: #ffffff; border-color: #e5e7eb; }
    body.light-theme .add-member-section h4, body.light-theme .current-members-section h4 { color: #374151; }

    /* Storage Quota Styles */
    .quota-input-group { 
      display: flex; 
      gap: 8px; 
      align-items: center; 
    }
    .quota-input-group input { 
      flex: 1; 
      min-width: 80px; 
    }
    .quota-input-group select { 
      width: 70px; 
      padding: 8px; 
      background: #1e1e1e; 
      border: 1px solid #3e3e42; 
      border-radius: 3px; 
      color: #fff; 
      font-size: 13px; 
    }
    .usage-bar { 
      width: 100px; 
      height: 16px; 
      background: #2d2d30; 
      border-radius: 8px; 
      overflow: hidden; 
      position: relative; 
    }
    .usage-fill { 
      height: 100%; 
      transition: width 0.3s ease; 
      border-radius: 8px; 
    }
    .usage-normal { background: #28a745; }
    .usage-warning { background: #ffc107; }
    .usage-danger { background: #dc3545; }
    .usage-text { 
      font-size: 11px; 
      color: #cccccc; 
      margin-top: 2px; 
    }

    /* Light theme overrides for quota */
    body.light-theme .quota-input-group select { 
      background: #ffffff; 
      border-color: #e5e7eb; 
      color: #111827; 
    }
    body.light-theme .usage-bar { 
      background: #f3f4f6; 
    }
    body.light-theme .usage-text { 
      color: #6b7280; 
    }
  </style>
</head>
<body class="light-theme">
  <!-- Header -->
  <div class="header">
    <div class="logo">
      <i class="fas fa-users"></i>
      <span>GESTIÓN DE USUARIOS</span>
    </div>
    <div class="user">
      <i class="fas fa-user"></i>
      <span>Administrador</span>
    </div>
    <a href="/biblioteca/public/index.php/drive" class="btn success">
      <i class="fas fa-arrow-left"></i>
      <span>Volver al Drive</span>
    </a>
  </div>

  <!-- Main Layout -->
  <div class="main">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">Administración</div>
      <a href="#" class="tree-item active" onclick="showUsersSection()" id="users-tab">
        <i class="fas fa-users"></i>
        <span>Usuarios</span>
      </a>
      <a href="#" class="tree-item" onclick="showGroupsSection()" id="groups-tab">
        <i class="fas fa-user-friends"></i>
        <span>Grupos</span>
      </a>
      <a href="#" class="tree-item" onclick="showSystemInfo()">
        <i class="fas fa-info-circle"></i>
        <span>Sistema</span>
      </a>
      <a href="/biblioteca/public/index.php/drive" class="tree-item">
        <i class="fas fa-hdd"></i>
        <span>Volver al Drive</span>
      </a>
    </div>

    <!-- Content -->
    <div class="content">
      <!-- Toolbar -->
      <div class="toolbar">
        <button class="btn success" onclick="openCreateModal()">
          <i class="fas fa-plus"></i>
          <span>Nuevo Usuario</span>
        </button>
        
        <div class="search-filters">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar usuarios..." onkeyup="searchUsers()">
          </div>
          <select id="roleFilter" class="filter-select" onchange="filterUsers()">
            <option value="">Todos los roles</option>
            <?php foreach ($roles as $roleKey => $roleName): ?>
              <option value="<?= htmlspecialchars($roleKey) ?>"><?= htmlspecialchars($roleName) ?></option>
            <?php endforeach; ?>
          </select>
          <select id="statusFilter" class="filter-select" onchange="filterUsers()">
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
          </select>
          <button class="btn btn-icon" title="Actualizar" onclick="loadUsers()">
            <i class="fas fa-sync-alt"></i>
          </button>
        </div>
      </div>

      <!-- Users Container -->
      <div id="usersSection" class="users-container">
        <div id="alertContainer"></div>
        
        <div id="loading" class="loading">
          <i class="fas fa-spinner fa-spin"></i>
          <div>Cargando usuarios...</div>
        </div>
        
        <div class="users-table-container" id="usersTableContainer" style="display: none;">
          <table class="users-table" id="usersTable">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Departamento</th>
                <th>Cuota</th>
                <th>Uso</th>
                <th>Estado</th>
                <th>Último Acceso</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
            </tbody>
          </table>
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
          <i class="fas fa-users"></i>
          <h3>No hay usuarios</h3>
          <p>No se encontraron usuarios que coincidan con los criterios de búsqueda.</p>
        </div>
      </div>

      <!-- Grupos Section -->
      <div id="groupsSection" class="users-container" style="display: none;">
        <div class="toolbar">
          <button class="btn" onclick="openGroupModal()">
            <i class="fas fa-plus"></i>
            Nuevo Grupo
          </button>
          <div class="toolbar-spacer"></div>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="groupSearchInput" placeholder="Buscar grupos..." oninput="searchGroups()">
          </div>
        </div>
        
        <div id="groupsLoading" class="loading" style="display: none;">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Cargando grupos...</span>
        </div>
        
        <div class="groups-table-container" id="groupsTableContainer" style="display: none;">
          <table class="users-table" id="groupsTable">
            <thead>
              <tr>
                <th>Nombre del Grupo</th>
                <th>Descripción</th>
                <th>Miembros</th>
                <th>Creado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="groupsTableBody">
            </tbody>
          </table>
        </div>

        <div id="groupsEmptyState" class="empty-state" style="display: none;">
          <i class="fas fa-user-friends"></i>
          <h3>No hay grupos</h3>
          <p>No se encontraron grupos que coincidan con los criterios de búsqueda.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para crear/editar usuario -->
  <div id="userModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Nuevo Usuario</h3>
        <button class="close" onclick="closeModal()">&times;</button>
      </div>
      <form id="userForm">
        <input type="hidden" id="userId" name="user_id">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        
        <div class="modal-body">
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
          </div>
          
          <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required>
          </div>
          
          <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos">
          </div>
          
          <div class="form-group">
            <label for="rol">Rol *</label>
            <select id="rol" name="rol" required>
              <?php foreach ($roles as $roleKey => $roleName): ?>
                <option value="<?= htmlspecialchars($roleKey) ?>"><?= htmlspecialchars($roleName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="departamento">Departamento</label>
            <input type="text" id="departamento" name="departamento">
          </div>
          
          <div class="form-group">
            <label for="cuota_almacenamiento">Cuota de Almacenamiento</label>
            <div class="quota-input-group">
              <input type="number" id="cuota_almacenamiento" name="cuota_almacenamiento" min="1" step="1" placeholder="1" value="1">
              <select id="cuota_unit" name="cuota_unit">
                <option value="MB">MB</option>
                <option value="GB" selected>GB</option>
                <option value="TB">TB</option>
              </select>
            </div>
            <small>Espacio máximo de almacenamiento para el usuario</small>
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña <span id="passwordRequired">*</span></label>
            <input type="password" id="password" name="password">
            <small style="color: #858585; font-size: 11px;">Mínimo 6 caracteres. Deja vacío para mantener la actual al editar.</small>
          </div>
          
          <div class="form-group" id="statusGroup" style="display: none;">
            <label for="activo">Estado</label>
            <select id="activo" name="activo">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
          <button type="submit" class="btn success" id="submitBtn">Crear Usuario</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para crear/editar grupo -->
  <div id="groupModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="groupModalTitle">Nuevo Grupo</h3>
        <button class="close" onclick="closeGroupModal()">&times;</button>
      </div>
      <form id="groupForm">
        <input type="hidden" id="groupId" name="group_id">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        
        <div class="modal-body">
          <div class="form-group">
            <label for="groupName">Nombre del Grupo *</label>
            <input type="text" id="groupName" name="nombre" required>
          </div>
          
          <div class="form-group">
            <label for="groupDescription">Descripción</label>
            <textarea id="groupDescription" name="descripcion" rows="3" placeholder="Descripción opcional del grupo"></textarea>
          </div>
          
          <div class="form-group">
            <label>Miembros del Grupo</label>
            <div class="members-container">
              <div class="search-members">
                <input type="text" id="memberSearch" placeholder="Buscar usuarios para agregar..." oninput="searchUsersForGroup()">
                <div id="userSuggestions" class="user-suggestions"></div>
              </div>
              <div id="selectedMembers" class="selected-members">
                <!-- Los miembros seleccionados aparecerán aquí -->
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn" onclick="closeGroupModal()">Cancelar</button>
          <button type="submit" class="btn success" id="groupSubmitBtn">Crear Grupo</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para gestionar miembros del grupo -->
  <div id="membersModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="membersModalTitle">Miembros del Grupo</h3>
        <button class="close" onclick="closeMembersModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="members-management">
          <div class="add-member-section">
            <h4>Agregar Miembros</h4>
            <div class="search-members">
              <input type="text" id="memberSearchModal" placeholder="Buscar usuarios..." oninput="searchUsersForModal()">
              <div id="userSuggestionsModal" class="user-suggestions"></div>
            </div>
          </div>
          
          <div class="current-members-section">
            <h4>Miembros Actuales</h4>
            <div id="currentMembers" class="current-members">
              <!-- Los miembros actuales aparecerán aquí -->
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="closeMembersModal()">Cerrar</button>
      </div>
    </div>
  </div>

  <script>
    let users = [];
    let filteredUsers = [];
    let isEditing = false;

    // Cargar usuarios al inicializar
    document.addEventListener('DOMContentLoaded', function() {
      loadUsers();
    });

    async function loadUsers() {
      showLoading(true);
      try {
        const response = await fetch('/biblioteca/public/index.php/admin/users/api', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          }
        });
        
        const data = await response.json();
        
        if (data.users) {
          users = data.users;
          filteredUsers = [...users];
          renderUsers();
        } else {
          showAlert('Error al cargar usuarios', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión al cargar usuarios', 'danger');
      } finally {
        showLoading(false);
      }
    }

    function renderUsers() {
      const tbody = document.getElementById('usersTableBody');
      const container = document.getElementById('usersTableContainer');
      const emptyState = document.getElementById('emptyState');
      
      if (filteredUsers.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
      }
      
      container.style.display = 'block';
      emptyState.style.display = 'none';
      
      tbody.innerHTML = filteredUsers.map(user => `
        <tr>
          <td>
            <div style="font-weight: 600;">${escapeHtml(user.nombre)} ${escapeHtml(user.apellidos || '')}</div>
          </td>
          <td>${escapeHtml(user.email)}</td>
          <td>
            <span class="role-badge role-${user.rol}">${escapeHtml(user.rol_nombre)}</span>
          </td>
          <td>${escapeHtml(user.departamento || '-')}</td>
          <td>${formatBytes(user.cuota_almacenamiento || 1073741824)}</td>
          <td>
            <div class="usage-container">
              <div class="usage-bar">
                <div class="usage-fill ${getUsageClass(user)}" style="width: ${getUsagePercentage(user)}%"></div>
              </div>
              <div class="usage-text">${formatBytes(user.almacenamiento_usado || 0)} / ${formatBytes(user.cuota_almacenamiento || 1073741824)}</div>
            </div>
          </td>
          <td>
            <span class="status-badge status-${user.activo ? 'active' : 'inactive'}">
              ${user.activo ? 'Activo' : 'Inactivo'}
            </span>
          </td>
          <td>${user.fecha_ultimo_acceso ? formatDate(user.fecha_ultimo_acceso) : 'Nunca'}</td>
          <td>
            <button class="action-btn edit" onclick="editUser(${user.id})" title="Editar">
              <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn toggle" onclick="toggleUserStatus(${user.id})" title="${user.activo ? 'Desactivar' : 'Activar'}">
              <i class="fas fa-${user.activo ? 'pause' : 'play'}"></i>
            </button>
            <button class="action-btn delete" onclick="deleteUser(${user.id})" title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `).join('');
    }

    function searchUsers() {
      filterUsers();
    }

    function filterUsers() {
      const search = document.getElementById('searchInput').value.toLowerCase();
      const roleFilter = document.getElementById('roleFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      filteredUsers = users.filter(user => {
        const matchesSearch = !search || 
          user.nombre.toLowerCase().includes(search) ||
          (user.apellidos && user.apellidos.toLowerCase().includes(search)) ||
          user.email.toLowerCase().includes(search);
        
        const matchesRole = !roleFilter || user.rol === roleFilter;
        const matchesStatus = statusFilter === '' || user.activo.toString() === statusFilter;
        
        return matchesSearch && matchesRole && matchesStatus;
      });
      
      renderUsers();
    }

    function openCreateModal() {
      isEditing = false;
      document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
      document.getElementById('submitBtn').textContent = 'Crear Usuario';
      document.getElementById('userForm').reset();
      document.getElementById('userId').value = '';
      document.getElementById('statusGroup').style.display = 'none';
      document.getElementById('passwordRequired').style.display = 'inline';
      document.getElementById('password').required = true;
      document.getElementById('userModal').style.display = 'block';
    }

    function editUser(userId) {
      const user = users.find(u => u.id === userId);
      if (!user) return;
      
      isEditing = true;
      document.getElementById('modalTitle').textContent = 'Editar Usuario';
      document.getElementById('submitBtn').textContent = 'Actualizar Usuario';
      
      document.getElementById('userId').value = user.id;
      document.getElementById('email').value = user.email;
      document.getElementById('nombre').value = user.nombre;
      document.getElementById('apellidos').value = user.apellidos || '';
      document.getElementById('rol').value = user.rol;
      document.getElementById('departamento').value = user.departamento || '';
      document.getElementById('activo').value = user.activo;
      document.getElementById('password').value = '';
      
      // Configurar cuota de almacenamiento
      const quotaInfo = convertBytesToUnit(user.cuota_almacenamiento || 1073741824);
      document.getElementById('cuota_almacenamiento').value = quotaInfo.value;
      document.getElementById('cuota_unit').value = quotaInfo.unit;
      
      document.getElementById('statusGroup').style.display = 'block';
      document.getElementById('passwordRequired').style.display = 'none';
      document.getElementById('password').required = false;
      
      document.getElementById('userModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('userModal').style.display = 'none';
      document.getElementById('userForm').reset();
    }

    // Manejar envío del formulario
    document.getElementById('userForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      // Debug: Log de los datos que se envían
      console.log('DEBUG - Datos del formulario:');
      console.log('cuota_almacenamiento:', formData.get('cuota_almacenamiento'));
      console.log('cuota_unit:', formData.get('cuota_unit'));
      console.log('isEditing:', isEditing);
      
      const action = isEditing ? 'update' : 'create';
      const url = `/biblioteca/public/index.php/admin/users/${action}`;
      
      try {
        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          showAlert(data.message, 'success');
          closeModal();
          loadUsers();
        } else {
          showAlert(data.error || 'Error al procesar la solicitud', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      }
    });

    async function toggleUserStatus(userId) {
      if (!confirm('¿Estás seguro de cambiar el estado de este usuario?')) return;
      
      const formData = new FormData();
      formData.append('user_id', userId);
      formData.append('_csrf', '<?= htmlspecialchars($csrf) ?>');
      
      try {
        const response = await fetch('/biblioteca/public/index.php/admin/users/toggle-status', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          showAlert(data.message, 'success');
          loadUsers();
        } else {
          showAlert(data.error || 'Error al cambiar el estado', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      }
    }

    async function deleteUser(userId) {
      if (!confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) return;
      
      const formData = new FormData();
      formData.append('user_id', userId);
      formData.append('_csrf', '<?= htmlspecialchars($csrf) ?>');
      
      try {
        const response = await fetch('/biblioteca/public/index.php/admin/users/delete', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          showAlert(data.message, 'success');
          loadUsers();
        } else {
          showAlert(data.error || 'Error al eliminar el usuario', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      }
    }

    function showSystemInfo() {
      openSystemModal('Versión: 1.0.0\nPHP: <?= PHP_VERSION ?>\nSistema de Gestión de Usuarios activo', 'info', 'Información del Sistema');
    }

    function showAlert(message, type) {
      const container = document.getElementById('alertContainer');
      const alert = document.createElement('div');
      alert.className = `alert alert-${type}`;
      alert.textContent = message;
      
      container.innerHTML = '';
      container.appendChild(alert);
      
      setTimeout(() => {
        alert.remove();
      }, 5000);
    }

    function showLoading(show) {
      document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }

    // Formatear bytes a unidades legibles
    function formatBytes(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Calcular porcentaje de uso
    function getUsagePercentage(user) {
      const quota = user.cuota_almacenamiento || 1073741824;
      const used = user.almacenamiento_usado || 0;
      return Math.min(100, Math.round((used / quota) * 100));
    }

    // Obtener clase CSS según el uso
    function getUsageClass(user) {
      const percentage = getUsagePercentage(user);
      if (percentage >= 95) return 'usage-danger';
      if (percentage >= 80) return 'usage-warning';
      return 'usage-normal';
    }

    // Convertir cuota de bytes a unidad legible para edición
    function convertBytesToUnit(bytes) {
      if (bytes >= 1099511627776) { // 1TB
        return { value: (bytes / 1099511627776).toFixed(2), unit: 'TB' };
      } else if (bytes >= 1073741824) { // 1GB
        return { value: (bytes / 1073741824).toFixed(2), unit: 'GB' };
      } else { // MB
        return { value: (bytes / 1048576).toFixed(2), unit: 'MB' };
      }
    }

    // Convertir unidad a bytes
    function convertUnitToBytes(value, unit) {
      const multipliers = {
        'MB': 1048576,
        'GB': 1073741824,
        'TB': 1099511627776
      };
      return Math.round(parseFloat(value) * (multipliers[unit] || 1073741824));
    }

    // ===== FUNCIONES DE GRUPOS =====
    let groups = [];
    let filteredGroups = [];
    let isEditingGroup = false;
    let selectedMembers = new Set();
    let currentGroupId = null;

    // Mostrar sección de grupos
    function showGroupsSection() {
      // Ocultar sección de usuarios
      document.getElementById('usersSection').style.display = 'none';
      document.getElementById('groupsSection').style.display = 'block';
      
      // Actualizar navegación
      document.getElementById('users-tab').classList.remove('active');
      document.getElementById('groups-tab').classList.add('active');
      
      // Cargar grupos
      loadGroups();
    }

    // Mostrar sección de usuarios
    function showUsersSection() {
      // Ocultar sección de grupos
      document.getElementById('groupsSection').style.display = 'none';
      document.getElementById('usersSection').style.display = 'block';
      
      // Actualizar navegación
      document.getElementById('groups-tab').classList.remove('active');
      document.getElementById('users-tab').classList.add('active');
      
      // Cargar usuarios
      loadUsers();
    }

    // Cargar grupos
    async function loadGroups() {
      document.getElementById('groupsLoading').style.display = 'flex';
      document.getElementById('groupsTableContainer').style.display = 'none';
      document.getElementById('groupsEmptyState').style.display = 'none';
      
      try {
        const response = await fetch('/biblioteca/public/index.php/admin/groups/api', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          }
        });
        
        if (!response.ok) {
          throw new Error('Error al cargar grupos');
        }
        
        const data = await response.json();
        groups = data.groups || [];
        filteredGroups = [...groups];
        renderGroups();
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar grupos', 'danger');
      } finally {
        document.getElementById('groupsLoading').style.display = 'none';
      }
    }

    // Renderizar grupos
    function renderGroups() {
      const tbody = document.getElementById('groupsTableBody');
      tbody.innerHTML = '';
      
      if (filteredGroups.length === 0) {
        document.getElementById('groupsTableContainer').style.display = 'none';
        document.getElementById('groupsEmptyState').style.display = 'flex';
        return;
      }
      
      document.getElementById('groupsTableContainer').style.display = 'block';
      document.getElementById('groupsEmptyState').style.display = 'none';
      
      filteredGroups.forEach(group => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-user-friends" style="color: #007acc;"></i>
              <span style="font-weight: 500;">${group.nombre}</span>
            </div>
          </td>
          <td>${group.descripcion || '<em>Sin descripción</em>'}</td>
          <td>
            <span class="role-badge" style="background: #28a745;">${group.miembros_count || 0} miembros</span>
          </td>
          <td>${formatDate(group.fecha_creacion)}</td>
          <td>
            <button class="action-btn" onclick="editGroup(${group.id})" title="Editar grupo">
              <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn" onclick="manageMembers(${group.id})" title="Gestionar miembros">
              <i class="fas fa-users"></i>
            </button>
            <button class="action-btn" onclick="deleteGroup(${group.id})" title="Eliminar grupo">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // Abrir modal de grupo
    function openGroupModal() {
      isEditingGroup = false;
      selectedMembers.clear();
      document.getElementById('groupModalTitle').textContent = 'Nuevo Grupo';
      document.getElementById('groupSubmitBtn').textContent = 'Crear Grupo';
      document.getElementById('groupForm').reset();
      document.getElementById('groupId').value = '';
      updateSelectedMembersUI();
      document.getElementById('groupModal').style.display = 'flex';
    }

    // Cerrar modal de grupo
    function closeGroupModal() {
      document.getElementById('groupModal').style.display = 'none';
      selectedMembers.clear();
    }

    // Buscar usuarios para agregar al grupo
    async function searchUsersForGroup() {
      const query = document.getElementById('memberSearch').value.trim();
      const suggestions = document.getElementById('userSuggestions');
      
      if (query.length < 2) {
        suggestions.style.display = 'none';
        return;
      }
      
      try {
        const response = await fetch(`/biblioteca/public/index.php/admin/users/search?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.users && data.users.length > 0) {
          suggestions.innerHTML = '';
          data.users.forEach(user => {
            if (!selectedMembers.has(user.id)) {
              const suggestion = document.createElement('div');
              suggestion.className = 'user-suggestion';
              suggestion.innerHTML = `
                <i class="fas fa-user"></i>
                <span>${user.nombre} (${user.email})</span>
              `;
              suggestion.onclick = () => addMember(user);
              suggestions.appendChild(suggestion);
            }
          });
          suggestions.style.display = 'block';
        } else {
          suggestions.style.display = 'none';
        }
      } catch (error) {
        console.error('Error searching users:', error);
      }
    }

    // Agregar miembro
    function addMember(user) {
      selectedMembers.add(user.id);
      updateSelectedMembersUI();
      document.getElementById('memberSearch').value = '';
      document.getElementById('userSuggestions').style.display = 'none';
    }

    // Remover miembro
    function removeMember(userId) {
      selectedMembers.delete(userId);
      updateSelectedMembersUI();
    }

    // Actualizar UI de miembros seleccionados
    function updateSelectedMembersUI() {
      const container = document.getElementById('selectedMembers');
      container.innerHTML = '';
      
      selectedMembers.forEach(userId => {
        const user = users.find(u => u.id === userId);
        if (user) {
          const tag = document.createElement('div');
          tag.className = 'member-tag';
          tag.innerHTML = `
            <span>${user.nombre}</span>
            <span class="remove" onclick="removeMember(${userId})">&times;</span>
          `;
          container.appendChild(tag);
        }
      });
    }

    // Enviar formulario de grupo
    document.getElementById('groupForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('members', JSON.stringify(Array.from(selectedMembers)));
      
      const url = isEditingGroup ? '/biblioteca/public/index.php/admin/groups/update' : '/biblioteca/public/index.php/admin/groups/create';
      
      try {
        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          closeGroupModal();
          loadGroups();
          showAlert(isEditingGroup ? 'Grupo actualizado exitosamente' : 'Grupo creado exitosamente', 'success');
        } else {
          showAlert(data.error || 'Error al procesar la solicitud', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      }
    });

    // Buscar grupos
    function searchGroups() {
      const query = document.getElementById('groupSearchInput').value.toLowerCase().trim();
      
      if (query === '') {
        filteredGroups = [...groups];
      } else {
        filteredGroups = groups.filter(group => 
          group.nombre.toLowerCase().includes(query) ||
          (group.descripcion && group.descripcion.toLowerCase().includes(query))
        );
      }
      
      renderGroups();
    }

    // Editar grupo
    async function editGroup(groupId) {
      // Implementar edición de grupo
      showAlert('Función de editar grupo en desarrollo', 'info');
    }

    // Gestionar miembros
    async function manageMembers(groupId) {
      // Implementar gestión de miembros
      showAlert('Función de gestión de miembros en desarrollo', 'info');
    }

    // Eliminar grupo
    async function deleteGroup(groupId) {
      if (!confirm('¿Estás seguro de que deseas eliminar este grupo?')) {
        return;
      }
      
      try {
        const formData = new FormData();
        formData.append('group_id', groupId);
        formData.append('_csrf', document.querySelector('input[name="_csrf"]').value);
        
        const response = await fetch('/biblioteca/public/index.php/admin/groups/delete', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          loadGroups();
          showAlert('Grupo eliminado exitosamente', 'success');
        } else {
          showAlert(data.error || 'Error al eliminar el grupo', 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
      }
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
      const userModal = document.getElementById('userModal');
      const groupModal = document.getElementById('groupModal');
      const membersModal = document.getElementById('membersModal');
      
      if (event.target === userModal) {
        closeModal();
      } else if (event.target === groupModal) {
        closeGroupModal();
      } else if (event.target === membersModal) {
        closeMembersModal();
      }
    }

    // Cerrar modal de miembros
    function closeMembersModal() {
      document.getElementById('membersModal').style.display = 'none';
    }
  </script>
</body>
</html>