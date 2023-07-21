<div class="d-flex justify-content-end gap-4 align-items-center">
  @if($page > 1)
    <button class="btn btn-primary mb-0" onclick="previousPage()">
    <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </button>
  @endif
  <div>
    {{ $page }}
  </div>
  <button class="btn btn-primary mb-0" onclick="nextPage()">
      <i class="fa fa-arrow-right" aria-hidden="true"></i>
  </button>
</div>
<script>
    function nextPage()
    {
      location.replace(`${location.origin}${location.pathname}?page={{ $page + 1 }}`)
    }

    function previousPage()
    {
      location.replace(`${location.origin}${location.pathname}?page={{ $page - 1 }}`)
    }
</script>