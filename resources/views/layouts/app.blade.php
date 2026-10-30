<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SICIV - Sistema de Control de Inventario y Ventas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: {
            primary:'#1F3A5F','primary-dark':'#162A45',accent:'#F28C28','accent-hover':'#e07b1a','bg-main':'#F4F6F9',
            'status-out':'#EF4444','status-low':'#F59E0B','status-ok':'#10B981'
        }, fontFamily:{ sans:['Inter','sans-serif'] } } } }
    </script>
    <style>
        body{font-family:'Inter',sans-serif;background-color:#F4F6F9}
        .sidebar-active{background-color:rgba(242,140,40,.15);border-left:4px solid #F28C28;color:#fff!important}
        .table-striped tbody tr:nth-child(even){background-color:#F8FAFC}.table-striped tbody tr:hover{background-color:#F1F5F9}
        ::-webkit-scrollbar{width:6px;height:6px}::-webkit-scrollbar-track{background:#f1f1f1}::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}::-webkit-scrollbar-thumb:hover{background:#94a3b8}
    </style>
</head>
<body class="text-slate-800 antialiased h-screen flex flex-col overflow-hidden">
<div class="h-full w-full flex flex-col">
<header class="bg-primary text-white h-16 flex items-center justify-between px-6 z-20 shadow-md">
    <div class="flex items-center space-x-3">
        <i class="fa-solid fa-screwdriver-wrench text-accent text-2xl"></i>
        <span class="font-bold text-lg tracking-wide">SICIV <span class="text-xs font-normal opacity-80 border-l border-slate-500 pl-2 ml-1">Ferretería El Salvador</span></span>
    </div>
    <div class="flex items-center space-x-4">
        <div class="text-right">
            <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Usuario SICIV' }}</p>
            <p class="text-xs text-accent capitalize font-medium">{{ strtolower(auth()->user()->rol ?? 'desarrollo') }}</p>
        </div>
        <div class="h-8 w-px bg-slate-600"></div>
        <span class="text-slate-300 text-sm flex items-center space-x-1"><i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span></span>
    </div>
</header>
<div class="flex flex-1 overflow-hidden">
<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800 overflow-y-auto">
    <div class="p-4 border-b border-slate-800/60 text-xs text-slate-400 font-medium">ROL ACTIVO: <span class="uppercase text-slate-200 font-bold">{{ auth()->user()->rol ?? 'DESARROLLO' }}</span></div>
    <nav class="flex-1 py-3 px-2 space-y-1">
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-house w-5 text-center text-slate-400"></i><span>Inicio</span></a>
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-500">CATÃLOGO</div>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-tags w-5 text-center text-slate-400"></i><span>Categorías</span></a>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-boxes-stacked w-5 text-center text-slate-400"></i><span>Productos</span></a>
        <a href="{{ route('proveedores.index') }}" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('proveedores.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white' }}"><i class="fa-solid fa-truck-field w-5 text-center {{ request()->routeIs('proveedores.*')?'text-accent':'text-slate-400' }}"></i><span>Proveedores</span></a>
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-500">INVENTARIO</div>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-warehouse w-5 text-center text-slate-400"></i><span>Existencias</span></a>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-triangle-exclamation w-5 text-center text-slate-400"></i><span>Alertas de Stock</span></a>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-clock-rotate-left w-5 text-center text-slate-400"></i><span>Movimientos</span></a>
        <a href="{{ route('entradas.create') }}" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('entradas.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white' }}"><i class="fa-solid fa-file-circle-plus w-5 text-center {{ request()->routeIs('entradas.*')?'text-accent':'text-slate-400' }}"></i><span>Registrar Entrada</span></a>
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-500">VENTAS</div>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-cart-shopping w-5 text-center text-slate-400"></i><span>Registrar Venta</span></a>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white"><i class="fa-solid fa-receipt w-5 text-center text-slate-400"></i><span>Consultar Ventas</span></a>
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-500">REPORTES</div>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white">
            <i class="fa-solid fa-chart-line w-5 text-center text-blue-300"></i>
            <span>Mayor Movimiento</span>
        </a>
        <a href="#" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 hover:text-white">
            <i class="fa-solid fa-chart-pie w-5 text-center text-blue-300"></i>
            <span>Resumen Ventas</span>
        </a>    </nav>
    <div class="p-3 text-xs text-slate-500 text-center border-t border-slate-800">v2.4.0 â€¢ 1440x900 Responsive</div>
</aside>
<main class="flex-1 overflow-y-auto bg-bg-main p-8 relative">
@if(session('ok'))<div class="fixed top-20 right-8 z-50 flex items-center p-4 rounded-lg shadow-lg text-white bg-emerald-600"><i class="fa-solid fa-circle-check text-xl mr-3"></i><span class="text-sm font-medium">{{ session('ok') }}</span></div>@endif
@if($errors->any())<div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-xs"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $errors->first() }}</div>@endif
@yield('content')
</main></div></div>
@stack('modals') @stack('scripts')
</body></html>
