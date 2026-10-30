@extends('layouts.app')
@section('title','Registrar Entrada de Inventario - SICIV')
@section('content')
<div class="space-y-6">
 <div><h1 class="text-xl font-bold text-slate-900">Registrar Entrada de Inventario</h1><p class="text-xs text-slate-500">Recepción de mercadería de proveedores</p></div>
 <form method="POST" action="{{ route('entradas.store') }}" id="entry-form">@csrf
  <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-3 gap-4">
   <div><label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Proveedor Activo</label><select name="id_proveedor" class="w-full p-2.5 border rounded-lg text-xs outline-none focus:border-primary" required><option value="">-- Seleccione proveedor --</option>@foreach($proveedores as $s)<option value="{{ $s->id_proveedor }}">{{ $s->nombre }}</option>@endforeach</select></div>
   <div><label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fecha de Recepción</label><input type="date" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" class="w-full p-2.5 border rounded-lg text-xs outline-none"></div>
   <div><label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Documento de Referencia</label><input name="referencia" id="entry-doc" placeholder="Ej: FAC-99082" class="w-full p-2.5 border rounded-lg text-xs outline-none font-mono"></div>
  </div>
  <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
   <h3 class="text-xs font-bold uppercase text-slate-600">Agregar Producto a la Entrada</h3>
   <div id="entry-error" class="hidden bg-red-50 border border-red-200 text-red-700 p-2 rounded text-xs"></div>
   <div class="grid grid-cols-12 gap-3 items-end">
    <div class="col-span-5"><label class="block text-[11px] text-slate-500 mb-1">Producto</label><select id="entry-prod-select" class="w-full p-2 border rounded-lg text-xs outline-none">@if($productos->isEmpty())<option value="">No hay productos activos disponibles</option>@else<option value="">-- Elige un producto --</option>@endif @foreach($productos as $p)<option value="{{ $p->id_producto }}" data-code="{{ $p->codigo }}" data-name="{{ $p->nombre }}">{{ $p->codigo }} - {{ $p->nombre }}</option>@endforeach</select></div>
    <div class="col-span-2"><label class="block text-[11px] text-slate-500 mb-1">Cantidad</label><input type="number" id="entry-qty" min="0.01" step="0.01" value="10" class="w-full p-2 border rounded-lg text-xs outline-none"></div>
    <div class="col-span-3"><label class="block text-[11px] text-slate-500 mb-1">Costo Unitario ($)</label><input type="number" step="0.01" id="entry-cost" min="0.01" value="5.00" class="w-full p-2 border rounded-lg text-xs outline-none"></div>
    <div class="col-span-2"><button type="button" id="add-entry-line" @disabled($productos->isEmpty()) class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold disabled:opacity-50 disabled:cursor-not-allowed">+ Agregar Línea</button></div>
   </div>
  </div>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-5 space-y-4">
   <table class="w-full text-left border-collapse"><thead><tr class="bg-slate-100 text-slate-600 text-xs uppercase border-b"><th class="p-3">Código</th><th class="p-3">Producto</th><th class="p-3 text-center">Cantidad</th><th class="p-3 text-right">Costo U.</th><th class="p-3 text-right">Subtotal</th><th class="p-3 text-center">Acción</th></tr></thead><tbody id="entry-lines" class="divide-y divide-slate-200 text-xs text-slate-700"><tr><td colspan="6" class="p-6 text-center text-slate-400">Sin ítems agregados a esta entrada.</td></tr></tbody></table>
   <div class="flex justify-between items-center pt-4 border-t"><span class="text-sm font-bold text-slate-700">TOTAL ENTRADA: $<span id="entry-total">0.00</span></span><button type="submit" id="confirm-entry" disabled class="px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-xs font-bold disabled:opacity-50">Confirmar Entrada</button></div>
  </div>
 </form>
</div>
@endsection
@push('scripts')
<script>
(function(){
 const cart=[];
 const select=document.getElementById('entry-prod-select');
 const qty=document.getElementById('entry-qty');
 const cost=document.getElementById('entry-cost');
 const error=document.getElementById('entry-error');
 const body=document.getElementById('entry-lines');
 const total=document.getElementById('entry-total');
 const confirm=document.getElementById('confirm-entry');
 function showError(message){error.textContent=message;error.classList.remove('hidden')}
 function clearError(){error.classList.add('hidden');error.textContent=''}
 function render(){
   if(!cart.length){body.innerHTML='<tr><td colspan="6" class="p-6 text-center text-slate-400">Sin ítems agregados a esta entrada.</td></tr>';confirm.disabled=true;total.textContent='0.00';return}
   body.innerHTML=cart.map((item,index)=>`<tr><td class="p-3 font-mono">${item.code}<input type="hidden" name="productos[${index}][id_producto]" value="${item.id}"></td><td class="p-3 font-semibold">${item.name}</td><td class="p-3 text-center">${item.qty}<input type="hidden" name="productos[${index}][cantidad]" value="${item.qty}"></td><td class="p-3 text-right">$${item.cost.toFixed(2)}<input type="hidden" name="productos[${index}][costo_unitario]" value="${item.cost}"></td><td class="p-3 text-right font-bold">$${item.subtotal.toFixed(2)}</td><td class="p-3 text-center"><button type="button" data-remove="${index}" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash-can"></i></button></td></tr>`).join('');
   total.textContent=cart.reduce((sum,item)=>sum+item.subtotal,0).toFixed(2);confirm.disabled=false;
 }
 document.getElementById('add-entry-line').addEventListener('click',function(){
   clearError();const id=select.value;const q=parseFloat(qty.value);const c=parseFloat(cost.value);
   if(!id){showError('Selecciona un producto.');return} if(!(q>0)||!(c>0)){showError('Error: La cantidad y el costo unitario deben ser mayores a cero.');return}
   if(cart.some(item=>item.id===id)){showError('El producto ya fue agregado a la entrada.');return}
   const option=select.options[select.selectedIndex];cart.push({id,code:option.dataset.code,name:option.dataset.name,qty:q,cost:c,subtotal:q*c});render();
 });
 body.addEventListener('click',function(e){const button=e.target.closest('[data-remove]');if(!button)return;cart.splice(Number(button.dataset.remove),1);render()});
 document.getElementById('entry-form').addEventListener('submit',function(e){if(!cart.length){e.preventDefault();showError('Agrega al menos un producto antes de confirmar la entrada.')}});
 render();
})();
</script>
@endpush
@if(session('entrada_confirmada'))
<div id="entry-success-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-primary px-8 py-6 text-white">
            <h2 class="text-lg font-bold uppercase tracking-wide">ENTRADA REGISTRADA</h2>
            <button type="button" onclick="closeEntrySuccessModal()" class="text-slate-300 hover:text-white" aria-label="Cerrar">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>
        <div class="px-8 py-12 text-center">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <i class="fa-solid fa-dolly text-4xl"></i>
            </div>
            <p class="text-xl font-bold text-slate-800">Entrada procesada con Correlativo: <span class="font-mono text-primary">{{ session('entrada_confirmada') }}</span></p>
            <p class="mt-5 text-base text-slate-600">El stock de los productos ha sido incrementado debidamente en el sistema.</p>
            <button type="button" onclick="closeEntrySuccessModal()" class="mt-6 w-full rounded-lg bg-primary py-3 text-sm font-bold text-white hover:bg-primary-dark">Aceptar</button>
        </div>
    </div>
</div>
<script>
function closeEntrySuccessModal() {
    document.getElementById('entry-success-modal').classList.add('hidden');
}
</script>
@endif