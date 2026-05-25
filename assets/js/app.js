function openModal(id){document.getElementById(id).classList.add('active');}
function closeModal(id){document.getElementById(id).classList.remove('active');}
document.addEventListener('click',e=>{if(e.target.classList.contains('modal-overlay'))e.target.classList.remove('active');});

// Countdown timer untuk peminjaman aktif
function initCountdowns(){
  document.querySelectorAll('[data-deadline]').forEach(el=>{
    const deadline=new Date(el.dataset.deadline).getTime();
    function tick(){
      const now=Date.now();
      const diff=deadline-now;
      if(diff<=0){el.textContent='⌛ Waktu habis!';el.classList.add('expired');return;}
      const d=Math.floor(diff/86400000);
      const h=Math.floor((diff%86400000)/3600000);
      const m=Math.floor((diff%3600000)/60000);
      const s=Math.floor((diff%60000)/1000);
      el.textContent=`⏱️ ${d}h ${h}j ${m}m ${s}d`;
    }
    tick();setInterval(tick,1000);
  });
}
document.addEventListener('DOMContentLoaded',initCountdowns);

// Hitung kembalian otomatis
function calcChange(formId){
  const f=document.getElementById(formId);
  const paid=parseFloat(f.money_paid.value)||0;
  const total=parseFloat(f.dataset.total)||0;
  f.money_change.value=Math.max(0,paid-total);
}
