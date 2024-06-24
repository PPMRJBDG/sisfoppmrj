<div class="card shadow border p-2">
  <div class="d-flex justify-content-end gap-4 align-items-center">
    @if($page > 1)
    <button class="btn btn-primary btn-sm" onclick="previousPage()">
      <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </button>
    @endif
    <button class="btn btn-secondary btn-sm">
      {{ $page }}
    </button>
    <button class="btn btn-primary btn-sm" onclick="nextPage()">
      <i class="fa fa-arrow-right" aria-hidden="true"></i>
    </button>
  </div>
</div>

<script>
  function nextPage() {
    getPage($("#current-url").val() + `?page={{ $page + 1 }}`)
  }

  function previousPage() {
    getPage($("#current-url").val() + `?page={{ $page - 1 }}`)
  }
</script>