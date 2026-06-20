<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('#example');
  if (table && typeof DataTable !== 'undefined') {
    new DataTable('#example', {
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      pageLength: 25,
      order: [[0, 'desc']],
      responsive: true
    });
  }
});

function estadoSolicitudHtml(stat) {
  const s = parseInt(stat, 10);
  const map = {
    1: ['Pendiente', 'badge-pendiente'],
    2: ['Aprobado', 'badge-aprobado'],
    3: ['Eliminado', 'badge-eliminado'],
    4: ['En revisión supervisor', 'badge-supervisor']
  };
  const item = map[s] || ['Desconocido', 'badge-desconocido'];
  return '<span class="pcr-badge ' + item[1] + '">' + item[0] + '</span>';
}
</script>
